<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$agency_temp = explode(":", $_POST["agency"]);
$agency = $agency_temp[0];
$report_name = $agency_temp[1];
$scan_start = $agency_temp[2];
$scan_end = $agency_temp[3];
$agency_sql = 	"SELECT DISTINCT 
					nessus_results.agency, 
					nessus_results.report_name, 
					nessus_results.scan_start, 
					nessus_results.scan_end 
				FROM 
					nessus_results
				";
$agency_stmt = $db->prepare($agency_sql);
$agency_stmt->execute();
if($agency != ""){
	$host_sql = "SELECT DISTINCT
					nessus_tags.host_name,
					nessus_tags.ip_addr,
					nessus_tags.fqdn,
					nessus_tags.netbios
				FROM
					nessus_results
				INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
				WHERE 
					nessus_results.agency = ? AND
					nessus_results.report_name = ? AND
					nessus_results.scan_start = ? AND
					nessus_results.scan_end = ?
				ORDER BY 
					nessus_tags.host_name
				";
	$host_data = array($agency, $report_name, $scan_start, $scan_end);
	$host_stmt = $db->prepare($host_sql);
	$host_stmt->execute($host_data);
}//end if

?>

<HTML>
<head>
<title>CREATE NESSUS LOW 2 PWN REPORT</title>
<script>
function selectAll(selectBox,selectAll) {
    // have we been passed an ID
    if (typeof selectBox == "string") {
        selectBox = document.getElementById(selectBox);
    }

    // is the select box a multiple select box?
    if (selectBox.type == "select-multiple") {
        for (var i = 0; i < selectBox.options.length; i++) {
            selectBox.options[i].selected = selectAll;
        }
    }
}
</script>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
select {font-family: courier new}
</style>
</head>
<BODY>
<table width="100%"><tr><td width="200px" valign="top"><?php include '../main/menu.php'; ?></td>
<td valign="top">
<table style="text-align: left; width: 950px;" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2">
	  <form name="f1"  action="" method="post">
	  <p align="center">[ Nessus Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.  <br>Then select the hosts and the Nessus Family of Plugins you want to include.</p>
  	  <select NAME="agency" SIZE="10"  style="width:950px;margin:5px 0 5px 0;" ONCHANGE="f1.submit()" >
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Report Name]",70)).str_replace(' ','&nbsp;',str_pad("[Date]",20))."</option>";
			while($agency_row = $agency_stmt->fetch(PDO::FETCH_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["report_name"], 70));
				$formatedDate = date("D M d H:i:s Y", $agency_row["scan_end"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $agency_row["agency"] . ":" . $agency_row["report_name"] . ":" . $agency_row["scan_start"] . ":" . $agency_row["scan_end"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select>
	  </form>
	  </td>
	</tr>  
	<tr>
	  <td style="width: 700px;" valign="top"> 
	  <form name="f2" action="low2pwnReport.php" method="post">
		<?php
		//host list
		if($agency == ""){
		?>
			<p align="center">[ Hosts ]</p>
			<SELECT MULTIPLE NAME="host" SIZE="25" style="width:700px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Hosts ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('hostselectall',true)" />
			<SELECT MULTIPLE NAME="host[]" SIZE="20" style="width:700px;margin:5px 0 5px 0;" id="hostselectall">
		<?php
			echo "<option value=\"REMOVE\">".str_replace(' ','&nbsp;',str_pad("[Host Name]", 16)).str_replace(' ','&nbsp;',str_pad("[IP Address]", 16)).str_replace(' ','&nbsp;',str_pad("[FQDN]", 35)).str_replace(' ','&nbsp;',str_pad("[NetBIOS]", 16))."</option>";
			while($host_row = $host_stmt->fetch(PDO::FETCH_ASSOC)){
			/*
			Nessus host_name can be an IP address or domain name depending on what was used to start the scan.  This is a pain in the ass.  Just saying :-)
			FQDN for host names mess up my nice neat columns so I'm going to just pull the host name from the FQDN.  How to tell between FQDN and IP?  Some pretty shitty code :-)
			*/
			  $host_check = explode(".",$host_row["host_name"]);
			  if(strlen($host_check[0] < 3)){ $host_name = $host_check[0];} else { $host_name = $host_row["host_name"]; }
			  $value1 = str_replace(' ','&nbsp;',str_pad($host_name, 16));
			  $value2 = str_replace(' ','&nbsp;',str_pad($host_row["ip_addr"], 16));
			  $value3 = str_replace(' ','&nbsp;',str_pad($host_row["fqdn"], 35));
			  $value4 = str_replace(' ','&nbsp;',str_pad($host_row["netbios"], 16));
			  echo "<OPTION value='" . $host_row["host_name"] . "'>" . $value1 . $value2 . $value3 . $value4 . "</OPTION>";
			}//end while
		?>
			</SELECT>						
		<?php
		}//end else
		?>
	<br><table>
	  <TR>
		<TD>
		<input type="hidden" name="agency" value="<?php echo "$agency";?>">
		<input type="hidden" name="report_name" value="<?php echo "$report_name";?>">
		<input type="hidden" name="scan_start" value="<?php echo "$scan_start";?>">
		<input type="hidden" name="scan_end" value="<?php echo "$scan_end";?>">
		<INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
		</TD>
	  </TR>
	</table>

	  </td>
      <td style="width: 250px;" valign="top" align="right">	  
      <table style="text-align: left; width: 225px;" border="0" cellpadding="2" cellspacing="2">
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Low 2 Pwn Issues</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="isServiceDetect" checked>
			</td>
            <td style="width: 174px;">Service Detection</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="isAdmin" checked>
			</td>
            <td style="width: 174px;">Admin Pages</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="isWebDAV" checked>
			</td>
            <td style="width: 174px;">HTTP PUT/WebDAV/SEARCH</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="isTraceaxd" checked>
			</td>
            <td style="width: 174px;">Trace.axd</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="isAFP" checked>
			</td>
            <td style="width: 174px;">Apple Filing Protocol (AFP)</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="isSharepoint" checked>
			</td>
            <td style="width: 174px;">Sharepoint</td>
          </tr>
          <tr>
            <td style="width: 30px;">
                                <input type="checkbox" value="1" name="isBrowsableDir" checked>
                        </td>
            <td style="width: 174px;">Browsable Directories</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="isJbossTomcat" checked>
			</td>
            <td style="width: 174px;">JBoss/Tomcat server-status</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Low 2 Pwn Honorable Mentions</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="isNullSession" checked>
			</td>
            <td style="width: 174px;">Null Session</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="isFCKeditor" checked>
			</td>
            <td style="width: 174px;">FCKeditor</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="isNFS" checked>
			</td>
            <td style="width: 174px;">Open NFS</td>
          </tr>
      </table>
	  </form>
      </td>
    </tr>
    <tr>
      <td colspan="2">
			<?php include '../main/footer.php'; ?>
      </td>
    </tr>
</table>
</td></tr></table>
</body>
</html>


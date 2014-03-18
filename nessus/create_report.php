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
	print_r($host_data);
	$host_stmt = $db->prepare($host_sql);
	$host_stmt->execute($host_data);
	$test = $host_stmt->rowCount();echo $test . "<hr>";
	$plugin_sql = 	"SELECT DISTINCT 
						nessus_results.pluginFamily 
					FROM 
						nessus_results 
				WHERE 
					nessus_results.agency = ? AND
					nessus_results.report_name = ? AND
					nessus_results.scan_start = ? AND
					nessus_results.scan_end = ?
					ORDER BY 
						nessus_results.pluginFamily
					";
	$plugin_data = array($agency, $report_name, $scan_start, $scan_end);
	$plugin_stmt = $db->prepare($plugin_sql);
	$plugin_stmt->execute($plugin_data);
}//end if

?>

<HTML>
<head>
<title>CREATE NESSUS REPORT</title>
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
	  <form name="f2" action="report.php" method="post">
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
			echo "<option value=\"REMOTE\">".str_replace(' ','&nbsp;',str_pad("[Host Name]", 16)).str_replace(' ','&nbsp;',str_pad("[IP Address]", 16)).str_replace(' ','&nbsp;',str_pad("[FQDN]", 35)).str_replace(' ','&nbsp;',str_pad("[NetBIOS]", 16))."</option>";
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
		<?php
		//nessus plugin families
		if($agency == ""){
		?>
			<p align="center">[ Plugin Families ]</p>
			<SELECT MULTIPLE NAME="family" SIZE="15" style="width:700px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Plugin Families ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('familyselectall',true)" />
			<SELECT MULTIPLE NAME="family[]" SIZE="15" style="width:700px;margin:5px 0 5px 0;" id="familyselectall">
		<?php
			while($plugin_row = $plugin_stmt->fetch(PDO::FETCH_ASSOC)){
				if($plugin_row["pluginFamily"] == ""){
					echo "<OPTION value='" . $plugin_row["pluginFamily"] . "'>Information Only</OPTION>";
				} else {
					echo "<OPTION value='" . $plugin_row["pluginFamily"] . "'>" . $plugin_row["pluginFamily"] . "</OPTION>";
				}
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>	  
	<br>
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	  <tr>
	  <td>
		<p>Who created this report?</p>
<p><textarea style="width:300px;margin:5px 0 5px 0;" rows="5" name="w1">
Mainstream Security
James Edge
Attack & Penetration Testing Practice Manager
james@mainstreamsecurity.com
</textarea></p>
	  </td>
	  <td>
		<p>Who is this report for?</p>
<p><textarea style="width:300px;margin:5px 0 5px 0;" rows="5" name="w2">
</textarea></p>
	  </td>
	  </tr>
	</table>  
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
            <td colspan="2" rowspan="1" style="width: 30px;">Plugin Information</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isPlugName" checked>
			</td>
            <td style="width: 174px;">Include Name</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input checked="checked" name="default" value="default" type="checkbox">
			</td>
            <td style="width: 174px;">Include Family</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isPlugInfo" checked>
			</td>
            <td style="width: 174px;">Additional Information</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isSynopsis" checked>
			</td>
            <td style="width: 174px;">Synopsis</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isDescription" checked>
			</td>
            <td style="width: 174px;">Description</td>
          </tr>
          <tr>
            <td style="width: 30px;">
                                <input type="checkbox" value="y" name="isSolution" checked>
                        </td>
            <td style="width: 174px;">Solution</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isSeeAlso" checked>
			</td>
            <td style="width: 174px;">See Also</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isPlugOut" checked>
			</td>
            <td style="width: 174px;">Include Plugin Output</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Risk Information</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isCvss" checked>
			</td>
            <td style="width: 174px;">Include Cvss Score</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isVulnPub">
			</td>
            <td style="width: 174px;">Include Vuln Pub Date</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isExploit">
			</td>
            <td style="width: 174px;">Include Exploit Information</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Additional Research Links</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isCve" checked>
			</td>
            <td style="width: 174px;">Include CVE</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isBid" checked>
            </td>
            <td style="width: 174px;">Include Bugtraq ID</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isOsvdb" checked>
			</td>
            <td style="width: 174px;">Include OSVBD</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isCert" checked>
			</td>
            <td style="width: 174px;">Include Cert</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isIava" checked>
			</td>
            <td style="width: 174px;">Include IAVA</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isCWE" checked>
			</td>
            <td style="width: 174px;">Include CWE</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isMS" checked>
			</td>
            <td style="width: 174px;">Include MS Bulletin</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isSec" checked>
			</td>
            <td style="width: 174px;">Include Secunia</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isEdb" checked>
			</td>
            <td style="width: 174px;">Include EDB-ID</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Severity</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="4" name="critical" checked>
			</td>
            <td style="width: 174px;">Critical Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="3" name="high" checked>
			</td>
            <td style="width: 174px;">High Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="2" name="medium">
			</td>
            <td style="width: 174px;">Medium Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="low">
			</td>
            <td style="width: 174px;">Low Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="0" name="info">
			</td>
            <td style="width: 174px;">Information Only</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Miscellaneous</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isNotes">
			</td>
            <td style="width: 174px;">Include Notes</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isAffected" checked>
			</td>
            <td style="width: 174px;">Include Host List</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="isService" checked>
			</td>
            <td style="width: 174px;">Include Service/Protocol</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="cover" checked>
			</td>
            <td style="width: 174px;">Include Cover Page</td>
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


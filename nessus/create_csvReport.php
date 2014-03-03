<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );

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
$agency_result = $db->query($agency_sql);ifError($plugin_result);

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
					nessus_results.agency='$agency' AND
					nessus_results.report_name='$report_name' AND
					nessus_results.scan_start='$scan_start' AND
					nessus_results.scan_end='$scan_end'
				ORDER BY 
					nessus_tags.host_name
				";

	$host_result = $db->query($host_sql);ifError($host_result);
	$plugin_sql = 	"SELECT DISTINCT 
						nessus_results.pluginFamily 
					FROM 
						nessus_results 
					WHERE 
						nessus_results.agency='$agency' AND 
						nessus_results.report_name='$report_name' AND
						nessus_results.scan_start='$scan_start' AND
						nessus_results.scan_end='$scan_end'
					ORDER BY 
						nessus_results.pluginFamily
					";
	$plugin_result = $db->query($plugin_sql);ifError($plugin_result);
}//end if
?>

<HTML>
<head>
<title>CREATE NESSUS VULNERABILITY MATRIX</title>
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
<td>
<table style="text-align: left; width: 850px;" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td style="width: 600px;">
	  <form name="f1"  action="" method="post">
	  <p align="center">[ Nessus Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.  <br>Then select the hosts and the Nessus Family of Plugins you want to include.</p>
  	  <select NAME="agency" SIZE="10"  style="width:600px;margin:5px 0 5px 0;" ONCHANGE="f1.submit()" >
		<option value="none" selected>[Agency]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Report Name]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Date/Time]</option>
			<?php
			while($agency_row = $agency_result->fetchRow(DB_FETCHMODE_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["report_name"], 20));
				$formatedDate = date("D M d H:i:s Y", $agency_row["scan_end"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $agency_row["agency"] . ":" . $agency_row["report_name"] . ":" . $agency_row["scan_start"] . ":" . $agency_row["scan_end"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select>
	  </form>
	<form name="f2" action="csvReport.php" method="post">
		<?php
		//host list
		if($agency == ""){
		?>
			<p align="center">[ Hosts ]</p>
			<SELECT MULTIPLE NAME="host" SIZE="25" style="width:600px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Hosts ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('hostselectall',true)" />
			<SELECT MULTIPLE NAME="host[]" SIZE="20" style="width:600px;margin:5px 0 5px 0;" id="hostselectall">
			<option value='REMOVE'>[Host Name]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[IP Address]&nbsp;&nbsp;&nbsp;&nbsp;[FQDN]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[NetBIOS]</option>
		<?php
			while($host_row = $host_result->fetchRow(DB_FETCHMODE_ASSOC)){
			/*
			Nessus host_name can be an IP address or domain name depending on what was used to start the scan.  This is a pain in the ass.  Just saying :-)
			FQDN for host names mess up my nice neat columns so I'm going to just pull the host name from the FQDN.  How to tell between FQDN and IP?  Some pretty shitty code :-)
			*/
			  $host_check = explode(".",$host_row["host_name"]);
			  if(strlen($host_check[0] < 3)){ $host_name = $host_check[0];} else { $host_name = $host_row["host_name"]; }
			  $value1 = str_replace(' ','&nbsp;',str_pad($host_name, 16));
			  $value2 = str_replace(' ','&nbsp;',str_pad($host_row["ip_addr"], 16));
			  $value3 = str_replace(' ','&nbsp;',str_pad($host_row["fqdn"], 25));
			  $value4 = str_replace(' ','&nbsp;',str_pad($host_row["netbios"], 16));
			  echo "<OPTION value='" . $host_row["host_name"] . "'>" . $value1 . $value2 . $value3 . $value4 . "</OPTION>";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>
		<?php
		if($agency == ""){
		?>
			<p align="center">[ Plugin Families ]</p>
			<SELECT MULTIPLE NAME="family" SIZE="15" style="width:600px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Plugin Families ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('familyselectall',true)" />
			<SELECT MULTIPLE NAME="family[]" SIZE="15" style="width:600px;margin:5px 0 5px 0;" id="familyselectall">
		<?php
			while($plugin_row = $plugin_result->fetchRow(DB_FETCHMODE_ASSOC)){
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
	<br><table align="center">
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
            <td colspan="2" rowspan="1" style="width: 30px;">Sort Order</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="risk" name="isSort" checked>
			</td>
            <td style="width: 174px;">Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="family" name="isSort">
			</td>
            <td style="width: 174px;">Plugin Family</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="exploit" name="isSort">
			</td>
            <td style="width: 174px;">Exploitability</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="vuln_age" name="isSort">
			</td>
            <td style="width: 174px;">Vulnerability Age</td>
          </tr>
      </table>
      </td>
    </tr>
    <tr>
      <td colspan="2">
			<?php  include '../main/footer.php'; ?>
      </td>
    </tr>
</table>
</FORM>

</td></tr></table>
</body>
</html>
<?php
function ifError($error)
{
	if (PEAR::isError($error)) {
		echo 'Standard Message: ' . $error->getMessage() . "</br>";
		echo 'Standard Code: ' . $error->getCode() . "</br>";
		echo 'DBMS/User Message: ' . $error->getUserInfo() . "</br>";
		echo 'DBMS/Debug Message: ' . $error->getDebugInfo() . "</br>";
		exit;
	}
}
?>
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
					nessus_compliance_results.agency, 
					nessus_compliance_results.report_name, 
					nessus_compliance_results.scan_start, 
					nessus_compliance_results.scan_end 
				FROM 
					nessus_compliance_results
				";
$agency_result = $db->query($agency_sql);ifError($compliance_result);

if($agency != ""){
	$host_sql = "SELECT DISTINCT 
					nessus_compliance_results.host_name, 
					nessus_compliance_results.ip_addr, 
					nessus_compliance_results.fqdn, 
					nessus_compliance_results.netbios 
				FROM 
					nessus_compliance_results 
				WHERE 
					nessus_compliance_results.agency='$agency' AND
					nessus_compliance_results.report_name='$report_name' AND
					nessus_compliance_results.scan_start='$scan_start' AND
					nessus_compliance_results.scan_end='$scan_end'
				ORDER BY 
					nessus_compliance_results.host_name
				";

	$host_result = $db->query($host_sql);ifError($host_result);
	$compliance_sql = 	"SELECT DISTINCT
						nessus_audit_file.custom_item_type
					FROM
						nessus_audit_file
					INNER JOIN nessus_compliance_results ON nessus_audit_file.description = nessus_compliance_results.description
					WHERE
						nessus_compliance_results.agency = '$agency' AND
						nessus_compliance_results.report_name = '$report_name' AND
						nessus_compliance_results.scan_start = '$scan_start' AND
						nessus_compliance_results.scan_end = '$scan_end'
					ORDER BY
						nessus_audit_file.custom_item_type ASC
					";
	$compliance_result = $db->query($compliance_sql);ifError($compliance_result);
}//end if

?>
<HTML>
<head>
<title>CREATE NESSUS COMPLIANCE REPORT</title>
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
<table style="text-align: left; width: 850px;" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td style="width: 600px;">
	  <form name="f1"  action="" method="post">
	  <p align="center">[ Nessus Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.  <br>Then select the hosts and the Nessus Family of Plugins you want to include.</p>
  	  <select NAME="agency" SIZE="10"  style="width:600px;margin:5px 0 5px 0;" ONCHANGE="f1.submit()" >
		<option value="none" selected>[Agency]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Report Name]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Date/Time]]</option>
			<?php
			while($agency_row = $agency_result->fetchRow(DB_FETCHMODE_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["report_name"], 20));
				$formatedDate = date("D M d H:i:s Y", $agency_row["scan_end"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $agency_row["agency"] . ":" . $agency_row["report_name"] . ":" . $agency_row["scan_start"] . ":" . $agency_row["scan_end"] . "'>" . $value1 . $value2 . $value3 . "</option>\n";
			}
			?>
	  </select>
	  </form>
	<form name="f2" action="complianceCSV.php" method="post">
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
			  echo "<OPTION value='" . $host_row["host_name"] . "'>" . $value1 . $value2 . $value3 . $value4 . "</OPTION>\n";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>
		<?php
		if($agency == ""){
		?>
			<p align="center">[ Compliance Types ]</p>
			<SELECT MULTIPLE NAME="itemType" SIZE="15" style="width:600px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Compliance Types ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('complianceselectall',true)" />
			<SELECT MULTIPLE NAME="itemType[]" SIZE="15" style="width:600px;margin:5px 0 5px 0;" id="complianceselectall">
		<?php
			while($compliance_row = $compliance_result->fetchRow(DB_FETCHMODE_ASSOC)){
					echo "<OPTION value='" . $compliance_row["custom_item_type"] . "'>" . $compliance_row["custom_item_type"] . "</OPTION>\n";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>
	  </td>
      <td style="width: 250px;" valign="top" align="right">
      <table style="text-align: left; width: 225px;" border="0" cellpadding="2" cellspacing="2">
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Check Results</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="PASSED" checked>
			</td>
            <td style="width: 174px;">Passed</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="FAILED" checked>
			</td>
            <td style="width: 174px;">Failed</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="y" name="ERROR">
			</td>
            <td style="width: 174px;">Error</td>
          </tr>
      </table>
		<input type="hidden" name="agency" value="<?php echo "$agency";?>">
		<input type="hidden" name="report_name" value="<?php echo "$report_name";?>">
		<input type="hidden" name="scan_start" value="<?php echo "$scan_start";?>">
		<input type="hidden" name="scan_end" value="<?php echo "$scan_end";?>">
		<INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
      </td>
    </tr>
    <tr>
      <td colspan="2">
		<?php include '../main/footer.php'; ?>
      </td>
    </tr>
</table>
</form>
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
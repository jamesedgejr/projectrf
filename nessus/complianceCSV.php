<?php
$start = mktime();
include('../main/config.php');
require_once( 'DB.php' );
date_default_timezone_set('UTC');
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );ifError($db);
$agency = $_POST["agency"];
$report_name = $_POST["report_name"];
$scan_start = $_POST["scan_start"];
$scan_end = $_POST["scan_end"];
$hostPost = $_POST["host"];
foreach($hostPost as $key => $value) {
	if ($value == "REMOVE") unset($hostPost[$key]);
}
$sql = "CREATE temporary TABLE nessus_temp_hosts (host_name VARCHAR(255))";
$result = $db->query($sql);ifError($result);
foreach ($hostPost as $hP){
	$sql="INSERT INTO nessus_temp_hosts (host_name) VALUES ('$hP')";
	$result = $db->query($sql);ifError($result);	
}
$itemTypePost = $_POST["itemType"];
$sql = "CREATE temporary TABLE nessus_temp_itemType (custom_item_type VARCHAR(255))";
$result = $db->query($sql);ifError($result);
foreach ($itemTypePost as $iTP){
	$sql="INSERT INTO nessus_temp_itemType (custom_item_type) VALUES ('$iTP')";
	$result = $db->query($sql);ifError($result);	
}

$passed = $_POST["PASSED"];
$failed = $_POST["FAILED"];
$error  = $_POST["ERROR"];
$sArray = array();
if ($passed == "y") {$sArray[] = 1;}
if ($failed == "y") {$sArray[] = 3;}
if ($error == "y") {$sArray[] = 2;}
$sql = "CREATE temporary TABLE nessus_temp_severity (severity VARCHAR(255))";
$result = $db->query($sql);ifError($result);
foreach ($sArray as $s){
		$sql="INSERT INTO nessus_temp_severity (severity) VALUES ('$s')";
		$result = $db->query($sql);	ifError($result);
}


date_default_timezone_set('UTC');
$myDir = "/var/www/projectRF/nessus/csvfiles/";
$myFileName = $agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");
$sql = 	"SELECT 
			nessus_compliance_results.host_name,
			nessus_compliance_results.ip_addr,
			nessus_compliance_results.mac_addr,
			nessus_compliance_results.fqdn,
			nessus_compliance_results.netbios,
			nessus_compliance_results.operating_system,
			nessus_compliance_results.host_start,
			nessus_compliance_results.host_end,
			nessus_compliance_results.pluginID,
			nessus_compliance_results.pluginName,
			nessus_compliance_results.severity,
			nessus_compliance_results.description,
			nessus_audit_file.check_type,
			nessus_audit_file.custom_item_type,
			nessus_audit_file.value_type,
			nessus_audit_file.value_data,
			nessus_audit_file.service_name,
			nessus_audit_file.svc_option,
			nessus_audit_file.acl_option,
			nessus_audit_file.file,
			nessus_audit_file.reg_key,
			nessus_audit_file.reg_item,
			nessus_compliance_results.plugin_output,
			nessus_compliance_results.remoteValue,
			nessus_compliance_results.policyValue,
			nessus_compliance_results.complianceError
		FROM
			nessus_compliance_results
		INNER JOIN nessus_temp_hosts ON nessus_temp_hosts.host_name = nessus_compliance_results.host_name
		INNER JOIN nessus_audit_file ON nessus_compliance_results.description = nessus_audit_file.description
		INNER JOIN nessus_temp_itemType ON nessus_audit_file.custom_item_type = nessus_temp_itemType.custom_item_type
		INNER JOIN nessus_temp_severity ON nessus_temp_severity.severity = nessus_compliance_results.severity
		WHERE
			nessus_compliance_results.agency = '$agency' AND
			nessus_compliance_results.report_name = '$report_name' AND
			nessus_compliance_results.scan_start = '$scan_start' AND
			nessus_compliance_results.scan_end = '$scan_end'
		";
$result = $db->query($sql);	ifError($result);

fwrite($fh, "\"Agency\",\"Scan Start\",\"Scan End\",\"Report Name\",\"Hostname\",\"IP Address\",\"MAC Address\",\"FQDN\",\"NetBIOS\",\"OS\",\"Host Start\",\"Host End\",\"pluginID\",\"Plugin Name\",\"Severity\",\"Description\",\"Check Type\",\"Check Details\",\"Value Type\",\"Value Data\",\"Service Name\",\"SVC Option\",\"ACL Option\",\"File\",\"Reg Key\",\"Reg Item\",\"Plugin Output\",\"Remote Value\",\"Policy Value\",\"Compliance Error\"");
fwrite($fh, "\n");

while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){
	$host_name = $row["host_name"];
	$ip_addr = $row["ip_addr"];
	$mac_addr = $row["mac_addr"];
	$fqdn = $row["fqdn"];
	$netbios = $row["netbios"];
	$operating_system = $row["operating_system"];
	$host_start = $row["host_start"];
	$host_end = $row["host_end"];
	$pluginID = $row["pluginID"];
	$pluginName = $row["pluginName"];
	$severity = $row["severity"];
	$description = addslashes($row["description"]);
	$check_type = $row["check_type"];
	$custom_item_type = $row["custom_item_type"];
	$value_type = str_replace('"', '', $row["value_type"]);
	$value_data = str_replace('"', '', $row["value_data"]);
	$service_name = str_replace('"', '', $row["service_name"]);
	$svc_option = str_replace('"', '', $row["svc_option"]);
	$acl_option = str_replace('"', '', $row["acl_option"]);
	$check_file = str_replace('"', '', $row["file"]);
	$reg_key = str_replace('"', '', $row["reg_key"]);
	$reg_item = str_replace('"', '', $row["reg_item"]);
	$plugin_output = str_replace('"', '', $row["plugin_output"]);
	$remoteValue = str_replace('"', '', $row["remoteValue"]);
	$policyValue = str_replace('"', '', $row["policyValue"]);
	$complianceError = $row["complianceError"];

fwrite($fh, "\"$agency\",\"$scan_start\",\"$scan_end\",\"$report_name\",\"$host_name\",\"$ip_addr\",\"$mac_addr\",\"$fqdn\",\"$netbios\",\"$operating_system\",\"$host_start\",\"$host_end\",\"$pluginID\",\"$pluginName\",\"$severity\",\"$description\",\"$check_type\",\"$custom_item_type\",\"$value_type\",\"$value_data\",\"$service_name\",\"$svc_option\",\"$acl_option\",\"$file\",\"$reg_key\",\"$reg_item\",\"$plugin_output\",\"$remoteValue\",\"$policyValue\",\"$complianceError\"");
fwrite($fh, "\n");
}
$isStyle = "style_nessus.css";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
select {font-family: courier new}
</style>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title><?php echo "$agency - Nessus Vulnerability Report";?></title>
<link rel="stylesheet" type="text/css" href="../main/<?php echo "$isStyle";?>" />
</head>
<body>
<table width="100%"><tr>
	<td width="20%" valign="top">
	<?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">
		<hr>
		<p align="center"><a href="csvfiles/<?php echo "$myFileName";?>">Click Here</a> to download the CSV file.</p>
		<hr>
	</td>
</tr></table>
<?php 
$end = mktime();
$time = $end - $start;
echo "<!--" . $time . "-->";
?>
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
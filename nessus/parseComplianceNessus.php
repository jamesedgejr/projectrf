<html>
<head><title>Completed upload of Nessus Compliance v2 XML file.</title>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
</style>
</head>
<body>
<table width="100%"><tr>
	<td width="20%" valign="top">
	<?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">

<?php
$agency = $_POST["agency"];
$uploaddir = sys_get_temp_dir();
$uploadfile = tempnam(sys_get_temp_dir(), basename($_FILES['userfile']['name']));
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
	echo "<hr><p align=\"center\"><b>File is valid, and was successfully uploaded.</b></p><hr>";
	} else { 
		echo "<h1>Upload Error!</h1>";
		echo "An error occurred while executing this script. The file may be too large or the upload folder may have insufficient permissions.";
		echo "<p />";
		echo "Please examine the following items to see if there is an issue";
		echo "<hr><pre>";
		echo "1.  ".$uploaddir." (Temp) directory exists and has the correct permissions.<br />";
		echo "2.  The php.ini file needs to be modified to allow for larger file uploads.<br />";
		echo "</pre><hr>";
		exit; 
}


if(file_exists($uploadfile)) { 
	$xml = simplexml_load_file($uploadfile);
} 
else { 
	exit('Failed to open the xml file');
} 

include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
$randValue = rand();
$compliancePluginArray = array("21156","21157","24760","33814","33929","33930","33931","46689");
$report_name = $xml->Report[name];
foreach($xml->Report->ReportHost as $ReportHost){
	/* 
	   The name can be either an IP or domain name.
	   It depends on if you used the IP or DNS name when a machine was scanned.
	*/
	$host_name = $ReportHost[name];
	foreach($ReportHost->HostProperties->tag as $tag){
		switch ($tag[name]) {
			case "HOST_END":
				$host_end = $tag;
				break;
			case "operating-system":
				$operating_system = $tag;
				break;
			case "mac-address":
				$mac_addr = $tag;
				break;
			case "host-ip":
				$ip_addr = $tag;
				break;
			case "host-fqdn":
				$fqdn = $tag;
				break;
			case "netbios-name":
				$netbios = $tag;
				break;
			case "HOST_START":
				$host_start = $tag;
				break;
			case "system-type":
				$system_type = $tag;
				break;
			case "ssh-auth-meth":
				$ssh_auth_meth = $tag;
				break;
			case "ssh-login-used":
				$ssh_login_used = $tag;
				break;
			case "smb-login-used":
				$smb_login_used = $tag;
				break;
			case "local-checks-proto":
				$local_checks_proto = $tag;
				break;
/*-----PCI DSS COMPLIANCE -------------------------------------------------*/
			case "pcidss:compliance:failed":
				$pcidss_compliance_failed = $tag;
				break;
			case "pci-dss-compliance":
				$pcidss_compliance = $tag;
				break;
			case "pcidss:low_risk_flaw":
				$pcidss_low_risk_flaw = $tag;
				break;
			case "pcidss:medium_risk_flaw":
				$pcidss_medium_risk_flaw = $tag;
				break;
			case "pcidss:high_risk_flaw":
				$pcidss_high_risk_flaw = $tag;
				break;
			case "pcidss:www:xss":
				$pcidss_www_xss = $tag;
				break;
			case "pcidss:www:header_injection":
				$pcidss_www_header_injection = $tag;
				break;
			case "pcidss:directory_browsing":
				$pcidss_directory_browsing = $tag;
				break;
			case "pcidss:obsolete_operating_system":
				$pcidss_obsolete_operating_system = $tag;
				break;
			case "pcidss:deprecated_ssl":
				$pcidss_deprecated_ssl = $tag;
				break;
			case "pcidss:reachable_db":
				$pcidss_reachable_db = $tag;
				break;
			case "pcidss:expired_ssl_certificate":
				$pcidss_expired_ssl_certificate = $tag;
				break;
			default:  //who knows all the wonderful tags nessus has created.  I specifically ignore MSxx-xxx tags.
					if(!preg_match("/MS\d+-\d+/i", $tag[name])){
						$newTag[] = (string)$tag[name];
					}
		}
	}
	$sql = "INSERT INTO nessus_tags 
				(fqdn,host_end,host_start,ip_addr,local_checks_proto,mac_addr,netbios,operating_system,pcidss_compliance,
				pcidss_compliance_failed,pcidss_deprecated_ssl,pcidss_directory_browsing,
				pcidss_expired_ssl_certificate,pcidss_high_risk_flaw,pcidss_low_risk_flaw,
				pcidss_medium_risk_flaw,pcidss_obsolete_operating_system,pcidss_reachable_db,
				pcidss_www_header_injection,pcidss_www_xss,ssh_auth_meth,ssh_login_used,smb_login_used,system_type)
			VALUES 
				('$fqdn','$host_end','$host_start','$ip_addr','$local_checks_proto','$mac_addr','$netbios','$operating_system','$pcidss_compliance',
				'$pcidss_compliance_failed','$pcidss_deprecated_ssl','$pcidss_directory_browsing','$pcidss_expired_ssl_certificate',
				'$pcidss_high_risk_flaw','$pcidss_low_risk_flaw','$pcidss_medium_risk_flaw','$pcidss_obsolete_operating_system',
				'$pcidss_reachable_db','$pcidss_www_header_injection','$pcidss_www_xss','$ssh_auth_meth','$ssh_login_used','$smb_login_used','$system_type')
			";	
	$result = $db->query($sql);ifDBError($result);
	$sql = "SELECT LAST_INSERT_ID()";
	$tagID = $db->getRow($sql);ifDBError($result);
	
	foreach ($ReportHost->ReportItem as $ReportItem){
		$pluginID = $ReportItem[pluginID];
		if(in_array($pluginID, $compliancePluginArray)) {
			$severity = addslashes($ReportItem[severity]);
			$pluginName = addslashes($ReportItem[pluginName]);
			$full_description = addslashes($ReportItem->description);
			preg_match("/\"(.*)\"/",$ReportItem->description,$description);
			$description = addslashes($description[1]);
			$plugin_output = addslashes($ReportItem->plugin_output);
			
			if($severity != "2"){
				preg_match("/([0-9.]*) /s",$full_description,$compliance);
				$complianceID = $compliance[1];
				preg_match("/Remote value:(.*)Policy value:(.*)/s",$full_description,$policy);
				$remoteValue = $policy[1];
				$policyValue = $policy[2];
				$complianceError = "";
				$startEpoch = strtotime($host_start);
				$endEpoch = strtotime($host_end);
				$startScanArray[] = $startEpoch;
				$endScanArray[] = $endEpoch;
				$scan_start = $randValue;
				$scan_end = $randValue;
				$sql = "INSERT INTO nessus_compliance_results (agency, report_name, scan_start, scan_end, tagID, host_name, pluginID, pluginName, severity, description, plugin_output, remoteValue, policyValue, complianceError) VALUES ('$agency', '$report_name', '$scan_start', '$scan_end', '$tagID[0]', '$host_name', '$pluginID', '$pluginName', '$severity', '$description', '$plugin_output', '$remoteValue', '$policyValue', '$complianceError')";
				$result = $db->query($sql);ifDBError($result);
				
			} elseif ($severity == "2") {
				preg_match("/\[[A-Z]*](.*)/s",$full_description,$errors);
				$complianceError = $errors[1];
				$remoteValue = "";
				$policyValue = "";
				$startEpoch = strtotime($host_start);
				$endEpoch = strtotime($host_end);
				$startScanArray[] = $startEpoch;
				$endScanArray[] = $endEpoch;
				$scan_start = $randValue;
				$scan_end = $randValue;
				$sql = "INSERT INTO nessus_compliance_results (agency, report_name, scan_start, scan_end, tagID, host_name, pluginID, pluginName, severity, description, plugin_output, remoteValue, policyValue, complianceError) VALUES ('$agency', '$report_name', '$scan_start', '$scan_end', '$tagID[0]', '$host_name', '$pluginID', '$pluginName', '$severity', '$description', '$plugin_output', '$remoteValue', '$policyValue', '$complianceError')";
				$result = $db->query($sql);ifDBError($result);
			}
		}
	}
}
/*
Find the scan start and end time from all scan start and end times collected.
*/
sort($startScanArray);
$scan_start = $startScanArray[0];
rsort($endScanArray);
$scan_end = $endScanArray[0];
$sql = "UPDATE nessus_compliance_results SET scan_start = '$scan_start', scan_end = '$scan_end' WHERE scan_start = '$randValue' AND scan_end = '$randValue'";
$result = $db->query($sql);ifDBError($result);
?>
	<table cellspacing="5" cellpadding="5" width="600">
		<tr>
			<td colspan="2">
				<form enctype="multipart/form-data" action="parseComplianceAudit.php" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000000" />
				<img src="images/nessus_logo.png"></img>
				<p>The Nessus .audit file is used in a compliance scan and is the only place where the Compliance Type can be found.  It will be uploaded and parsed adding this information to the database.</p>
			</td>
		</tr>
		<tr>
			<td><p>Select .audit compliance file: </p></td><td><input name="userfile" type="file" /></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Process File" />
				<input type="hidden" name="agency" value="<?php echo "$agency"; ?>">
				<input type="hidden" name="report_name" value="<?php echo "$report_name"; ?>">
				</form>
			</td>
		</tr>
	</table>
</td></tr></table>
</body>
</html>
<?php 
	/*
		Clean up the database after every upload to remove all duplicates.  FOR DEBUGGING AND TESTING PURPOSES
	*
	$sql = "CREATE TEMPORARY TABLE results_temp SELECT DISTINCT agency, report_name, host_name, ip_addr, mac_addr, fqdn, netbios, operating_system, host_start, host_end, pluginID, pluginName, pluginFamily, port, service, protocol, severity, cvss_vector, cvss_score, risk_factor, exploitability_ease, vuln_publication_date, exploit_framework_metasploit, metasploit_name, description, plugin_publication_date, synopsis, see_also, patch_publication_date, exploit_available, plugin_modification_date, plugin_output, plugin_version, solution, cveList, bidList, osvdbList, certList, iavaList, cweList, msftList, secuniaList, edbList FROM nessus_results";
	$result = $db->query($sql);ifDBError($result);
	$sql = "TRUNCATE TABLE nessus_results";
	$result = $db->query($sql);ifDBError($result);
	$sql = "INSERT INTO nessus_results (agency, report_name, host_name, ip_addr, mac_addr, fqdn, netbios, operating_system, host_start, host_end, pluginID, pluginName, pluginFamily, port, service, protocol, severity, cvss_vector, cvss_score, risk_factor, exploitability_ease, vuln_publication_date, exploit_framework_metasploit, metasploit_name, description, plugin_publication_date, synopsis, see_also, patch_publication_date, exploit_available, plugin_modification_date, plugin_output, plugin_version, solution, cveList, bidList, osvdbList, certList, iavaList, cweList, msftList, secuniaList, edbList) SELECT results_temp.agency, results_temp.report_name, results_temp.host_name, results_temp.ip_addr, results_temp.mac_addr, results_temp.fqdn, results_temp.netbios, results_temp.operating_system, results_temp.host_start, results_temp.host_end, results_temp.pluginID, results_temp.pluginName, results_temp.pluginFamily, results_temp.port, results_temp.service, results_temp.protocol, results_temp.severity, results_temp.cvss_vector, results_temp.cvss_score, results_temp.risk_factor, results_temp.exploitability_ease, results_temp.vuln_publication_date, results_temp.exploit_framework_metasploit, results_temp.metasploit_name, results_temp.description, results_temp.plugin_publication_date, results_temp.synopsis, results_temp.see_also, results_temp.patch_publication_date, results_temp.exploit_available, results_temp.plugin_modification_date, results_temp.plugin_output, results_temp.plugin_version, results_temp.solution, results_temp.cveList, results_temp.bidList, results_temp.osvdbList, results_temp.certList, results_temp.iavaList, results_temp.cweList, results_temp.msftList, results_temp.secuniaList, results_temp.edbList FROM results_temp";
	$result = $db->query($sql);ifDBError($result);
	$sql = "DROP TEMPORARY TABLE results_temp";
	$result = $db->query($sql);ifDBError($result);
	*/
function ifDBError($error)
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
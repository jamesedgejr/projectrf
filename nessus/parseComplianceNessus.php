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
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v = new Valitron\Validator($_POST);
$v->rule('slug', 'agency');
if($v->validate()) {

} else {
    print_r($v->errors());
	exit;
} 
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


$randValue = rand();
$compliancePluginArray = array("21156","21157","24760","33814","33929","33930","33931","46689");
$report_name = preg_replace("/[\W]*/", '', $xml->Report[name]);
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
		$tags_sql = "INSERT INTO nessus_tags 
			(
				bios_uuid,
				fqdn,
				host_end,
				host_name,
				host_start,
				ip_addr,
				local_checks_proto,
				mac_addr,
				netbios,
				operating_system,
				operating_system_unsupported,
				pcidss_compliance,
				pcidss_compliance_failed,
				pcidss_deprecated_ssl,
				pcidss_directory_browsing,
				pcidss_expired_ssl_certificate,
				pcidss_high_risk_flaw,
				pcidss_low_risk_flaw,
				pcidss_medium_risk_flaw,
				pcidss_obsolete_operating_system,
				pcidss_reachable_db,
				pcidss_www_header_injection,
				pcidss_www_xss,
				smb_login_used,
				ssh_auth_meth,
				ssh_login_used,
				system_type
			)
			VALUES 
				(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
			";

	$tags_stmt = $db->prepare($tags_sql);
	$tags_sql_data = array($bios_uuid,$fqdn,$host_end,$host_name,$host_start,$ip_addr,$local_checks_proto,$mac_addr,$netbios,$operating_system,$operating_system_unsupported,$pcidss_compliance,$pcidss_compliance_failed,$pcidss_deprecated_ssl,$pcidss_directory_browsing,$pcidss_expired_ssl_certificate,$pcidss_high_risk_flaw,$pcidss_low_risk_flaw,$pcidss_medium_risk_flaw,$pcidss_obsolete_operating_system,$pcidss_reachable_db,$pcidss_www_header_injection,$pcidss_www_xss,$smb_login_used,$ssh_auth_meth,$ssh_login_used,$system_type);
	$tags_stmt->execute($tags_sql_data);
	$tagID = $db->lastInsertId();
	
	foreach ($ReportHost->ReportItem as $ReportItem){
		$port = $ReportItem[port];
		$svc_name = $ReportItem[svc_name];
		$protocol = $ReportItem[protocol];
		$severity = $ReportItem[severity];
		$pluginID = $ReportItem[pluginID];
		$pluginName = $ReportItem[pluginName];
		$pluginFamily = $ReportItem[pluginFamily];
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
				
				
				$sql = "INSERT INTO nessus_compliance_results 
						(
							agency, 
							complianceError,
							description, 
							host_name, 
							plugin_output, 
							pluginID, 
							pluginName, 
							policyValue, 
							remoteValue, 
							report_name, 
							scan_end, 
							scan_start, 
							severity, 
							tagID 
						) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				$stmt = $db->prepare($sql);
				$sql_data = array($agency, $complianceError, $description, $host_name, $plugin_output, $pluginID, $pluginName, $policyValue, $remoteValue, $report_name, $scan_end, $scan_start, $severity, $tagID[0]);
				print_r($sql_data);
				$stmt->execute($sql_data);
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
				$sql = "INSERT INTO nessus_compliance_results 
						(
							agency, 
							complianceError,
							description, 
							host_name, 
							plugin_output, 
							pluginID, 
							pluginName, 
							policyValue, 
							remoteValue, 
							report_name, 
							scan_end, 
							scan_start, 
							severity, 
							tagID 
						) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				$stmt = $db->prepare($sql);
				$sql_data = array($agency, $complianceError, $description, $host_name, $plugin_output, $pluginID, $pluginName, $policyValue, $remoteValue, $report_name, $scan_end, $scan_start, $severity, $tagID[0]);
				$stmt->execute($sql_data);
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
$sql_update_nessus_results = "UPDATE nessus_results SET scan_start = ?, scan_end = ? WHERE scan_start = $randValue AND scan_end = $randValue";
$stmt = $db->prepare($sql_update_nessus_results);
$sql_data = array($scan_start,$scan_end);
$stmt->execute($sql_data);
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

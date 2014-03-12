<html>
<head><title>Completed upload of Nessus v2 XML file.</title>
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
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$randValue = rand();
$startScanArray = array();
$endScanArray = array();
$newTag = array();
$report_name = $xml->Report[name];
foreach($xml->Report->ReportHost as $ReportHost){
	$scan_start = $randValue;
	$scan_end = $randValue;
	/* 
	   The name can be either an IP or domain name.
	   It depends on if you used the IP or DNS name when a machine was scanned.
	*/
	$host_name = $ReportHost[name];
	$host_end = $operating_system = $mac_addr = $ip_addr = $fqdn = $netbios = $host_start = $system_type = "";
	foreach($ReportHost->HostProperties->tag as $tag){
		switch ($tag[name]) {
			case "bios-uuid":
				$bios_uuid = $tag;
				break;
			case "host-fqdn":
				$fqdn = $tag;
				break;
			case "HOST_END":
				$host_end = $tag;
				break;
			case "HOST_START":
				$host_start = $tag;
				break;
			case "host-ip":
				$ip_addr = $tag;
				break;
			case "local-checks-proto":
				$local_checks_proto = $tag;
				break;
			case "mac-address":
				$mac_addr = $tag;
				break;
			case "netbios-name":
				$netbios = $tag;
				break;
			case "operating-system":
				$operating_system = $tag;
				break;
			case "operating-system-unsupported":
				$operating_system_unsupported = $tag;
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
/*-----PCI DSS COMPLIANCE -------------------------------------------------*/
			case "smb-login-used":
				$smb_login_used = $tag;
				break;
			case "ssh-auth-meth":
				$ssh_auth_meth = $tag;
				break;
			case "ssh-login-used":
				$ssh_login_used = $tag;
				break;
			case "system-type":
				$system_type = $tag;
				break;
			default:  //who knows all the wonderful tags nessus has created.  I specifically ignore MSxx-xxx, netstat-XXXX, patch-summary-XXXX, and traceroute tags.
					if(!preg_match("/MS\d+-\d+/i", $tag[name])){
						if(!preg_match("/netstat-.*/i", $tag[name])){
							if(!preg_match("/patch-summary-.*/i", $tag[name])){
								if(!preg_match("/traceroute.*/i", $tag[name])){
									$newTag[] = (string)$tag[name];
								}
							}
						}
					}
		}
	}
	$sql = "INSERT INTO nessus_tags 
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
	$stmt = $db->prepare($sql);
	$sql_data = array($bios_uuid,$fqdn,$host_end,$host_name,$host_start,$ip_addr,$local_checks_proto,$mac_addr,$netbios,$operating_system,$operating_system_unsupported,$pcidss_compliance,$pcidss_compliance_failed,$pcidss_deprecated_ssl,$pcidss_directory_browsing,$pcidss_expired_ssl_certificate,$pcidss_high_risk_flaw,$pcidss_low_risk_flaw,$pcidss_medium_risk_flaw,$pcidss_obsolete_operating_system,$pcidss_reachable_db,$pcidss_www_header_injection,$pcidss_www_xss,$smb_login_used,$ssh_auth_meth,$ssh_login_used,$system_type);
	$stmt->execute($sql_data);
	//$sql = "SELECT LAST_INSERT_ID()";
	$tagID = $db->lastInsertId();
	echo $tagID . "<br>";

	foreach ($ReportHost->ReportItem as $ReportItem){
		
		$cveList = $bidList = $osvdbList = $certList = $iavaList = $iavbList = $cweList = $msftList = $secuniaList = $edbList = $see_alsoList = "";
		
		//grab each value from ReportItem
		$port = htmlspecialchars($ReportItem[port], ENT_QUOTES);
		$service = htmlspecialchars($ReportItem[svc_name], ENT_QUOTES);
		$protocol = htmlspecialchars($ReportItem[protocol], ENT_QUOTES);
		$severity = htmlspecialchars($ReportItem[severity], ENT_QUOTES);
		$pluginID = htmlspecialchars($ReportItem[pluginID], ENT_QUOTES);
		$pluginName = htmlspecialchars($ReportItem[pluginName], ENT_QUOTES);
		$pluginFamily = htmlspecialchars($ReportItem[pluginFamily], ENT_QUOTES);
		
		//XML document now lists items in alphabetical order
		foreach ($ReportItem->bid as $bid) {
			$bidList = $bidList . "," . htmlspecialchars($bid);
		}
		$canvas_package = htmlspecialchars($ReportItem->canvas_package, ENT_QUOTES);
		foreach ($ReportItem->cert as $cert) {
			$certList = $certList . "," . htmlspecialchars($cert);
		}
		$cpe = htmlspecialchars($ReportItem[cpe], ENT_QUOTES);
		foreach ($ReportItem->cve as $cve) {
			$cveList = $cveList . "," . htmlspecialchars($cve);
		}
		$cvss_base_score = htmlspecialchars($ReportItem->cvss_base_score, ENT_QUOTES);
		$cvss_temporal_score = htmlspecialchars($ReportItem->cvss_temporal_score, ENT_QUOTES);
		$cvss_temporal_vector = htmlspecialchars($ReportItem->cvss_temporal_vector, ENT_QUOTES);
		$cvss_vector = htmlspecialchars($ReportItem->cvss_vector, ENT_QUOTES);
		foreach ($ReportItem->cwe as $cwe) {
			$cweList = $cweList . "," . htmlspecialchars($cwe);
		}
		$d2_elliot_name = htmlspecialchars($ReportItem->d2_elliot_name, ENT_QUOTES);
		$description = htmlspecialchars($ReportItem->description, ENT_QUOTES);
		foreach ($ReportItem->edb-id as $edb) {
			$edbList = $edbList . "," . htmlspecialchars($edb);
		}
		$exploit_available = htmlspecialchars($ReportItem->exploit_available, ENT_QUOTES);
		$exploit_framework_canvas = htmlspecialchars($ReportItem->exploit_framework_canvas, ENT_QUOTES);
		$exploit_framework_core = htmlspecialchars($ReportItem->exploit_framework_core, ENT_QUOTES);
		$exploit_framework_d2_elliot = htmlspecialchars($ReportItem->exploit_framework_d2_elliot, ENT_QUOTES);
		$exploit_framework_metasploit = htmlspecialchars($ReportItem->exploit_framework_metasploit, ENT_QUOTES);
		$exploitability_ease = htmlspecialchars($ReportItem->exploitability_ease, ENT_QUOTES);
		//fname
		$fname = htmlspecialchars($ReportItem->fname, ENT_QUOTES);

		foreach ($ReportItem->icsa as $icsa) {
			$icsaList = $icsaList . "," . htmlspecialchars($icsa);
		}
		foreach ($ReportItem->iava as $iava) {
			$iavaList = $iavaList . "," . htmlspecialchars($iava);
		}
		foreach ($ReportItem->iavb as $iavb) {
			$iavbList = $iavbList . "," . htmlspecialchars($iavb);
		}
		$metasploit_name = htmlspecialchars($ReportItem->metasploit_name, ENT_QUOTES);
		foreach ($ReportItem->msft as $msft) {
			$msftList = $msftList . "," . htmlspecialchars($msft);
		}
		foreach ($ReportItem->osvdb as $osvdb) {
			$osvdbList = $osvdbList . "," . htmlspecialchars($osvdb);
		}
		$patch_publication_date = htmlspecialchars($ReportItem->patch_publication_date, ENT_QUOTES);
		$patch_publication_date = ($patch_publication_date == "") ? "Not known": htmlspecialchars($patch_publication_date, ENT_QUOTES);
		$plugin_modification_date = htmlspecialchars($ReportItem->plugin_modification_date, ENT_QUOTES);
		$plugin_modification_date = ($plugin_modification_date == "") ? "Not known": htmlspecialchars($plugin_modification_date, ENT_QUOTES);
		//plugin_name
		$plugin_publication_date = htmlspecialchars($ReportItem->plugin_publication_date, ENT_QUOTES);
		$plugin_publication_date = ($plugin_publication_date == "") ? "Not known": htmlspecialchars($plugin_publication_date, ENT_QUOTES);
		$plugin_type = htmlspecialchars($ReportItem->plugin_type, ENT_QUOTES);
		$risk_factor = htmlspecialchars($ReportItem->risk_factor, ENT_QUOTES);
		$script_version = htmlspecialchars($ReportItem->script_version, ENT_QUOTES);
		foreach ($ReportItem->secunia as $secunia) {
			$secuniaList = $secuniaList . "," . htmlspecialchars($secunia);
		}		
		foreach($ReportItem->see_also as $see_also){
			$see_alsoList = $see_alsoList . "," . htmlspecialchars($see_also);
		}
		$solution = htmlspecialchars($ReportItem->solution, ENT_QUOTES);
		$stig_severity = htmlspecialchars($ReportItem->stig_severity, ENT_QUOTES);
		$synopsis = htmlspecialchars($ReportItem->synopsis, ENT_QUOTES);
		$vuln_publication_date = htmlspecialchars($ReportItem->vuln_publication_date, ENT_QUOTES);
		$vuln_publication_date = ($vuln_publication_date == "") ? "Not known": htmlspecialchars($vuln_publication_date, ENT_QUOTES);
		$plugin_output = htmlspecialchars($ReportItem->plugin_output, ENT_QUOTES);


		/*
		foreach ($ReportItem->xref as $xref) {
			$x = explode(":", $xref);
			switch ($x[0]) {
				case "OSVDB":
					$osvdbList = $osvdbList . "," . htmlspecialchars($x[1]);
					break;
				case "CERT":
					$certList = $certList . "," . htmlspecialchars($x[1]);
					break;
				case "IAVA":
					$iavaList = $iavaList . "," . htmlspecialchars($x[1]);
					break;
				case "CWE":
					$cweList = $cweList . "," . htmlspecialchars($x[1]);
					break;
				case "MSFT":
					$msftList = $msftList . "," . htmlspecialchars($x[1]);
					break;
				case "Secunia":
					$secuniaList = $secuniaList . "," . htmlspecialchars($x[1]);
					break;
				case "EDB-ID":
					$ebdList = $edbList . "," . htmlspecialchars($x[1]);
					break;
			}	
		}
		*/
		$startEpoch = strtotime($host_start);
		$endEpoch = strtotime($host_end);
		$startScanArray[] = $startEpoch;
		$endScanArray[] = $endEpoch;
		
		$sql = "INSERT INTO nessus_results 	
					(
					agency, 
					bidList, 
					canvas_package,
					certList,
					cpe,
					cveList, 
					cvss_base_score, 
					cvss_temporal_score, 
					cvss_temporal_vector, 
					cvss_vector, 
					cweList,
					d2_elliot_name,
					description, 
					edbList,
					exploit_available, 
					exploit_framework_canvas,
					exploit_framework_core,
					exploit_framework_d2_elliot,
					exploit_framework_metasploit, 
					exploitability_ease, 
					fname,
					icsaList,
					iavaList, 
					iavbList,
					metasploit_name, 
					msftList, 
					osvdbList, 
					patch_publication_date, 
					plugin_modification_date, 
					plugin_output, 
					plugin_publication_date, 
					plugin_type, 
					pluginFamily, 
					pluginID, 
					pluginName, 
					port, 
					protocol, 
					report_name, 
					risk_factor, 
					scan_end, 
					scan_start, 
					script_version, 
					secuniaList, 
					see_also, 
					service, 
					severity, 
					solution, 
					stig_severity,
					synopsis, 
					tagID, 
					vuln_publication_date
					) 
				VALUES 
					(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
		";
		$stmt = $db->prepare($sql);
		$sql_data = array($agency,$bidList,$canvas_package,$certList,$cpe,$cveList,$cvss_base_score,$cvss_temporal_score,$cvss_temporal_vector,$cvss_vector,$cweList,$d2_elliot_name,$description,$edbList,$exploit_available,$exploit_framework_canvas,$exploit_framework_core,$exploit_framework_d2_elliot,$exploit_framework_metasploit,$exploitability_ease,$fname,$icsaList,$iavaList,$iavbList,$metasploit_name,$msftList,$osvdbList,$patch_publication_date,$plugin_modification_date,$plugin_output,$plugin_publication_date,$plugin_type,$pluginFamily,$pluginID,$pluginName,$port,$protocol,$report_name,$risk_factor,$scan_end,$scan_start,$script_version,$secuniaList,$see_alsoList,$service,$severity,$solution,$stig_severity,$synopsis,$tagID[0],$vuln_publication_date);
		$stmt->execute($sql_data);
	}
}
/*
Find the scan start and end time from all scan start and end times collected.
*/
sort($startScanArray);
$scan_start = $startScanArray[0];
rsort($endScanArray);
$scan_end = $endScanArray[0];
$sql_update_nessus_results = "UPDATE nessus_results SET scan_start = ?, scan_end = ? WHERE scan_start = ? AND scan_end = ?";
$stmt = $db->prepare($sql_update_nessus_results);
$sql_data = array($scan_start,$scan_end,$randValue,$randValue);
$stmt->execute($sql_update_nessus_results);

//process and display any Nesses <tag> elements that the developer has not seen before
$newTags = array_unique($newTag);
if(!empty($newTags)){
	echo "<p>This parse script found Nessus XML tag elements that are new to the developer.  Please send an email to projectRF(at)jedge.com and provide the name of the tag(s) listed below.  The .nesses file would be helpful as well!</p>";
	echo "<p>Please note that if you are familiar with the HostProperties XML tags in the .nessus file I'm completely ignoring any MSxx-xxx (Microsoft Bulletin), netstat, patch-summer, and tracerout tags.  They are filtered out and not shown to you.</p>";
	foreach ($newTags as $nT){
			echo $nT . "<br>";
	}
}


?>
</td></tr></table>
</body>
</html>

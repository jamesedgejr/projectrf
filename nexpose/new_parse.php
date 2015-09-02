<html>
<head><title>Completed upload of Nexpose XML file.</title>
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
$filename = basename($_FILES['userfile']['name']);
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

libxml_use_internal_errors(true);
if(file_exists($uploadfile)) { 
	$xml = simplexml_load_file($uploadfile);
	if ($xml === false) {
		echo "Failed loading XML\n";
		foreach(libxml_get_errors() as $error) {
			echo "\t", $error->message;
		}
	}	
} 
else { 
	exit('Failed to open the xml file');
} 


/*
<scans>
<scan id="17" name="Marcus Interna CORPl non-PCI" startTime="20150630T224059542" endTime="20150630T230010646" status="finished"/>
</scans>
*/
$xml_version =  $xml["version"];
foreach ($xml->scans->scan as $scan){
	$sql = "INSERT INTO nexpose_scans 
			(
				scan_id,
				scan_name,
				scan_startTime,
				scan_endTime,
				scan_status,
				xml_version,
				agency,
				filename
				
			)
			VALUES 
				(?,?,?,?,?,?,?,?)
			";
	$stmt = $db->prepare($sql);
	$sql_data = array(
						$scan['id'],
						htmlspecialchars($scan['name'], ENT_QUOTES),
						$scan['startTime'],
						$scan['endTime'],
						$scan['status'],
						$xml['version'],
						$agency,
						$filename
				);
	$stmt->execute($sql_data);
}
//<node address="74.82.129.7" status="alive" hardware-address="000000000000" device-id="41" site-name="2015-ASV Recert-74.82.129.7" site-importance="Normal" scan-template="2-ASV PCI Scan (Experis Default)" risk-score="39689.754">
foreach($xml->nodes->node as $node){

	$node_name = "";
	foreach($node->names->name as $name){
		$node_name .= $name . "\n";	
	}
	
	$sql = "INSERT INTO nexpose_nodes
			(
				node_address,
				node_status,
				node_hardware_address,
				node_device_id,
				site_name,
				site_importance,
				scan_template,
				node_risk_score,
				node_name				
			)
			VALUES 
				(?,?,?,?,?,?,?,?,?)
			";
	$stmt = $db->prepare($sql);
	$sql_data = array(
						$node['address'],
						$node['status'],
						$node['hardware-address'],
						$node['device-id'],
						htmlspecialchars($node['site-name'], ENT_QUOTES),
						$node['site-importance'],
						htmlspecialchars($node['scan-template'], ENT_QUOTES),
						$node['risk-score'],
						$node_name
				);
	$stmt->execute($sql_data);
	//put in database table nexpose_nodes with $scan_id (include name and fingerprint info below)
	
	foreach($node->fingerprints->os as $os){
		$os_certainty = $os['certainty'];
		$os_device_class = $os['device-class'];
		$os_vendor = $os['vendor'];
		$os_family = $os['family'];
		$os_product = $os['product'];
		$os_version = $os['version'];
		
		//put in database with $device_id
		$sql = "INSERT INTO nexpose_device_fingerprints
			(
				device_id,
				device_certainty,
				device_class,
				device_vendor,
				device_family,
				device_product,
				device_version				
			)
			VALUES 
				(?,?,?,?,?,?,?)
			";
		$stmt = $db->prepare($sql);
		$sql_data = array(	
							$node['device-id'],
							$os['certainty'],
							$os['device-class'],
							htmlspecialchars($os['vendor'], ENT_QUOTES),
							htmlspecialchars($os['family'], ENT_QUOTES),
							htmlspecialchars($os['product'], ENT_QUOTES),
							htmlspecialchars($os['version'], ENT_QUOTES)
						);
		$stmt->execute($sql_data);

	}	
	
	foreach ($node->tests->test as $test){
		$sql = "INSERT INTO nexpose_tests
			(
				test_id,
				scan_id,
				device_id,
				test_key,
				test_status,
				test_vulnerable_since,
				test_pci_compliance_status,
				test_paragraph
			VALUES 
				(?,?,?,?,?,?,?,?)
			";
		$stmt = $db->prepare($sql);
		$sql_data = array(	
							$test['id'],
							$test['scan-id'],
							$node['device-id'],
							$test['key'],
							$test['status'],
							$test['vulnerable-since'],
							$test['pci-compliance-status'],
							htmlspecialchars($test->Paragraph->asXML(), ENT_QUOTES)
						);
		$stmt->execute($sql_data);		
	}


	
	foreach($node->endpoints->endpoint as $endpoint){
		$sql = "INSERT INTO nexpose_endpoints
			(
				device_id,
				endpoint_protocol,
				endpoint_port,
				endpoint_status,
				service_name			
			)
			VALUES 
				(?,?,?,?,?)
			";
		$stmt = $db->prepare($sql);
		$sql_data = array(	
							$node['device-id'],
							$endpoint['protocol'],
							$endpoint['port'],
							$endpoint['status'],
							htmlspecialchars($endpoint->services->service['name'], ENT_QUOTES)
						);
		$stmt->execute($sql_data);
		$endpoint_id = $db->lastInsertId();
		foreach($endpoint->services->service->fingerprints->fingerprint as $fingerprint){
			$sql = "INSERT INTO nexpose_endpoint_fingerprints
				(
					endpoint_id,
					endpoint_certainty,
					endpoint_vendor,
					endpoint_family,
					endpoint_product,
					endpoint_version			
				)
				VALUES 
					(?,?,?,?,?,?)
				";
			$stmt = $db->prepare($sql);
			$sql_data = array(	
								$endpoint_id,
								$fingerprint['certainty'],
								htmlspecialchars($fingerprint['vendor'], ENT_QUOTES),
								htmlspecialchars($fingerprint['family'], ENT_QUOTES),
								htmlspecialchars($fingerprint['product'], ENT_QUOTES),
								htmlspecialchars($fingerprint['version'], ENT_QUOTES)
							);
			$stmt->execute($sql_data);
		}
		foreach($endpoint->services->service->configuration->config as $config){
		
			$config_name = $config['name'];
			//echo $config_name . "<br>";
			//echo $config . "<br>";
			//put in nexpose_service_config table
		}
		foreach($endpoint->services->service->tests->test as $service_test){
			$sql = "INSERT INTO nexpose_tests
				(
					test_id,
					scan_id,
					device_id,
					endpoint_id,
					test_key,
					test_status,
					test_vulnerable_since,
					test_pci_compliance_status,
					test_paragraph
				)
				VALUES 
					(?,?,?,?,?,?,?,?,?)
				";
			$stmt = $db->prepare($sql);
			$sql_data = array(	
								$service_test['id'],
								$service_test['scan-id'],
								$node['device-id'],
								$endpoint_id,
								$service_test['key'],
								$service_test['status'],
								$service_test['vulnerable-since'],
								$service_test['pci-compliance-status'],
								htmlspecialchars($service_test->Paragraph->asXML(), ENT_QUOTES)
							);
			$stmt->execute($sql_data);					
		}	
	}
}

foreach($xml->VulnerabilityDefinitions->vulnerability as $vulnerability){
/*
<vulnerability 
id="apache-tomcat-cve-2010-1157" 
title="Apache Tomcat: Low: Information disclosure in authentication headers (CVE-2010-1157)"
 severity="3" 
pciSeverity="2" 
cvssScore="2.6" 
cvssVector="(AV:N/AC:H/Au:N/C:P/I:N/A:N)" 
published="20100423T000000000" 
added="20120517T000000000" 
modified="20150213T000000000" 
riskScore="412.9403">

*/

/*

<malware></malware>

*/


/*
<exploits><exploit 
id="43838" 
title="Apache Tomcat 5.5.0 to 5.5.29 &amp; 6.0.0 to 6.0.26 - Information Disclosure Vulnerability" 
type="exploitdb" 
link="http://www.exploit-db.com/exploits/12343" 
skillLevel="Expert"/>
</exploits>

*/
	foreach($vulnerability->exploits->exploit as $exploit){
		$sql = "INSERT INTO nexpose_exploits
			(
				vuln_id,
				exploit_id,
				exploit_title,
				exploit_type,
				exploit_link,
				exploit_skillLevel
			)
			VALUES 
				(?,?,?,?,?,?)
			";
		$stmt = $db->prepare($sql);
		$sql_data = array(	
							htmlspecialchars($vulnerability['id'], ENT_QUOTES),
							$exploit['id'],
							htmlspecialchars($exploit['title'], ENT_QUOTES),
							$exploit['type'],
							htmlspecialchars($exploit['link'], ENT_QUOTES),
							$exploit['skillLevel']
						);
		$stmt->execute($sql_data);	
	}
	$appleList = $bidList = $certList = $cveList = $msftList = $osvdbList = $redhatList = $urlList = $xfList =  "";
	foreach($vulnerability->references->reference as $reference){
		
		switch ($reference['source']) {
				case "APPLE":
					$appleList = $appleList . "," . htmlspecialchars($reference);
					break;
				case "BID":
					$bidList = $bidList . "," . htmlspecialchars($reference);
					break;
				case "CERT":
					$certList = $certList . "," . htmlspecialchars($reference);
					break;
				case "CVE":
					$cveList = $cveList . "," . htmlspecialchars($reference);
					break;
				case "MS":
					$msftList = $msftList . "," . htmlspecialchars($reference);
					break;
				case "OSVDB":
					$osvdbList = $osvdbList . "," . htmlspecialchars($reference);
					break;
				case "REDHAT":
					$redhatList = $redhatList . "," . htmlspecialchars($reference);
					break;
				case "URL":
					$urlList = $urlList . "," . htmlspecialchars($reference);
					break;
				case "XF":
					$xfList = $xfList . "," . htmlspecialchars($reference);
					break;
				default:  
					$newReference[] = (string)$reference['source'];
		}
	}

	foreach($vulnerability->tags->tag as $tag){
		$sql = "INSERT INTO nexpose_tags
			(
				vuln_id,
				tag
			)
			VALUES 
				(?,?)
			";
		$stmt = $db->prepare($sql);
		$sql_data = array(	
							htmlspecialchars($vulnerability['id'], ENT_QUOTES),
							$tag
						);
		$stmt->execute($sql_data);
	}
			$sql = "INSERT INTO nexpose_vulnerabilities
			(
				vuln_id,
				vuln_title,
				pciSeverity,
				cvssScore,
				cvssVector,
				vuln_published,
				vuln_added,
				vuln_modified,
				riskScore,
				description,
				solution,
				appleList,
				bidList,
				certList,
				cveList,
				msftList,
				osvdbList,
				redhatList,
				urlList,
				xfList
			)
			VALUES 
				(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
			";
		$stmt = $db->prepare($sql);
		$sql_data = array(	
							htmlspecialchars($vulnerability['id'], ENT_QUOTES),
							htmlspecialchars($vulnerability['title'], ENT_QUOTES),
							$vulnerability['pciSeverity'],
							$vulnerability['cvssScore'],
							$vulnerability['cvssVector'],
							$vulnerability['published'],
							$vulnerability['added'],
							$vulnerability['modified'],
							$vulnerability['riskScore'],
							htmlspecialchars($vulnerability->description->asXML(), ENT_QUOTES),
							htmlspecialchars($vulnerability->solution->asXML(), ENT_QUOTES),
							$appleList,
							$bidList,
							$certList,
							$cveList,
							$msftList,
							$osvdbList,
							$redhatList,
							$urlList,
							$xfList
						);
		$stmt->execute($sql_data);	
}

//$result = array_unique($newReference);
//print_r($result);
/*

		if (!$stmt) {
			echo "\nPDO::errorInfo():\n";
			print_r($dbh->errorInfo());
		}
*/

?>
</td></tr></table>
</body>
</html>

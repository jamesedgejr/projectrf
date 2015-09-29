<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
/*
$v = new Valitron\Validator($_POST);
$v->rule('accepted', ['isPlugName','isPlugFam','isPlugInfo','isPlugOut','isService','isCvss','isVulnPub','isExploit','isSynopsis','isDescription','isSolution','isSeeAlso','isCve','isBid','isOsvdb','isCert','isIava','isCWE','isMS','isSec','isEdb','isAffected','isNotes','cover']);
$v->rule('numeric', ['scan_start', 'scan_end']);
$v->rule('slug','agency');
//$v->rule('regex','report_name','/[a-zA-Z]+/');
$v->rule('length',1,['critical','high','medium','low','info']);
$v->rule('integer',['critical','high','medium','low','info']);
if(!$v->validate()) {
    print_r($v->errors());
	exit;
} 
*/
$nodeArray = $_POST["node"];
foreach($nodeArray as $key => $value) {
	if ($value == "REMOVE") unset($nodeArray[$key]);
}
$sql = "CREATE temporary TABLE nexpose_tmp_nodes (node_address VARCHAR(255), node_device_id VARCHAR(255), INDEX ndx_node_address (node_address))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($nodeArray as $nA){
	//$v = new Valitron\Validator(array($hA));
	//$v->rule('regex', '0');
	//if(!$v->validate()) {
	//	print_r($v->errors());
	//} 
	$temp_nodes_array = explode(":", $nA);
	$sql="INSERT INTO nexpose_tmp_nodes (node_address, node_device_id) VALUES (?,?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($temp_nodes_array[0],$temp_nodes_array[1]));
}
$tags = $_POST["tags"];
$sql = "CREATE temporary TABLE nexpose_tmp_tags (tag VARCHAR(255), INDEX ndx_tag (tag))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($tags as $t){
	$sql="INSERT INTO nexpose_tmp_tags (tag) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($t));
}

/*$critical = $_POST["critical"];	
$high = $_POST["high"];
$medium = $_POST["medium"];
$low  = $_POST["low"];
$info = $_POST["info"];
$sArray = array($critical, $high, $medium, $low, $info);
$sql = "CREATE temporary TABLE nessus_tmp_severity (severity VARCHAR(255), INDEX ndx_severity (severity))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($sArray as $s){
	if($s != ""){
		$sql="INSERT INTO nessus_tmp_severity (severity) VALUES (?)";
		$stmt = $db->prepare($sql);
		$stmt->execute(array($s));
	}
}
*/

$scan_id = $_POST["scan_id"];
$agency = $_POST["agency"];

$justVulnDB = $_POST["justVulnDB"];
$isVulnDB = $_POST["isVulnDB"];
date_default_timezone_set('UTC');
$myDir = getcwd() . "/csvfiles/";
$myFileName = $agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");


$sql = "SELECT DISTINCT
nexpose_vulnerabilities.vuln_id,
nexpose_vulnerabilities.vuln_title,
nexpose_vulnerabilities.pciSeverity,
nexpose_vulnerabilities.cvssScore,
nexpose_vulnerabilities.cvssVector,
nexpose_vulnerabilities.vuln_published,
nexpose_vulnerabilities.vuln_added,
nexpose_vulnerabilities.vuln_modified,
nexpose_vulnerabilities.riskScore,
nexpose_vulnerabilities.description,
nexpose_vulnerabilities.solution,
nexpose_vulnerabilities.appleList,
nexpose_vulnerabilities.bidList,
nexpose_vulnerabilities.certList,
nexpose_vulnerabilities.cveList,
nexpose_vulnerabilities.msftList,
nexpose_vulnerabilities.osvdbList,
nexpose_vulnerabilities.redhatList,
nexpose_vulnerabilities.urlList,
nexpose_vulnerabilities.xfList,
nexpose_nodes.node_address,
nexpose_nodes.node_name,
nexpose_nodes.node_device_id,
nexpose_endpoints.endpoint_protocol,
nexpose_endpoints.endpoint_port,
nexpose_endpoints.service_name,
nexpose_tests.endpoint_id,
nexpose_tests.test_paragraph
FROM
nexpose_vulnerabilities
Inner Join nexpose_tests ON nexpose_tests.test_id = nexpose_vulnerabilities.vuln_id
Inner Join nexpose_nodes ON nexpose_tests.device_id = nexpose_nodes.node_device_id
Left Join nexpose_endpoints ON nexpose_endpoints.endpoint_id = nexpose_tests.endpoint_id
WHERE
nexpose_tests.scan_id =  ?
ORDER BY
nexpose_vulnerabilities.cvssScore DESC,
nexpose_vulnerabilities.vuln_title ASC
";

$data = array($scan_id);
$stmt = $db->prepare($sql);
$stmt->execute($data);
$header = array($isVulnDB, "CVSS", "Risk", "IP Address", "FQDN", "OS", "Protocol", "Port", "Service Details", "Vuln ID", "Title", "Description", "Solution", "Results");
fputcsv($fh, $header);
//fwrite($fh, "\"$isVulnDB\",\"CVSS\",\"Risk\",\"IP Address\",\"FQDN\",\"OS\",\"Protocol\",\"Port\",\"Service Details\",\"Vuln ID\",\"Title\",\"Description\",\"Solution\"\n");

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

	$cvssScore = $row["cvssScore"];
	$node_address = $row["node_address"];
	$node_name = $row["node_name"];
	$node_device_id = $row["node_device_id"];
	$os_sql = "SELECT DISTINCT
				nexpose_device_fingerprints.device_certainty,
				nexpose_device_fingerprints.device_class,
				nexpose_device_fingerprints.device_vendor,
				nexpose_device_fingerprints.device_family,
				nexpose_device_fingerprints.device_product,
				nexpose_device_fingerprints.device_version
			FROM
				nexpose_device_fingerprints
			WHERE
				nexpose_device_fingerprints.device_id =  ? AND
				nexpose_device_fingerprints.device_certainty >=  '0.8'	
	";
	$os_stmt = $db->prepare($os_sql);
	$os_stmt->execute(array($node_device_id));
	$os_row = $os_stmt->fetch(PDO::FETCH_ASSOC);
	
	$operating_system = $os_row["device_class"] . " " . $os_row["device_vendor"] . " " . $os_row["device_family"] . " " . $os_row["device_product"] . " " . $os_row["device_version"];
	
	$endpoint_protocol = $row["endpoint_protocol"];
	$endpoint_port = $row["endpoint_port"];
	$endpoint_id = $row["endpoint_id"];
	$service_sql = "SELECT DISTINCT
						nexpose_endpoint_fingerprints.endpoint_certainty,
						nexpose_endpoint_fingerprints.endpoint_vendor,
						nexpose_endpoint_fingerprints.endpoint_family,
						nexpose_endpoint_fingerprints.endpoint_product,
						nexpose_endpoint_fingerprints.endpoint_version
					FROM
						nexpose_endpoint_fingerprints
					WHERE
						nexpose_endpoint_fingerprints.endpoint_id =  ? AND
						nexpose_endpoint_fingerprints.endpoint_certainty >=  '0.75'
					";
	$service_stmt = $db->prepare($service_sql);
	$service_stmt->execute(array($endpoint_id));
	$service_row = $service_stmt->fetch(PDO::FETCH_ASSOC);	
	
	$service_details = $service_row["endpoint_vendor"] . " " . $service_row["endpoint_family"] . " " . $service_row["endpoint_product"] . " " . $service_row["endpoint_version"];
	
	$vuln_id = $row["vuln_id"];
	$vuln_title = $row["vuln_title"];
	$vuln_title = str_replace("&lt;","<", $vuln_title);
	$vuln_title = str_replace("&gt;",">", $vuln_title);
	
	$description = htmlspecialchars_decode($row["description"], ENT_QUOTES);
	$description = str_replace('<Paragraph preformat="true">',"", $description);
	$description = str_replace("<description>","", $description);
	$description = str_replace("</description>","", $description);
	$description = str_replace("<ContainerBlockElement>","", $description);
	$description = str_replace("</ContainerBlockElement>","", $description);
	$description = str_replace("<Paragraph>","", $description);
	$description = str_replace("</Paragraph>","", $description);
	$description = str_replace("<ListItem>","", $description);
	$description = str_replace("</ListItem>","", $description);
	$description = str_replace("<UnorderedList>","", $description);
	$description = str_replace("</UnorderedList>","", $description);
	$description = str_replace("","", $description);
	$description = str_replace("&gt;",">", $description);
	$description = str_replace("&lt;","<", $description);	
	$description = str_replace("\n", '', $description);
	$description = str_replace("\r", '', $description);
	$description = str_replace("\r\n", '', $description);
	
	$solution = htmlspecialchars_decode($row["solution"], ENT_QUOTES);
	$solution = str_replace('<Paragraph preformat="true">',"", $solution);
	$solution = str_replace("<solution>","", $solution);
	$solution = str_replace("</solution>","", $solution);
	$solution = str_replace("<ContainerBlockElement>","", $solution);
	$solution = str_replace("</ContainerBlockElement>","", $solution);
	$solution = str_replace("<Paragraph>","", $solution);
	$solution = str_replace("</Paragraph>","", $solution);
	$solution = str_replace("<ListItem>","", $solution);
	$solution = str_replace("</ListItem>","", $solution);
	$solution = str_replace("<UnorderedList>","", $solution);
	$solution = str_replace("</UnorderedList>","", $solution);
	$solution = str_replace("&gt;",">", $solution);
	$solution = str_replace("&lt;","<", $solution);
	$solution = str_replace("\n", '', $solution);
	$solution = str_replace("\r", '', $solution);
	$solution = str_replace("\r\n", '', $solution);

	$paragraph = htmlspecialchars_decode($row["test_paragraph"], ENT_QUOTES);
	$paragraph = str_replace('<Paragraph preformat="true">',"", $paragraph);
	$paragraph = str_replace("<paragraph>","", $paragraph);
	$paragraph = str_replace("</paragraph>","", $paragraph);
	$paragraph = str_replace("<ContainerBlockElement>","", $paragraph);
	$paragraph = str_replace("</ContainerBlockElement>","", $paragraph);
	$paragraph = str_replace("<Paragraph>","", $paragraph);
	$paragraph = str_replace("</Paragraph>","", $paragraph);
	$paragraph = str_replace("<ListItem>","", $paragraph);
	$paragraph = str_replace("</ListItem>","", $paragraph);
	$paragraph = str_replace("<UnorderedList>","", $paragraph);
	$paragraph = str_replace("</UnorderedList>","", $paragraph);
	$paragraph = str_replace("&gt;",">", $paragraph);
	$paragraph = str_replace("&lt;","<", $paragraph);
	$cveList = explode(",", trim($row["cveList"], ","));
	

	$vulnIDListArray = array();
	if($isVulnDB == "CVE"){
		$vulnDBList = $cveList;
	} elseif ($isVulnDB == "BID") {
		$vulnDBList = $bidList;
	} elseif ($isVulnDB == "OSVDB") {
		$vulnDBList = $osvdbList;
	} elseif ($isVulnDB == "MSFT") {
		$vulnDBList = $msftList;
	} elseif ($isVulnDB == "CWE") {
		$vulnDBList = $cweList;
	} elseif ($isVulnDB == "Secunia") {
		$vulnDBList = $secuniaList;
	} 
	if($justVulnDB == "true" && !empty($vulnDBList[0])){
		foreach($vulnDBList as $vDB){
			//fwrite($fh, "\"$vDB\",\"$cvssScore\",\"\",\"$node_address\",\"$node_name\",\"$operating_system\",\"$endpoint_protocol\",\"$endpoint_port\",\"$service_details\",\"$vuln_id\",\"$vuln_title\",\"$description\",\"$solution\"\n");
			fputcsv($fh, array($vDB,$cvssScore,"",$node_address,$node_name,$operating_system,$endpoint_protocol,$endpoint_port,$service_details,$vuln_id,$vuln_title,$description,$solution,$paragraph));
		}
	} elseif ($justVulnDB != "true") {
		foreach($vulnDBList as $vDB){
			//fwrite($fh, "\"$vDB\",\"$cvssScore\",\"\",\"$node_address\",\"$node_name\",\"$operating_system\",\"$endpoint_protocol\",\"$endpoint_port\",\"$service_details\",\"$vuln_id\",\"$vuln_title\",\"$description\",\"$solution\"\n");
			fputcsv($fh, array($vDB,$cvssScore,"",$node_address,$node_name,$operating_system,$endpoint_protocol,$endpoint_port,$service_details,$vuln_id,$vuln_title,$description,$solution,$paragraph));
		}	
	}
}

?>
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title>NESSUS CSV FILE</title>
<link rel="stylesheet" type="text/css" href="../main/<?php echo "$isStyle";?>" />
<style type="text/css">
p {font-size: 90%}
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
		<hr>
		<p align="center"><a href="csvfiles/<?php echo "$myFileName";?>">Click Here</a> to download the CSV file.</p>
		<hr>
	</td>
</tr></table>
</body>
</html>
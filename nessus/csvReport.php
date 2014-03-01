<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
ifError($db);

$hostPost = $_POST["host"];
foreach($hostPost as $key => $value) {
	if ($value == "REMOVE") unset($hostPost[$key]);
}
$sql = "CREATE temporary TABLE nessus_tmp_hosts (host_name VARCHAR(255))";
$sth = $db->prepare($sql);
$results = $db->execute($sth);ifError($results);
foreach ($hostPost as $hP){
	$sql="INSERT INTO nessus_tmp_hosts (host_name) VALUES (?)";
	$sth = $db->prepare($sql);
	$results = $db->execute($sth, $hP);ifError($results);
}
$family = $_POST["family"];
$sql = "CREATE temporary TABLE nessus_tmp_family (pluginFamily VARCHAR(255))";
$sth = $db->prepare($sql);
$results = $db->execute($sth);ifError($results);
foreach ($family as $f){
	$sql="INSERT INTO nessus_tmp_family (pluginFamily) VALUES (?)";
	$sth = $db->prepare($sql);
	$results = $db->execute($sth, $f);ifError($results);	
}

$critical = $_POST["critical"];
$high = $_POST["high"];
$medium = $_POST["medium"];
$low  = $_POST["low"];
$info = $_POST["info"];
$sArray = array($critical, $high, $medium, $low, $info);
$sql = "CREATE temporary TABLE nessus_tmp_severity (severity VARCHAR(255))";
$sth = $db->prepare($sql);
$results = $db->execute($sth);ifError($results);

foreach ($sArray as $s){
	if($s != ""){
		$sql="INSERT INTO nessus_tmp_severity (severity) VALUES (?)";
		$sth = $db->prepare($sql);
		$results = $db->execute($sth, $s);ifError($results);	
	}
}

$agency = $_POST["agency"];
$report_name = $_POST["report_name"];
$scan_start = $_POST["scan_start"];
$scan_end = $_POST["scan_end"];

switch ($isSort) {
	case "risk":
		$sortOrder = "`nessus_results`.`severity` DESC, `nessus_results`.`cvss_base_score` DESC";
		break;
	case "family":
		$sortOrder = "`nessus_results`.`pluginFamily` ASC, `nessus_results`.`severity` DESC, `nessus_results`.`cvss_base_score` DESC";
		break;
	case "exploit":
		$sortOrder = "`nessus_results`.`exploit_available` DESC, `nessus_results`.`exploit_framework_metasploit` DESC";
		break;
	case "vuln_age":
		$sortOrder = "`nessus_results`.`vuln_publication_date` ASC, `nessus_results`.`severity` DESC, `nessus_results`.`cvss_base_score` DESC";
		break;
	default: /* We shall default to sorting by CVSS score */
		$sortOrder = "`nessus_results`.`cvss_base_score` DESC";
}
date_default_timezone_set('UTC');
$myDir = "/var/www/projectRF/nessus/csvfiles/";
$myFileName = $agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");


$sql = "SELECT DISTINCT
	nessus_results.canvas_package,
	nessus_results.bidList,
	nessus_results.certList,
	nessus_results.cveList,
	nessus_results.cvss_base_score,
	nessus_results.cvss_vector,
	nessus_results.cweList,
	nessus_results.d2_elliot_name,
	nessus_results.description,
	nessus_results.edbList,
	nessus_results.exploitability_ease,
	nessus_results.exploit_available,
	nessus_results.exploit_framework_canvas,
	nessus_results.exploit_framework_core,
	nessus_results.exploit_framework_d2_elliot,
	nessus_results.exploit_framework_metasploit,
	nessus_results.iavaList,
	nessus_results.iavbList,
	nessus_results.metasploit_name,
	nessus_results.msftList,
	nessus_results.osvdbList,
	nessus_results.patch_publication_date,
	nessus_results.pluginFamily,
	nessus_results.pluginID,
	nessus_results.plugin_modification_date,
	nessus_results.pluginName,
	nessus_results.plugin_publication_date,
	nessus_results.risk_factor,
	nessus_results.script_version,
	nessus_results.secuniaList,
	nessus_results.see_also,
	nessus_results.severity,
	nessus_results.solution,
	nessus_results.synopsis,
	nessus_results.vuln_publication_date
FROM
	nessus_results
INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_results.host_name
INNER JOIN nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
WHERE
	nessus_results.agency =  ? AND
	nessus_results.report_name =  ? AND
	nessus_results.scan_start =  ? AND
	nessus_results.scan_end =  ?
ORDER BY $sortOrder
";

$sth = $db->prepare($sql);
$data = array($agency, $report_name, $scan_start, $scan_end);
$results = $db->execute($sth, $data);ifError($results);
fwrite($fh, "\"CVE\",\"CVSS\",\"Risk\",\"Host\",\"Protocol\",\"Port\",\"Name\",\"Synopsis\",\"Description\",\"Solution\",\"See Also\",\"Plugin Output\"\n");
/*CVE
CVSS
Risk
Host
Protocol
Port
Name
Synopsis
Description
Solution
See Also
Plugin Output
*/
while($row = $results->fetchRow(DB_FETCHMODE_ASSOC)){
	$ip_address = $row[ip_addr];
	$os = $row[operating_system];
	$cvss = $row[cvss_base_score];
	$risk_factor = $row[risk_factor];
	$pluginName = $row[pluginName];
	$pluginName = str_replace("&lt;", "<", $pluginName);
	if($risk_factor == "Critical"){$allCriticalCVEs = $allCriticalCVEs . $row[cveList];}
	if($risk_factor == "High"){$allHighCVEs = $allHighCVEs . $row[cveList];}
	if($risk_factor == "Medium"){$allMediumCVEs = $allMediumCVEs . $row[cveList];}
	$cveArray = explode(",",$row[cveList]);
	
	fwrite($fh, "\"$ip_address\",\"$os\",\"$cvss\",\"$risk_factor\",\"$cveArray[1]\n$cveArray[2]\n$cveArray[3]\n$cveArray[4]\n$cveArray[5]\",\"$pluginName\"\n");
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
<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$agency_report_array = explode(":", $_POST["agency_report"]);
$agency = $agency_report_array[0];
$report_name = $agency_report_array[1];
$scan_start = $agency_report_array[2];
$scan_end = $agency_report_array[3];

date_default_timezone_set('UTC');
$myDir = "/var/www/projectRF/nessus/csvfiles/";
$myFileName = $agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");

$sql = "SELECT
	nessus_tags.ip_addr,
	nessus_tags.operating_system,
	nessus_results.cvss_base_score,
	nessus_results.risk_factor,
	nessus_results.cveList,
	nessus_results.pluginName,
	nessus_results.report_name,
	nessus_results.exploit_available,
	nessus_results.exploitability_ease,
	nessus_results.plugin_type,
	nessus_results.exploit_framework_metasploit,
	nessus_results.metasploit_name,
	nessus_results.exploit_framework_canvas,
	nessus_results.canvas_package,
	nessus_results.exploit_framework_core,
	nessus_results.exploit_framework_d2_elliot,
	nessus_results.d2_elliot_name
FROM
	nessus_results
	Inner Join nessus_tags ON nessus_tags.tagID = nessus_results.tagID
WHERE
	nessus_results.agency =  ? AND
	nessus_results.report_name =  ? AND
	nessus_results.scan_start =  ? AND
	nessus_results.scan_end =  ? AND
	nessus_results.cvss_base_score >  '0'
ORDER BY
	nessus_results.cvss_base_score DESC,
	nessus_results.pluginName ASC
";

$stmt = $db->prepare($sql);
$data = array($agency, $report_name, $scan_start, $scan_end);
$stmt->execute($data);
fwrite($fh, "\"IP Address\",\"OS\",\"CVSS\",\"Risk\",\"CVE\",\"Name\"\n");

$cveStats = array();
$cveStatsTotals = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	$ip_address = $row[ip_addr];
	$os = $row[operating_system];
	$os = str_replace("Enterprise", "Ent", $os);
	$os = str_replace("Standard", "Std", $os);
	$os = str_replace("Service Pack", "SP", $os);
	$os = str_replace("Microsoft", "", $os);
	$os = str_replace("Edition", "Ed", $os);
	$os = str_replace("(English)", "", $os);
	$cvss = $row[cvss_base_score];
	$risk_factor = $row[risk_factor];
	$pluginName = $row[pluginName];
	$pluginName = str_replace("&lt;", "<", $pluginName);	
	$cveArray = array_filter(explode(",",$row[cveList]));
	
	if(isset($cveStats[$ip_address][$risk_factor])){
		$cveStats[$ip_address][$risk_factor] = array_unique(array_merge($cveStats[$ip_address][$risk_factor],$cveArray), SORT_REGULAR);
		$cveStatsTotals[Total][$risk_factor] = array_unique(array_merge($cveStats[$ip_address][$risk_factor],$cveArray), SORT_REGULAR);
	} else {
		$cveStats[$ip_address][$risk_factor] = $cveArray;
		$cveStatsTotals[Total][$risk_factor] = $cveArray;
	}
	
	fwrite($fh, "\"$ip_address\",\"$os\",\"$cvss\",\"$risk_factor\",\"$cveArray[1]\n$cveArray[2]\n$cveArray[3]\n$cveArray[4]\n$cveArray[5]\",\"$pluginName\"\n");
}
fwrite($fh, "\"IP Address\",\"Critical\",\"High\",\"Medium\"\n");
foreach($cveStats as $ip_addr => $subarray){
	$criticalTotal = count($subarray[Critical]);
	$highTotal = count($subarray[High]);
	$mediumTotal = count($subarray[Medium]);
	$lowTotal = count($subarray[Low]);
	fwrite($fh, "\"$ip_addr\",\"$criticalTotal\",\"$highTotal\",\"$mediumTotal\",\"$lowTotal\"\n");
}
$criticalTotal = count($cveStatsTotals[Total][Critical]);
$highTotal = count($cveStatsTotals[Total][High]);
$mediumTotal = count($cveStatsTotals[Total][Medium]);
$lowTotal = count($cveStatsTotals[Total][Low]);
fwrite($fh, "\"Total\",\"$criticalTotal\",\"$highTotal\",\"$mediumTotal\",\"$lowTotal\"\n");

?>
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title>NESSUS REPORT FOR TECHNICAL</title>
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
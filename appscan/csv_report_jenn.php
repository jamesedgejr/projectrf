<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbUrl/$dbname" );ifError($db);

$Url = $_POST["Url"];
$sql = "CREATE temporary TABLE appscan_tmp_url (Url VARCHAR(255))";
$result = $db->query($sql);ifError($result);
foreach ($Url as $u){
	$sql="INSERT INTO appscan_tmp_url (Url) VALUES ('$u')";
	$result = $db->query($sql);ifError($result);
}
$threat = $_POST["threat"];
$sql = "CREATE temporary TABLE appscan_tmp_threat (threatClassification_name VARCHAR(255))";
$result = $db->query($sql);ifError($result);
foreach ($threat as $th){
	$sql="INSERT INTO appscan_tmp_threat (threatClassification_name) VALUES ('$th')";
	$result = $db->query($sql);ifError($result);	
}
$agency = $_POST["agency"];
$report_name = $_POST["report_name"];
	
$high = $_POST["high"];
$medium = $_POST["medium"];
$low  = $_POST["low"];
$info = $_POST["info"];
$sArray = array($high, $medium, $low, $info);
$sql = "CREATE temporary TABLE appscan_tmp_severity (severity VARCHAR(255))";
$result = $db->query($sql);
ifError($result);

foreach ($sArray as $s){
		$sql="INSERT INTO appscan_tmp_severity (severity) VALUES ('$s')";
		$result = $db->query($sql);	ifError($result);
}

date_default_timezone_set('UTC');
$myDir = "csvfiles/";
$myFileName = $agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions for the csvfiles folder.");
$severities = array("High", "Medium", "Low", "Informational");
$distinct_vulns = array();
$total_vulns = array();
$sql = 	"SELECT DISTINCT
			AppScan_IssueTypes.threatClassification_name,
			AppScan_Issues.Url,
			AppScan_Issues.Difference
		FROM
			appscan_tmp_url
		INNER JOIN AppScan_Issues ON appscan_tmp_url.Url = AppScan_Issues.Url
		INNER JOIN AppScan_IssueTypes ON AppScan_IssueTypes.IssueType_ID = AppScan_Issues.Issue_IssueTypeID
		INNER JOIN appscan_tmp_severity ON appscan_tmp_severity.severity = AppScan_IssueTypes.Severity
		INNER JOIN appscan_tmp_threat ON appscan_tmp_threat.threatClassification_name = AppScan_IssueTypes.threatClassification_name
		WHERE AppScan_IssueTypes.Severity = ?
		ORDER BY
			AppScan_IssueTypes.Severity_number DESC,
			AppScan_IssueTypes.threatClassification_name ASC,
			AppScan_Issues.Url ASC
		";
fwrite($fh, "\"Severity\",\"Threat Classification\",\"URL\",\"Parameter\"");
fwrite($fh, "\n");
foreach($severities as $severity){

	$flipped_data =& $db->getAll($sql, array("$severity"), DB_FETCHMODE_ORDERED | DB_FETCHMODE_FLIPPED);ifError($data);
	$data =& $db->getAll($sql, array("$severity"), DB_FETCHMODE_ASSOC);ifError($data);
	
	$threats = array_unique($flipped_data[0]);
	$threat_count = array_count_values($flipped_data[0]);
	$total_urls_array = array();
	$distinct_vulns[$severity] = count($threats);
	$total_vulns[$severity] = array_sum($threat_count);
	foreach($threats as $t){
		for($i=0;$i<count($data);$i++){
			if($data[$i]["threatClassification_name"] == $t){
				$difference_temp1 = explode('-&gt;',$data[$i]["Difference"]);
				$difference_temp2 = explode('=', $difference_temp1[1]);
				$parameter = ltrim((empty($difference_temp2[0])) ? "NOPARAM":$difference_temp2[0]);
				$threat = $data[$i]["threatClassification_name"];
				$url = $data[$i]["Url"];
				$total_urls_array[] = $url;
				fwrite($fh, "\"$severity\",\"$threat\",\"$url\",\"$parameter\"");
				fwrite($fh, "\n");
			}
		}
		$total_urls = count(array_unique($total_urls_array));
		fwrite($fh, "\"\",\"Total $t:  $threat_count[$t]\",\"$total_urls Vulnerable Pages\",\"\"");
		fwrite($fh, "\n");
		$total_urls_array = array();
	}
}

fwrite($fh, $distinct_vulns['High'] . " Distinct High Severity Level Vulnerabilities");
fwrite($fh, "\n");
fwrite($fh, $distinct_vulns['Medium'] . " Distinct Medium Severity Level Vulnerabilities");
fwrite($fh, "\n");
fwrite($fh, $distinct_vulns['Low'] . " Distinct Low Severity Level Vulnerabilities");
fwrite($fh, "\n");
fwrite($fh, $distinct_vulns['Informational'] . " Distinct Informational Severity Level Vulnerabilities");
fwrite($fh, "\n");
fwrite($fh, array_sum($distinct_vulns) . " Distinct Vulnerabilities");
fwrite($fh, "\n");

fwrite($fh, $total_vulns['High'] . " Total High Severity Level Vulnerabilities");
fwrite($fh, "\n");
fwrite($fh, $total_vulns['Medium'] . " Total Medium Severity Level Vulnerabilities");
fwrite($fh, "\n");
fwrite($fh, $total_vulns['Low'] . " Total Low Severity Level Vulnerabilities");
fwrite($fh, "\n");
fwrite($fh, $total_vulns['Informational'] . " Total Informational Severity Level Vulnerabilities");
fwrite($fh, "\n");
fwrite($fh, array_sum($total_vulns) . " Total Vulnerabilities");
fwrite($fh, "\n");


?>
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title>IBM APPSCAN CSV VULNERABILITY REPORT</title>
<link rel="stylesheet" type="text/css" href="../main/style_nessus.css" />
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

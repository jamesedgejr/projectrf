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
$sql = 	"SELECT
			AppScan_Issues.agency,
			AppScan_Issues.XmlReport_Name,
			AppScan_IssueTypes.advisory_name,
			AppScan_IssueTypes.threatClassification_name,
			AppScan_IssueTypes.Severity,
			AppScan_Issues.Url,
			AppScan_Issues.Difference,
			AppScan_Issues.OriginalHttpTraffic,
			AppScan_Issues.Validation_String,
			AppScan_Issues.Reasoning
		FROM
			appscan_tmp_url
		INNER JOIN AppScan_Issues ON appscan_tmp_url.Url = AppScan_Issues.Url
		INNER JOIN AppScan_IssueTypes ON AppScan_IssueTypes.IssueType_ID = AppScan_Issues.Issue_IssueTypeID
		INNER JOIN appscan_tmp_severity ON appscan_tmp_severity.severity = AppScan_IssueTypes.Severity
		INNER JOIN appscan_tmp_threat ON appscan_tmp_threat.threatClassification_name = AppScan_IssueTypes.threatClassification_name
		ORDER BY
			AppScan_IssueTypes.Severity ASC,
			AppScan_IssueTypes.threatClassification_name ASC,
			AppScan_IssueTypes.advisory_name ASC,
			AppScan_Issues.Url ASC
		";
$results = $db->query($sql);ifError($results);
fwrite($fh, "\"Threat Classification\",\"Advisory Name\",\"Severity\",\"Scanner\",\"URL\",\"Parameter\",\"Validation String\"");
fwrite($fh, "\n");
while($row = $results->fetchRow(DB_FETCHMODE_ASSOC)){
	$advisory_name = $row["advisory_name"];
	$threatClassification_name = $row["threatClassification_name"];
	$severity = $row["Severity"];
	$url = $row["Url"];
	$difference_temp1 = split('->',$row["Difference"]);
	$difference_temp2 = split('=', $difference_temp1[1]);
	$parameter = $difference_temp2[0];
	$validation_string = $row["Validation_String"];
	$reasoning = $row["Reasoning"];
	
	fwrite($fh, "\"$threatClassification_name\",\"$advisory_name\",\"$severity\",\"AppScan\",\"$url\",\"$parameter\",\"$validation_string\"");
	fwrite($fh, "\n");
}
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

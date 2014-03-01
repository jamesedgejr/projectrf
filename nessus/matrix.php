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

$agency = $_POST["agency"];
$report_name = $_POST["report_name"];
$scan_start = $_POST["scan_start"];
$scan_end = $_POST["scan_end"];
$isPlugName = ($_POST["isPlugName"] == "y") ? "y" : "n";
$isPlugFam = ($_POST["isPlugFam"] == "y") ? "y" : "n";
$isSort = $_POST["isSort"];
$pivot = $_POST["pivot"];
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

date_default_timezone_set('UTC');
$myDir = "/var/www/projectRF/nessus/csvfiles/";
$myFileName = $agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");

//hosts are left side with vulnerabilities across the top
if($pivot == "left"){
	fwrite($fh, "\"\",\"\",\"\",");
	$sql = "SELECT DISTINCT
				nessus_results.pluginID,
				nessus_results.pluginName,
				nessus_results.pluginFamily,
				nessus_results.severity,
				nessus_results.cvss_base_score
			FROM
				nessus_results
			INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_results.host_name
			INNER JOIN nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
			INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
			WHERE
				(nessus_results.agency = ? AND nessus_results.report_name = ?)
			ORDER BY $sortOrder
			";
	$data = array($agency, $report_name);
	$sth = $db->prepare($sql);
	$results = $db->execute($sth, $data);ifError($results);
	//$pluginData =& $db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
	print_r($results);
	while($row = $results->fetchRow(DB_FETCHMODE_ASSOC)){
	//foreach($pluginData as $pD){
		$pluginName = $row["pluginName"];
		if($isPlugName == "y"){
			fwrite($fh, "\"$pluginName\"");
		}
		fwrite($fh, ",");
	}
	fwrite($fh, "\n");
	
	fwrite($fh, "\"\",\"\",\"\",");
	foreach($pluginData as $pD){
		$pluginFamily = $row["pluginFamily"];
		if($isPlugFam == "y"){
			fwrite($fh, "\"$pluginFamily\"");
		}
		fwrite($fh, ",");
	}
	fwrite($fh, "\n");
	fwrite($fh, "\"\",\"\",\"\",");
	$result = $db->query($sql);
	foreach($pluginData as $pD){
		$cvss_base_score = $row["cvss_base_score"];
		$severity = $row["severity"];
		if ($severity == "4"){
			$risk = "Critical";
		} else if ($severity == "3") {
			$risk = "High";
		} else if ($severity == "2") {
			$risk = "Medium";
		} else if ($severity == "1") {
			$risk = "Low";
		} else if($severity == "0"){
			$risk = "Information";
		}	
		fwrite($fh, "\"$risk\",");
	}
	fwrite($fh, "\n");
	
	foreach($host as $h){
		fwrite($fh, "\"$h\",");
		//I think having the fqdn and netbios (if available) would be nice!

		
		
		
		$host_sql = "SELECT DISTINCT
						nessus_tags.fqdn,
						nessus_tags.operating_system
					FROM
						nessus_results
					INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
					INNER JOIN nessus_tmp_family ON nessus_results.pluginFamily = nessus_tmp_family.pluginFamily
					INNER JOIN nessus_tmp_hosts ON nessus_results.host_name = nessus_tmp_hosts.host_name
					WHERE
						nessus_results.host_name = '$h' AND
						nessus_results.agency = '$agency' AND
						nessus_results.report_name = '$report_name'
		";
		$host_results = $db->query($host_sql);ifError($host_results);
		$host_row = $host_results->fetchRow(DB_FETCHMODE_ASSOC);
		$fqdn = $host_row["fqdn"];
		$operating_system = $host_row["operating_system"];
		fwrite($fh, "\"$fqdn\",\"$operating_system\",");
		foreach($pluginData as $pD){
			$pluginID = $row["pluginID"];
			$lookup_sql = 	"SELECT DISTINCT
								nessus_results.pluginID
							FROM
								nessus_results
							INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_results.host_name
							INNER JOIN nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
							INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
							WHERE
								nessus_results.host_name = '$h' AND
								nessus_results.agency = '$agency' AND
								nessus_results.report_name = '$report_name' AND
								nessus_results.pluginID = '$pluginID'
							";
			$lookup_results = $db->query($lookup_sql);ifError($lookup_results);
			$num = $lookup_results->numRows();
			if($num == 0){
				//IF NUM = 0 THEN NESSUS DID NOT FIND THIS ip VULNERABLE TO THIS PLUGIN
				fwrite($fh, "\"\",");
			}
			else {
				fwrite($fh, "\"X\",");
			}
		}//end foreach
		fwrite($fh, "\n");
	}//end OF HOST FOREACH
} else {//end pivot left
//hosts are across the top and vulnerabilities are along the left side

	if($isPlugFam == "y" && $isPlugName == "y"){
		$spacer = "\"\",\"\",\"\",";
	} elseif ($isPlugFam == "n" && $isPlugName == "y"){
		$spacer = "\"\",\"\",";
	} elseif ($isPlugFam == "y" && $isPlugName == "n"){
		$spacer = "\"\",\"\",";
	} else {
		echo "You need to select either Plugin Name, Plugin Family, or both.  Deselecting both of them will produce a really useless report.";
		exit;
	}
	$hostArray = array();
	$sql = "SELECT DISTINCT
				nessus_results.host_name,
				nessus_tags.fqdn,
				nessus_tags.operating_system
			FROM
				nessus_results
			INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
			INNER JOIN nessus_tmp_family ON nessus_results.pluginFamily = nessus_tmp_family.pluginFamily
			INNER JOIN nessus_tmp_hosts ON nessus_results.host_name = nessus_tmp_hosts.host_name
			WHERE
				nessus_results.agency = '$agency' AND 
				nessus_results.report_name = '$report_name' AND
				nessus_results.scan_start = '$scan_start' AND
				nessus_results.scan_end = '$scan_end'
			ORDER BY nessus_results.host_name ASC 
			";
	$hostData =& $db->getAll($sql, array(), DB_FETCHMODE_ASSOC);ifError($hostData);
	fwrite($fh, $spacer);
	fwrite($fh, "\"Host Name\",");
	foreach($hostData as $hD){
		$host_name = $hD["host_name"];
		fwrite($fh, "\"$host_name\",");
	}
	fwrite($fh, "\n");
	fwrite($fh, $spacer);
	fwrite($fh, "\"FQDN\",");
	foreach($hostData as $hD){
		$fqdn = $hD["fqdn"];
		fwrite($fh, "\"$fqdn\",");
	}
	fwrite($fh, "\n");
	fwrite($fh, $spacer);
	fwrite($fh, "\"Operating System\",");
	foreach($hostData as $hD){
		$operating_system = $hD["operating_system"];
		fwrite($fh, "\"$operating_system\",");
	}
	fwrite($fh, "\n\n");
	if($isPlugFam == "y" && $isPlugName == "y"){
		fwrite($fh, "\"Plugin Family\",\"Plugin Name\",\"Severity\",");
	} elseif ($isPlugFam == "n" && $isPlugName == "y"){
		fwrite($fh, "\"Plugin Name\",\",\"Severity\",");
	} elseif ($isPlugFam == "y" && $isPlugName == "n"){
		fwrite($fh, "\"Plugin Family\",\"Severity\",");
	} else {
		echo "You need to select either Plugin Name, Plugin Family, or both.  Deselecting both of them will produce a really useless report.";
		exit;
	}
	fwrite($fh, "\n");
	$sql = "SELECT DISTINCT
				nessus_results.pluginID,
				nessus_results.pluginName,
				nessus_results.pluginFamily,
				nessus_results.cvss_base_score,
				nessus_results.severity
			FROM
				nessus_results
			INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_results.host_name
			INNER JOIN nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
			INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
			WHERE
				nessus_results.agency = '$agency' AND 
				nessus_results.report_name = '$report_name' AND
				nessus_results.scan_start = '$scan_start' AND
				nessus_results.scan_end = '$scan_end'
			ORDER BY $sortOrder
			";
	$pluginData =& $db->getAll($sql, array(), DB_FETCHMODE_ASSOC);ifError($pluginData);
	foreach ($pluginData as $pD){
		$pluginID = $row["pluginID"];
		$pluginName = $row["pluginName"];
		$pluginFamily = $row["pluginFamily"];
		if($isPlugFam == "y" && $isPlugName == "y"){
			fwrite($fh, "\"$pluginFamily\",\"$pluginName\",");
		} elseif ($isPlugFam == "n" && $isPlugName == "y"){
			fwrite($fh, "\"$pluginName\",\",");
		} elseif ($isPlugFam == "y" && $isPlugName == "n"){
			fwrite($fh, "\"$pluginFamily\",");
		} else {
			echo "You need to select either Plugin Name, Plugin Family, or both.  Deselecting both of them will produce a really useless report.";
			exit;
		}
		$cvss_base_score = $row["cvss_base_score"];
		$severity = $row["severity"];
		if ($cvss_base_score == "10.0"){
			$risk = "Critical";
		} else if ($severity == "3") {
			$risk = "High";
		} else if ($severity == "2") {
			$risk = "Medium";
		} else if ($severity == "1") {
			$risk = "Low";
		} else if($severity == "0"){
			$risk = "Information";
		}	
		fwrite($fh, "\"$risk\",\"\",");
		//we need to lookup and find if the host is vulnerable to the vulnerability
		foreach($hostData as $hD){
			$host_name = $hD["host_name"];
			$lookup_sql = "SELECT DISTINCT
						nessus_results.pluginID
					FROM
						nessus_results
					INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_results.host_name
					INNER JOIN nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
					INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
					WHERE
						nessus_results.host_name = '$host_name' AND
						nessus_results.agency = '$agency' AND
						nessus_results.report_name = '$report_name' AND
						nessus_results.pluginID = '$pluginID' AND
						nessus_results.scan_start = '$scan_start' AND
						nessus_results.scan_end = '$scan_end'		
			";
			$lookup_results = $db->query($lookup_sql);ifError($lookup_results);
			$num = $lookup_results->numRows();
			if($num == 0){
				//IF NUM = 0 THEN NESSUS DID NOT FIND THIS ip VULNERABLE TO THIS PLUGIN
				fwrite($fh, "\"\",");
			}
			else {
				fwrite($fh, "\"X\",");
			}
		}
		fwrite($fh, "\n");
		
	}
}


?>
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title>NESSUS VULNERABILITY MATRIX</title>
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

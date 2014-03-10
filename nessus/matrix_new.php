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
$pivot = $_POST["pivot"];

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
	$sql1 = "SELECT DISTINCT
				nessus_results.pluginID,
				nessus_results.pluginName,
				nessus_results.pluginFamily,
				nessus_results.severity,
				nessus_results.cvss_base_score
			FROM
				nessus_results
			INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
			INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
			INNER JOIN nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
			INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
			WHERE
				(nessus_results.agency = ? AND nessus_results.report_name = ?)
			";
	$data1 = array($agency, $report_name);
	$sth1 = $db->prepare($sql1);
	$results1 = $db->execute($sth1, $data1);ifError($results1);
	//$pluginData =& $db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
	while($row = $results1->fetchRow(DB_FETCHMODE_ASSOC)){
	//foreach($pluginData as $pD){
		$pluginName = $row["pluginName"];
		if($isPlugName == "y"){
			fwrite($fh, "\"$pluginName\"");
		}
		fwrite($fh, ",");
	}
	fwrite($fh, "\n");
	fwrite($fh, "\"\",\"\",\"\",");
	while($row = $results1->fetchRow(DB_FETCHMODE_ASSOC)){
	//foreach($pluginData as $pD){
		$pluginFamily = $row["pluginFamily"];
		if($isPlugFam == "y"){
			fwrite($fh, "\"$pluginFamily\"");
		}
		fwrite($fh, ",");
	}
	fwrite($fh, "\n");
	fwrite($fh, "\"\",\"\",\"\",");
	$result = $db->query($sql);
	//foreach($pluginData as $pD){
	while($row = $results->fetchRow(DB_FETCHMODE_ASSOC)){
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
					INNER JOIN nessus_tmp_hosts ON nessus_tags.host_name = nessus_tmp_hosts.host_name
					WHERE
						nessus_results.host_name = '$h' AND
						nessus_results.agency = '$agency' AND
						nessus_results.report_name = '$report_name'
		";
		$sth = $db->prepare($sql);
		$data 
		$host_results = $db->query($host_sql);ifError($host_results);
		$host_row = $host_results->fetchRow(DB_FETCHMODE_ASSOC);
		$fqdn = $host_row["fqdn"];
		$operating_system = $host_row["operating_system"];
		fwrite($fh, "\"$fqdn\",\"$operating_system\",");
		//foreach($pluginData as $pD){
		while($row = $results->fetchRow(DB_FETCHMODE_ASSOC)){
			$pluginID = $row["pluginID"];
			$lookup_sql = 	"SELECT DISTINCT
								nessus_results.pluginID
							FROM
								nessus_results
							INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
							INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
							INNER JOIN nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
							INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
							WHERE
								nessus_tags.host_name = '$h' AND
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
}
<?php

include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

$agency = $_POST["agency"];
$report_name = $_POST["report_name"];
$scan_start = $_POST["scan_start"];
$scan_end = $_POST["scan_end"];
/*
foreach($db->query('SELECT * FROM table') as $row) {
    echo $row['field1'].' '.$row['field2']; //etc...
}

$stmt = $db->query('SELECT * FROM table');
 
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['field1'].' '.$row['field2']; //etc...
}
*/
	$sql = "SELECT DISTINCT
				nessus_results.pluginID,
				nessus_results.pluginName,
				nessus_results.pluginFamily,
				nessus_results.severity,
				nessus_results.risk_factor,
				nessus_results.cvss_base_score
			FROM
				nessus_results
			WHERE
				nessus_results.agency =  ? AND
				nessus_results.report_name =  ? AND
				nessus_results.scan_start =  ? AND
				nessus_results.scan_end =  ?
			";
$stmt = $db->prepare($sql);
$stmt->execute(array($agency, $report_name, $scan_start, $scan_end));
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

	echo $row["pluginID"] . "<br>";

}
?>
<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);



$nessus_report = explode(":@:@:", $_POST["nessus_report"]);
$v1 = new Valitron\Validator($nessus_report);
$v1->rule('slug', '0');//validate agency
$v1->rule('regex','1', '/^([\w\s_.\[\]():;@-])+$/'); //regex includes alpha/numeric, space, underscore, dash, period, white space, brackets, parentheses, colon, "at" symbol, and semi-colon
$v1->rule('numeric',['2','3']);//validate scan_start and scan_end
if(!$v1->validate()) {
	print_r($v1->errors());
	exit;
} 
$nessus_agency = $nessus_report[0];
$report_name = $nessus_report[1];
$scan_start = $nessus_report[2];
$scan_end = $nessus_report[3];

$nmap_report = explode(":@:@:", $_POST["nmap_report"]);
$v2 = new Valitron\Validator($nmap_report);
$v2->rule('slug', '0');//validate agency
$v2->rule('regex','1','/^([\w _.-])+$/');// validate filename
$v2->rule('numeric',['2','3']);//validate nmaprun_start and finished_time
if(!$v2->validate()) {
    print_r($v2->errors());
	exit;
} 
$nmap_agency = $nmap_report[0];
$filename = $nmap_report[1];
$nmaprun_start = $nmap_report[2];
$finished_time = $nmap_report[3];


$nessus_sql = 	"SELECT
					nessus_tags.ip_addr,
					nessus_tags.mac_addr,
					nessus_tags.fqdn,
					nessus_tags.netbios,
					nessus_tags.operating_system,
					nessus_results.port,
					nessus_results.protocol,
					nessus_results.plugin_output
				FROM
					nessus_tags
					Inner Join nessus_results ON nessus_results.tagID = nessus_tags.tagID
				WHERE
					nessus_results.agency = ? AND 
					nessus_results.report_name = ? AND
					nessus_results.scan_start = ? AND
					nessus_results.scan_end = ?
					nessus_results.pluginFamily =  'Service detection'
				";
$nessus_data = array($nessus_agency, $report_name, $scan_start, $scan_end);
$nessus_stmt = $db->prepare($nessus_sql);
$nessus_stmt->execute($nessus_data);

date_default_timezone_set('UTC');
$myDir = getcwd() . "/csvfiles/";
$myFileName = $nessus_agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");

$header = array("IP Address","NMAP FQDN","NESSUS FQDN","NESSUS NETBIOS","NMAP MAC","NESSUS MAC","NMAP OS","NESSUS OS","NMAP PRODUCT/VERSION","NMAP SERVICE","NMAP PORT/PROTOCOL","NMAP PORT STATE","NESSUS PORT/PROTOCOL","NESSUS PLUGIN OUTPUT");
fputcsv($fh, $header);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title>CSV FILE</title>
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
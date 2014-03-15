<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

#create temporary table a(a int primary key, b char(1)) engine=innodb;
#create index b on a (b);

$host = $_POST["host"];
foreach($host as $key => $value) {
	if ($value == "dlskeaAKEJFDAKE") unset($host[$key]);
}
$sql = "CREATE temporary TABLE nmap_tmp_hosts (address_addr VARCHAR(15))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($host as $h){
	$sql="INSERT INTO nmap_tmp_hosts (address_addr) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($h));
}
$nse = $_POST["nse"];
foreach($nse as $key => $value) {
	if ($value == "dlskeaAKEJFDAKE") unset($nse[$key]);
}
$sql = "CREATE temporary TABLE nmap_tmp_port_nse (script_id VARCHAR(255) )";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($nse as $n){
	$nseArray = explode(":", $n);
	if($nseArray[0] == "Port"){
		$sql="INSERT INTO nmap_tmp_port_nse (script_id) VALUES (?)";
		$stmt = $db->prepare($sql);
		$stmt->execute(array($nseArray[1]));
	}
}
$sql = "CREATE temporary TABLE nmap_tmp_host_nse (script_id VARCHAR(255) )";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($nse as $n){
	$nseArray = explode(":", $n);
	if($nseArray[0] == "Host"){
		$sql="INSERT INTO nmap_tmp_host_nse (script_id) VALUES (?)";
		$stmt = $db->prepare($sql);
		$stmt->execute(array($nseArray[1]));
	}
}
$ports = $_POST["ports"];
foreach($ports as $key => $value) {
	if ($value == "dlskeaAKEJFDAKE") unset($ports[$key]);
}
$sql = "CREATE temporary TABLE nmap_tmp_ports (port_portid VARCHAR(255), port_service_name VARCHAR(255) )";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($ports as $p){
	$portsArray = explode(":", $p);
	$sql="INSERT INTO nmap_tmp_ports (port_portid, port_service_name) VALUES (?,?)";
	$stmt = $db->prepare($sql);
	$stmt->execute($portsArray[0],$portsArray[1]);
}
$agency = $_POST["agency"];
$filename = $_POST["filename"];
$nmaprun_start = $_POST["nmaprun_start"];
$finished_time = $_POST["finished_time"];
$pivot = $_POST["pivot"];
	
$open = $_POST["isOpen"];
$closed = $_POST["isClosed"];
$filtered  = $_POST["isFiltered"];
$sArray = array($open, $closed, $filtered);
$sql = "CREATE temporary TABLE nmap_tmp_portState (portState VARCHAR(255))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($sArray as $sA){
	$sql="INSERT INTO nmap_tmp_portState (portState) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($sA));
}

date_default_timezone_set('UTC');
$myDir = "/var/www/projectRF/nmap/csvfiles/";
$myFileName = $agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");


if($pivot == "left"){
	fwrite($fh, "\"DOMAIN NAME\",\"IP\",\"HOST UP\",\"HOST DOWN\",\"INDUSTRY\",\"GEO LAT\",\"GEO LONG\",\"SERVER TYPE\",\"SERVER VERSION\",\"TRACE\",\"GOOGLE MALWARE\",\"SSL OPEN\",\"SSLv2\",\"SSH OPEN\",\"SSHv1\",\"MS-TERM-SVC\"\n");
	$sql = "SELECT
				nmap_hosts_xml.hostname_name,
				nmap_hosts_xml.address_addr,
				nmap_hosts_xml.status_state,
				nmap_nse_xml.script_type,
				nmap_nse_xml.script_id,
				nmap_nse_xml.script_output,
				nmap_ports_xml.port_portid,
				nmap_ports_xml.port_state,
				nmap_ports_xml.port_service_name,
				nmap_ports_xml.port_service_product,
				nmap_ports_xml.port_service_version
			FROM
				nmap_runstats_xml
			INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
			INNER JOIN nmap_tmp_hosts ON nmap_tmp_hosts.address_addr = nmap_hosts_xml.address_addr
			INNER JOIN nmap_ports_xml ON nmap_ports_xml.host_id = nmap_hosts_xml.id
			INNER JOIN nmap_tmp_ports ON nmap_tmp_ports.port_portid = nmap_ports_xml.port_portid AND nmap_tmp_ports.port_service_name = nmap_ports_xml.port_service_name
			INNER JOIN nmap_nse_xml ON nmap_nse_xml.host_or_port_id = nmap_ports_xml.id
			INNER JOIN nmap_tmp_nse ON nmap_tmp_nse.script_type = nmap_nse_xml.script_type AND nmap_tmp_nse.script_id = nmap_nse_xml.script_id
			INNER JOIN nmap_tmp_portState ON nmap_tmp_portState.portState = nmap_ports_xml.port_state
			WHERE
				nmap_runstats_xml.agency = '$agency' AND
				nmap_runstats_xml.filename = '$filename' AND
				nmap_runstats_xml.nmaprun_start = '$nmaprun_start' AND
				nmap_runstats_xml.finished_time = '$finished_time'
			";
	echo $sql . "<hr>";
	$result = $db->query($sql);ifDBError($result);
	while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$address_addr = $row["address_addr"];
		fwrite($fh, "\"$address_addr\",\n");
	}

}
?>
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title>nmap VULNERABILITY MATRIX</title>
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

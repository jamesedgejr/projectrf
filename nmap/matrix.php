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
	$array = array($h);
	$sql="INSERT INTO nmap_tmp_hosts (address_addr) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute($array);
}
$nsePort = $_POST["nsePort"];
foreach($nsePort as $key => $value) {
	if ($value == "dlskeaAKEJFDAKE") unset($nsePort[$key]);
}
$sql = "CREATE temporary TABLE nmap_tmp_port_nse (script_id VARCHAR(255) )";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($nsePort as $nP){
	$array = array($nP);
	$sql="INSERT INTO nmap_tmp_port_nse (script_id) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute($array);
}
$nseHost = $_POST["nseHost"];
foreach($nseHost as $key => $value) {
	if ($value == "dlskeaAKEJFDAKE") unset($nseHost[$key]);
}
$sql = "CREATE temporary TABLE nmap_tmp_host_nse (script_id VARCHAR(255) )";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($nseHost as $nH){
	$array = array($nH);
	$sql="INSERT INTO nmap_tmp_host_nse (script_id) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute($array);
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
	$array = array($portsArray[0],$portsArray[1]);
	$sql="INSERT INTO nmap_tmp_ports (port_portid, port_service_name) VALUES (?,?)";
	$stmt = $db->prepare($sql);
	$stmt->execute($array);
}
$agency = $_POST["agency"];
$filename = $_POST["filename"];
$nmaprun_start = $_POST["nmaprun_start"];
$finished_time = $_POST["finished_time"];
$nsescript = $_POST["nsescript"];

$isUp = $_POST["isUp"];
$isDown = $_POST["isDown"];	
$isOpen = $_POST["isOpen"];
$isClosed = $_POST["isClosed"];
$isFiltered  = $_POST["isFiltered"];
$isOpenFiltered  = $_POST["isOpenFiltered"];
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


fwrite($fh, "\"HOST STATE\",\"IP\",\"DOMAIN NAME\",\"PORT/PROTOCOL\",\"SERVICE\",\"PRODUCT\",\"VERSION\",\"PORT STATE\",\"SCRIPT ID\",\"SCRIPT OUTPUT\",\n");
if($isOpen && $isUp){
	$main_sql = "SELECT
		nmap_runstats_xml.agency,
		nmap_runstats_xml.filename,
		nmap_runstats_xml.nmaprun_start,
		nmap_runstats_xml.finished_time,
		nmap_hosts_xml.hostname_name,
		nmap_hosts_xml.address_addr,
		nmap_hosts_xml.status_state,
		nmap_ports_xml.port_protocol,
		nmap_ports_xml.port_portid,
		nmap_ports_xml.port_state,
		nmap_ports_xml.port_service_name,
		nmap_ports_xml.port_service_product,
		nmap_ports_xml.port_service_version,
		nmap_ports_xml.port_service_extrainfo";

	if($nsescript == "port"){
		$main_sql .= ",
			nmap_port_nse_xml.script_id,
			nmap_port_nse_xml.script_output";
	} else {
		$main_sql .= ",
			nmap_host_nse_xml.script_id,
			nmap_host_nse_xml.script_output";
	}
	$main_sql .= "
		FROM
			nmap_runstats_xml
		Inner Join nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
		Left Join nmap_ports_xml ON nmap_ports_xml.host_id = nmap_hosts_xml.id
		Inner Join nmap_tmp_hosts ON nmap_tmp_hosts.address_addr = nmap_hosts_xml.address_addr
		Inner Join nmap_tmp_ports ON nmap_tmp_ports.port_portid = nmap_ports_xml.port_portid AND nmap_tmp_ports.port_service_name = nmap_ports_xml.port_service_name
	";
	if($nsescript == "port"){
		$main_sql .= "	Left Join nmap_port_nse_xml ON nmap_ports_xml.id = nmap_port_nse_xml.port_id
						Inner Join nmap_tmp_port_nse ON nmap_tmp_port_nse.script_id = nmap_port_nse_xml.script_id";
	} else {
		$main_sql .= "	Left Join nmap_host_nse_xml ON nmap_ports_xml.host_id = nmap_host_nse_xml.host_id
						Inner Join nmap_tmp_host_nse ON nmap_tmp_host_nse.script_id = nmap_host_nse_xml.script_id";
	}
	$main_sql .= "
		WHERE
			nmap_runstats_xml.agency =  ? AND
			nmap_runstats_xml.filename =  ? AND
			nmap_runstats_xml.nmaprun_start =  ? AND
			nmap_runstats_xml.finished_time =  ?";

	$data = array($agency, $filename, $nmaprun_start, $finished_time);
	$main_stmt = $db->prepare($main_sql);
	$main_stmt->execute($data);
	if(!$main_stmt->rowCount()){
		echo "<hr><p align=\"center\"><b>No Rows for hosts with Open Ports were returned.  You may have not selected any hosts or there are no hosts with the open ports or NSE script you chose to display.</b></p><hr>";
	}
	while($row = $main_stmt->fetch(PDO::FETCH_ASSOC)){
		$agency = $row["agency"];
		$filename = $row["filename"];
		$nmaprun_start = $row["nmaprun_start"];
		$finished_time = $row["finished_time"];
		$hostname_name = $row["hostname_name"];
		$address_addr = $row["address_addr"];
		$status_state = $row["status_state"];
		$port_protocol = $row["port_protocol"];
		$port_portid = $row["port_portid"];
		$port_state = $row["port_state"];
		$port_service_name = $row["port_service_name"];
		$port_service_product = $row["port_service_product"];
		$port_service_version = $row["port_service_version"];
		$port_service_extrainfo = $row["port_service_extrainfo"];
		$script_id = $row["script_id"];
		$script_output = $row["script_output"];
	
		fwrite($fh, "\"$status_state\",\"$address_addr\",\"$hostname_name\",\"$port_portid/$port_protocol\",\"$port_service_name\",\"$port_service_product\",\"$port_service_version\",\"$port_state\",\"$script_id\",\"$script_output\",\"\",\"\",\n");
	}
}
if($isClosed || $isFiltered || $isOpenFiltered){
	$sql = "SELECT
		nmap_runstats_xml.agency,
		nmap_runstats_xml.filename,
		nmap_runstats_xml.nmaprun_start,
		nmap_runstats_xml.finished_time,
		nmap_hosts_xml.hostname_name,
		nmap_hosts_xml.address_addr,
		nmap_hosts_xml.status_state,
		nmap_ports_xml.port_protocol,
		nmap_ports_xml.port_portid,
		nmap_ports_xml.port_state
		FROM
			nmap_runstats_xml
		Inner Join nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
		Left Join nmap_ports_xml ON nmap_ports_xml.host_id = nmap_hosts_xml.id
		WHERE
			nmap_runstats_xml.agency =  ? AND
			nmap_runstats_xml.filename =  ? AND
			nmap_runstats_xml.nmaprun_start =  ? AND
			nmap_runstats_xml.finished_time =  ?";
	$portOption = '';
	$portOption .= $isClosed ? 1 : 0;
	$portOption .= $isFiltered ? 1 : 0;
	$portOption .= $isOpenFiltered ? 1 : 0;
	switch ($portOption) {
	case '001':
			$sql .=" AND (nmap_ports_xml.port_state = 'open|filtered')";
			break;
	case '010':
			$sql .=" AND (nmap_ports_xml.port_state = 'filtered')";
			break;
	case '011':
			$sql .=" AND (nmap_ports_xml.port_state = 'filtered' OR nmap_ports_xml.port_state = 'open|filtered')";
			break;
	case '100':
			$sql .=" AND (nmap_ports_xml.port_state = 'closed')";
			break;
	case '101':
			$sql .=" AND (nmap_ports_xml.port_state = 'closed' OR nmap_ports_xml.port_state = 'open|filtered')";
			break;
	case '110':
			$sql .=" AND (nmap_ports_xml.port_state = 'closed' OR nmap_ports_xml.port_state = 'filtered')";
			break;
	case '111':
			$sql .=" AND (nmap_ports_xml.port_state = 'closed' OR nmap_ports_xml.port_state = 'filtered' OR nmap_ports_xml.port_state = 'open|filtered')";
			break;
	default:
		$sql .=" AND (nmap_ports_xml.port_state = 'closed')";
	}
	$data = array($agency, $filename, $nmaprun_start, $finished_time);
	$stmt = $db->prepare($sql);
	$stmt->execute($data);
	if(!$stmt->rowCount()){
		echo "<hr><p align=\"center\"><b>No Rows for hosts with Closed/Filtered/Open|Filtered Ports were returned.  You may have not selected any hosts or there are no hosts with these ports or NSE script you chose to display.</b></p><hr>";
	}
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$agency = $row["agency"];
		$filename = $row["filename"];
		$nmaprun_start = $row["nmaprun_start"];
		$finished_time = $row["finished_time"];
		$hostname_name = $row["hostname_name"];
		$address_addr = $row["address_addr"];
		$status_state = $row["status_state"];
		$port_protocol = $row["port_protocol"];
		$port_portid = $row["port_portid"];
		$port_state = $row["port_state"];
	
		fwrite($fh, "\"$status_state\",\"$address_addr\",\"$hostname_name\",\"$port_portid/$port_protocol\",\"\",\"\",\"\",\"$port_state\",\"\",\"\",\"\",\"\",\n");
	}
}

if($isDown){
	$sql = "SELECT DISTINCT
		nmap_runstats_xml.agency,
		nmap_runstats_xml.filename,
		nmap_runstats_xml.nmaprun_start,
		nmap_runstats_xml.finished_time,
		nmap_hosts_xml.hostname_name,
		nmap_hosts_xml.address_addr,
		nmap_hosts_xml.status_state
		FROM
			nmap_runstats_xml
		Inner Join nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
		WHERE
			nmap_runstats_xml.agency =  ? AND
			nmap_runstats_xml.filename =  ? AND
			nmap_runstats_xml.nmaprun_start =  ? AND
			nmap_runstats_xml.finished_time =  ? AND
			nmap_hosts_xml.status_state = ?";

	$data = array($agency, $filename, $nmaprun_start, $finished_time, $isDown);
	$stmt = $db->prepare($sql);
	$stmt->execute($data);
	if(!$stmt->rowCount()){
		echo "<hr><p align=\"center\"><b>No Rows for hosts that are Down.</p><hr>";
	}
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$agency = $row["agency"];
		$filename = $row["filename"];
		$nmaprun_start = $row["nmaprun_start"];
		$finished_time = $row["finished_time"];
		$hostname_name = $row["hostname_name"];
		$address_addr = $row["address_addr"];
		$status_state = $row["status_state"];
		fwrite($fh, "\"$status_state\",\"$address_addr\",\"$hostname_name\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\n");
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

<?php
$start = mktime();
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );ifDBError($db);

$agency_temp = explode(":", $_POST["agency"]);
$agency = $agency_temp[0];
$filename = $agency_temp[1];
$nmaprun_start = $agency_temp[2];
$finished_time = $agency_temp[3];

date_default_timezone_set('UTC');
$myDir = "/var/www/projectRF/nmap/csvfiles/";
$myFileName = $agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");



fwrite($fh, '"DOMAIN NAME","IP","HOST STATE","INDUSTRY","GEO LAT","GEO LONG","WEB SERVER PORTS","WEB SERVER TYPE","WEB SERVER VERSION","TRACE","GOOGLE MALWARE","SSL OPEN","SSLv2","SSH OPEN","SSHv1","MS-TERM-SVC"' . PHP_EOL);
$sql = "SELECT DISTINCT
nmap_hosts_xml.id AS host_id,
nmap_hosts_xml.hostname_name,
nmap_hosts_xml.address_addr,
nmap_hosts_xml.status_state,
nmap_nse_xml.script_output
FROM
nmap_runstats_xml
INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
INNER JOIN nmap_nse_xml ON nmap_nse_xml.host_or_port_id = nmap_hosts_xml.id
		WHERE
			nmap_runstats_xml.agency = '$agency' AND
			nmap_runstats_xml.filename = '$filename' AND
			nmap_runstats_xml.nmaprun_start = '$nmaprun_start' AND
			nmap_runstats_xml.finished_time = '$finished_time' AND
			nmap_nse_xml.script_id = 'ip-geolocation-geoplugin'
		";
$result = $db->query($sql);ifDBError($result);
$value = "PLACEHOLDER";
while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){
	$host_id = $row["host_id"];
	$hostname_name = $row["hostname_name"];
	$address_addr = $row["address_addr"];
	$status_state = $row["status_state"];
	#64.34.181.147 (00000.ca)  coordinates (lat,lon): 40.688800811768,-74.020301818848  state: New York, United States
	preg_match( '/\(lat,lon\):\s*(\-?\d+\.\d+),(\-?\d+\.\d+)/', $row["script_output"], $matches);
	$lat = $matches[1];
	$lon = $matches[2];
	
	$port_sql = "SELECT DISTINCT
					nmap_ports_xml.port_portid,
					nmap_ports_xml.port_service_name,
					nmap_ports_xml.port_service_product,
					nmap_ports_xml.port_service_version,
					nmap_ports_xml.port_service_extrainfo,
					nmap_nse_xml.script_id,
					nmap_nse_xml.script_output
				FROM
					nmap_ports_xml
				INNER JOIN nmap_nse_xml ON nmap_nse_xml.host_or_port_id = nmap_ports_xml.id
				WHERE
					nmap_ports_xml.host_id = '$host_id' AND
					nmap_ports_xml.port_state = 'open'
				";
	$port_result = $db->query($port_sql);ifDBError($port_result);
	$web_server_port = $web_server_type = $web_server_version = "";
	$sslOpen =  $sslv2 = $sshOpen = $sshv1 = $rdpOpen = "no";
	while($port_row = $port_result->fetchRow(DB_FETCHMODE_ASSOC)){
		$port_portid = $port_row["port_portid"];
		$port_service_name = $port_row["port_service_name"];
		$port_service_product = $port_row["port_service_product"];
		$port_service_version = $port_row["port_service_version"];
		$port_service_extrainfo = $port_row["port_service_extrainfo"];
		$port_script_id = $port_row["script_id"];
		$port_script_output = $port_row["script_output"];
		
		if($port_service_name == "http" || $port_service_name == "https"){
			$web_server_port = $port_portid;
			$web_server_type = $port_service_product;
			$web_server_version = $port_service_version;
		}
		if($port_script_id == "http-methods"){
			preg_match( '/Potentially risky methods:\s*(.*)/', $port_script_output, $matches);
			$http_methods = $matches[1];
		}
		if($port_script_id == "http-google-malware"){
			$http_google_malware = $port_script_output;
		}
		if($port_service_name == "https"){
			$sslOpen = "yes";
			if(preg_match('/server supports SSLv2 protocol/',$port_script_output)){
				$sslv2 = "yes";
			}
		}
		if($port_service_name == "ssh"){
			$sshOpen = "yes";
			if(preg_match('/protocol 1\./',$port_service_extrainfo)){
				$sshv1 = "yes";
			}
		}
		if($port_service_name == "ms-term-svc"){
			$rdpOpen = $port_service_version . ", " . $port_service_extrainfo;
		}
	}
	#fwrite($fh, "\"$hostname_name\",\"$address_addr\",\"$status_state\",\"INDUSTRY\",\"$lat\",\"$lon\",\"$web_server_port\",\"$web_server_type\",\"$web_server_version\",\"$http_methods\",\"$http_google_malware\",\"$sslOpen\",\"$sslv2\",\"$sshOpen\",\"$sshv1\",\"$rdpOpen\"\n");
	fwrite($fh, '"' . $hostname_name . '","' . $address_addr . '","' . $status_state . '","' . INDUSTRY . '","' . $lat . '","' . $lon . '","' . $web_server_port . '","' . $web_server_type . '","' . $web_server_version . '","' . $http_methods . '","' . $http_google_malware . '","' . $sslOpen . '","' . $sslv2 . '","' . $sshOpen . '","' . $sshv1 . '","' . $rdpOpen . '"' . PHP_EOL);
}

?>
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title>JP CSV FILE</title>
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
<?php 
$end = mktime();
$time = $end - $start;
echo "<!--" . $time . "-->";
?>
</body>
</html>
<?php

function ifDBError($error)
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

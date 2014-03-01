<html>
<head>
<title> Nmap Parse </title>
<style type="text/css">
p {font-size: 70%}
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
<?php
ini_set("memory_limit","256M");
$filename = $_FILES['userfile']['name'];
$uploaddir = sys_get_temp_dir();
$uploadfile = tempnam(sys_get_temp_dir(), basename($_FILES['userfile']['name']));
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
	echo "<hr><p align=\"center\"><b>File is valid, and was successfully uploaded.</b></p><hr>";
	} else { 
		echo "<h1>Upload Error!</h1>";
		echo "An error occurred while executing this script. The file may be too large or the upload folder may have insufficient permissions.";
		echo "<p />";
		echo "Please examine the following items to see if there is an issue";
		echo "<hr><pre>";
		echo "1.  ".$uploaddir." (Temp) directory exists and has the correct permissions.<br />";
		echo "2.  The php.ini file needs to be modified to allow for larger file uploads.<br />";
		echo "</pre><hr>";
		exit; 
}

if(file_exists($uploadfile)) {
	$xml = simplexml_load_file($uploadfile); 
}
else { 
	exit('Failed to process the xml file.');
} 

include('../main/config.php'); 
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" ); 

$agency = $_POST["agency"]; 
$nmaprun = $xml;
print_r($xml);
$scaninfo = $xml->scaninfo;
$runstats_finished = $xml->runstats->finished;
$runstats_hosts = $xml->hosts;
$sql = "INSERT INTO nmap_runstats_xml (
			agency,
			filename,
			nmaprun_scanner, 
			nmaprun_args, 
			nmaprun_start, 
			nmaprun_startstr, 
			nmaprun_version, 
			nmaprun_xmloutputversion, 
			scaninfo_type, 
			scaninfo_protocol, 
			scaninfo_numservices, 
			scaninfo_services, 
			finished_time, 
			finished_timestr, 
			finished_elapsed, 
			finished_summary, 
			hosts_up, 
			hosts_down, 
			hosts_total
		) VALUES (
			'$agency', 
			'$filename',
			'$nmaprun[scanner]', 
			'$nmaprun[args]', 
			'$nmaprun[start]', 
			'$nmaprun[startstr]', 
			'$nmaprun[version]', 
			'$nmaprun[xmloutputversion]', 
			'$scaninfo[type]', 
			'$scaninfo[protocol]', 
			'$scaninfo[numservices]', 
			'$scaninfo[services]', 
			'$runstats_finished[time]', 
			'$runstats_finished[timestr]', 
			'$runstats_finished[elapsed]', 
			'$runstats_finished[summary]', 
			'$runstats_hosts[up]', 
			'$runstats_hosts[down]', 
			'$runstats_hosts[total]'
		)";
echo "$sql<br>";
$results = $db->query($sql);ifDBError($result);
$sql = "SELECT LAST_INSERT_ID()";
$runstats_id = $db->getRow($sql);ifDBError($result);

foreach($xml->host as $host){

	$status_state = $host->status["state"];
	$status_reason = $host->status["reason"];
	$hostname_name = $host->hostnames->hostname[name];
	$hostname_type = $host->hostnames->hostname[type];
	foreach($host->address as $address){
	if($address["addrtype"] == "ipv4"){
		$ipv4_address = $address["addr"];
	}
		if($address["addrtype"] == "mac"){
			$mac_address = $address["addr"];
			$mac_vendor = $address["vendor"];
		}
	}
	if($status_state == "up"){	

		$address_addr = $host->address[addr];
		$address_addrtype =$host->address[addrtype]; 
		$extraports_state = $host->ports->extraports[state];
		$extraports_count = $host->ports->extraports[count];
	
		if(isset($host->os)){
			$os_portused_state_List = "";
			$os_portused_proto_List = "";
			$os_portused_portid_List = "";
			foreach($host->os->portused as $portused){
				$os_portused_state = $portused[state];
				$os_portused_proto = $portused[proto];
				$os_portused_portid = $portused[portid];
				
				$os_portused_state_List .= $os_portused_state . ",";
				$os_portused_proto_List .= $os_portused_proto . ",";
				$os_portused_portid_List .= $os_portused_portid . ",";
			}
			
			$os_osclass_type_List = "";
			$os_osclass_vendor_List = "";
			$os_osclass_osfamily_List = "";
			$os_osclass_accuracy_List = "";
			foreach ($host->os->osclass as $osclass){
				$os_osclass_type = $osclass[type];
				$os_osclass_vendor = $osclass[vendor]; 
				$os_osclass_osfamily = $osclass[osfamily]; 
				$os_osclass_accuracy = $osclass[accuracy];
			
				$os_osclass_type_List .= $os_osclass_type . ",";
				$os_osclass_vendor_List .= $os_osclass_vendor . ",";
				$os_osclass_osfamily_List .= $os_osclass_osfamily . ",";
				$os_osclass_accuracy_List .= $os_osclass_accuracy . ",";
			}//end osclass foreach
		
			$os_osmatch_name_List = "";
			$os_osmatch_accuracy_List = "";
			$os_osmatch_line_List = "";	
			foreach ($host->os->osmatch as $osmatch){
				$os_osmatch_name = $osmatch[name];
				$os_osmatch_accuracy = $osmatch[accuracy];
				$os_osmatch_line = $osmatch[line]; 
				
				$os_osmatch_name_List .= $os_osmatch_name . ",";
				$os_osmatch_accuracy_List .= $os_osmatch_accuracy . ",";
				$os_osmatch_line_List .= $os_osmatch_line . ",";
			}//end os foreach
		
			$os_osfingerprint = $host->osfingerprint[fingerprint];
		}
		$uptime_seconds = $host->uptime[seconds];
		$uptime_lastboot = $host->uptime[lastboot];
		$tcpsequence_index = $host->tcpsequence[index];
		$tcpsequence_class = $host->tcpsequence['class'];
		$tcpsequence_difficulty = $host->tcpsequence[difficulty];
		$tcpsequence_values = $host->tcpsequence[values];
		$ipidsequence_class = $host->ipidsequence['class'];
		$ipidsequence_values = $host->ipidsequence[values];
		$tcptsequence_class = $host->tcptsequence['class'];
		$tcptsequence_values = $host->tcptsequence[values];
		$times = $host->times;
	
		$sql = "INSERT INTO nmap_hosts_xml (
				runstats_id, 
				address_addr, 
				status_state, 
				status_reason, 
				address_addrtype, 
				hostname_name, 
				hostname_type, 
				extraports_state, 
				extraports_count, 
				os_portused_state, 
				os_portused_proto, 
				os_portused_portid, 
				os_osclass_type, 
				os_osclass_vendor, 
				os_osclass_osfamily, 
				os_osclass_accuracy, 
				os_osmatch_name, 
				os_osmatch_accuracy, 
				os_osmatch_line, 
				os_osfingerprint, 
				uptime_seconds, 
				uptime_lastboot, 
				tcpsequence_index, 
				tcpsequence_class, 
				tcpsequence_difficulty, 
				tcpsequence_values, 
				ipidsequence_class, 
				ipidsequence_values, 
				tcptsequence_class, 
				tcptsequence_values,
				times_srtt,
				times_rttvar,
				times_to
			) VALUES (
				'$runstats_id[0]', 
				'$address_addr', 
				'$status_state', 
				'$status_reason', 
				'$address_addrtype', 
				'$hostname_name', 
				'$hostname_type', 
				'$extraports_state', 
				'$extraports_count', 
				'$os_portused_state_List', 
				'$os_portused_proto_List', 
				'$os_portused_portid_List', 
				'$os_osclass_type_List', 
				'$os_osclass_vendor_List', 
				'$os_osclass_osfamily_List', 
				'$os_osclass_accuracy_List', 
				'$os_osmatch_name_List', 
				'$os_osmatch_accuracy_List', 
				'$os_osmatch_line_List', 
				'$os_osfingerprint', 
				'$uptime_seconds', 
				'$uptime_lastboot', 
				'$tcpsequence_index', 
				'$tcpsequence_class', 
				'$tcpsequence_difficulty', 
				'$tcpsequence_values', 
				'$ipidsequence_class', 
				'$ipidsequence_values', 
				'$tcptsequence_class', 
				'$tcptsequence_values',
				'$times[srtt]',
				'$times[rttvar]',
				'$times[to]'
			)";
		$results = $db->query($sql);ifDBError($result);
		$sql = "SELECT LAST_INSERT_ID()";
		$nmap_host_id = $db->getRow($sql);ifDBError($result);
		
		$trace = $host->trace;
		if(isset($trace->hop)){
			foreach($trace->hop as $hop){
				$sql = "INSERT INTO nmap_host_trace_xml (host_id, trace_port, trace_proto, hop_ttl, hop_ipaddr, hop_rtt, hop_host) VALUES ('$nmap_host_id[0]', '$trace[port]', '$trace[proto]', '$hop[ttl]', '$hop[ipaddr]', '$hop[rtt]', '$hop[host]')";
				$results = $db->query($sql);ifDBError($result);	
			}
		}
		if(isset($host->hostscript)){
			foreach($host->hostscript->script as $hS){
				$script_type = "host";
				$script_id = $hS["id"];
				$script_output = addslashes($hS["output"]);
				$sql = "INSERT INTO nmap_nse_xml (host_or_port_id, script_type, script_id, script_output) VALUES ('$nmap_host_id[0]', '$script_type', '$script_id', '$script_output')";
				$results = $db->query($sql);ifDBError($result);
			}
		}
	
		foreach($host->ports->port as $port){
			$port_protocol = $port[protocol];
			$port_portid = $port[portid];
			$port_state = $port->state[state];
			$port_service_name = $port->service[name];
			$port_service_product = $port->service[product];
			$port_service_version = $port->service[version];
			$port_service_extrainfo = $port->service[extrainfo];
			$port_service_servicefp = addslashes($port->service[servicefp]);
			$port_service_method = $port->service[method];
			$port_service_conf = $port->service[conf];
		
			$sql = "INSERT INTO nmap_ports_xml (host_id, port_protocol, port_portid, port_state, port_service_name, port_service_product, port_service_version, port_service_extrainfo, port_service_servicefp, port_service_method, port_service_conf) VALUES ('$nmap_host_id[0]', '$port_protocol', '$port_portid', '$port_state', '$port_service_name', '$port_service_product', '$port_service_version', '$port_service_extrainfo', '$port_service_servicefp', '$port_service_method', '$port_service_conf')";
			$results = $db->query($sql);ifDBError($result);
			$sql = "SELECT LAST_INSERT_ID()";
			$nmap_port_id = $db->getRow($sql);ifDBError($result);
			
			if(isset($port->script)){
				foreach($port->script as $script){
					$script_type = "port";
					$script_id = $script["id"];
					$script_output = $script["output"];
					$sql = "INSERT INTO nmap_nse_xml (host_or_port_id, script_type, script_id, script_output) VALUES ('$nmap_port_id[0]', '$script_type', '$script_id', '$script_output')";
					$results = $db->query($sql);ifDBError($result);
				}
			}	
		}//end port foreach
	} else {//end state up if
		$sql = "INSERT INTO nmap_hosts_xml (
					runstats_id, 
					address_addr, 
					status_state, 
					status_reason, 
					address_addrtype, 
					hostname_name, 
					hostname_type
				) VALUES (
					'$runstats_id[0]', 
					'$address_addr', 
					'$status_state', 
					'$status_reason', 
					'$address_addrtype', 
					'$hostname_name', 
					'$hostname_type')
				";	
		$results = $db->query($sql);ifDBError($result);
	}//end else state down
}//end host foreach

?>
</td></tr></table>
</body></html>


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

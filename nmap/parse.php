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
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v = new Valitron\Validator($_POST);
$v->rule('slug', 'agency');
if(!$v->validate()) {

    print_r($v->errors());
	exit;
} 
$agency = $_POST["agency"]; 
$nmaprun = $xml;
$scaninfo = $xml->scaninfo;
$runstats_finished = $xml->runstats->finished;
$runstats_hosts = $xml->runstats->hosts;
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
		) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $db->prepare($sql);
$data = array($agency,$filename,$nmaprun[scanner],$nmaprun[args],$nmaprun[start],$nmaprun[startstr],$nmaprun[version],$nmaprun[xmloutputversion],$scaninfo[type],$scaninfo[protocol],$scaninfo[numservices],$scaninfo[services],$runstats_finished[time],$runstats_finished[timestr],$runstats_finished[elapsed],$runstats_finished[summary],$runstats_hosts[up],$runstats_hosts[down],$runstats_hosts[total]);
$stmt->execute($data);
$runstats_id = $db->lastInsertId();

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
			) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt = $db->prepare($sql);
		$data = array($runstats_id,
					$address_addr,
					$status_state,
					$status_reason,
					$address_addrtype,
					$hostname_name,
					$hostname_type,
					$extraports_state,
					$extraports_count,
					$uptime_seconds,
					$uptime_lastboot,
					$tcpsequence_index,
					$tcpsequence_class,
					$tcpsequence_difficulty,
					$tcpsequence_values,
					$ipidsequence_class,
					$ipidsequence_values,
					$tcptsequence_class,
					$tcptsequence_values,
					$times[srtt],
					$times[rttvar],
					$times[to]);
		$stmt->execute($data);
		$nmap_host_id = $db->lastInsertId();
	
		if(isset($host->os)){
			$os_osfingerprint = htmlspecialchars($host->os->osfingerprint[fingerprint], ENT_QUOTES);
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
			foreach ($host->os->osmatch as $osmatch){
				$os_osmatch_name = $osmatch[name];
				$os_osmatch_accuracy = $osmatch[accuracy];
				$os_osmatch_line = $osmatch[line]; 
				$osmatch_sql = "INSERT INTO nmap_osmatch_xml (
							runstats_id, 
							host_id,
							os_portused_state, 
							os_portused_proto, 
							os_portused_portid, 
							os_osmatch_name, 
							os_osmatch_accuracy, 
							os_osmatch_line,
							os_osfingerprint
						) VALUES (?,?,?,?,?,?,?,?,?)";
				$osmatch_data = array(
								$runstats_id,
								$nmap_host_id,
								$os_portused_state_List,
								$os_portused_proto_List,
								$os_portused_portid_List,
								$os_osmatch_name,
								$os_osmatch_accuracy,
								$os_osmatch_line,
								$os_osfingerprint);

				$osmatch_stmt = $db->prepare($osmatch_sql);
				$osmatch_stmt->execute($osmatch_data);

				$osmatch_id = $db->lastInsertId();
				foreach ($host->os->osmatch->osclass as $osclass){
					$os_osclass_type = $osclass[type];
					$os_osclass_vendor = $osclass[vendor]; 
					$os_osclass_osfamily = $osclass[osfamily]; 
					$os_osclass_osgen = $osclass[osgen];
					$os_osclass_accuracy = $osclass[accuracy];
					$os_osclass_cpe = $osclass->cpe;
					$osclass_sql = "INSERT INTO nmap_osclass_xml (
										runstats_id, 
										host_id,
										osmatch_id,
										os_osclass_type, 
										os_osclass_vendor, 
										os_osclass_osfamily,
										os_osclass_osgen,
										os_osclass_accuracy,
										os_osclass_cpe
									) VALUES (?,?,?,?,?,?,?,?,?)";
					$osclass_stmt = $db->prepare($osclass_sql);
					$osclass_data = array($runstats_id,$nmap_host_id,$osmatch_id,$os_osclass_type,$os_osclass_vendor,$os_osclass_osfamily,$os_osclass_osgen,$os_osclass_accuracy,$os_osclass_cpe);
					$osclass_stmt->execute($osclass_data);
				}//end osclass foreach				
			}//end os foreach
		}//end if os

		
		$trace = $host->trace;
		if(isset($trace->hop)){
			foreach($trace->hop as $hop){
				$sql = "INSERT INTO nmap_host_trace_xml (host_id, trace_port, trace_proto, hop_ttl, hop_ipaddr, hop_rtt, hop_host) VALUES (?,?,?,?,?,?,?)";
				$stmt = $db->prepare($sql);
				$data = array($nmap_host_id,$trace[port],$trace[proto],$hop[ttl],$hop[ipaddr],$hop[rtt],$hop[host]);
				$stmt->execute($data);
			}
		}
		if(isset($host->hostscript)){
			foreach($host->hostscript->script as $hS){
				$script_id = $hS["id"];
				$script_output = htmlspecialchars($hS["output"], ENT_QUOTES);
				$sql = "INSERT INTO nmap_host_nse_xml (host_id, script_id, script_output) VALUES (?,?,?)";
				$stmt = $db->prepare($sql);
				$data = array($nmap_host_id,$script_id,$script_output);
				print_r($data);
				$stmt->execute($data);
			}
		}
	
		foreach($host->ports->port as $port){
			$port_protocol = $port[protocol];
			$port_portid = $port[portid];
			$port_state = $port->state[state];
			$port_service_name = $port->service[name];
			$port_service_product = $port->service[product];
			$port_service_tunnel = $port->service[tunnel];
			$port_service_version = $port->service[version];
			$port_service_extrainfo = $port->service[extrainfo];
			$port_service_servicefp = addslashes($port->service[servicefp]);
			$port_service_method = $port->service[method];
			$port_service_conf = $port->service[conf];
			$port_sql = "INSERT INTO nmap_ports_xml (
						host_id, 
						port_protocol, 
						port_portid, 
						port_state, 
						port_service_name, 
						port_service_product, 
						port_service_tunnel,
						port_service_version, 
						port_service_extrainfo, 
						port_service_servicefp, 
						port_service_method, 
						port_service_conf
						) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
			$port_stmt = $db->prepare($port_sql);
			$port_data = array(
						$nmap_host_id,
						$port_protocol,
						$port_portid,
						$port_state,
						$port_service_name,
						$port_service_product,
						$port_service_tunnel,
						$port_service_version,
						$port_service_extrainfo,
						$port_service_servicefp,
						$port_service_method,
						$port_service_conf);
			$port_stmt->execute($port_data);
			$nmap_port_id = $db->lastInsertId();
			if(isset($port->script)){
				foreach($port->script as $script){
					$script_id = $script["id"];
					$script_output = htmlspecialchars($script["output"], ENT_QUOTES);
					$sql = "INSERT INTO nmap_port_nse_xml (host_id, port_id, script_id, script_output) VALUES (?,?,?,?)";
					$stmt = $db->prepare($sql);
					$data = array($nmap_host_id,$nmap_port_id,$script_id,$script_output);
					$stmt->execute($data);
				}
			}	
		}//end port foreach
	} else {//end state up if
		$sql = "INSERT INTO nmap_hosts_xml (runstats_id, address_addr, status_state, status_reason, address_addrtype, hostname_name, hostname_type) VALUES (?,?,?,?,?,?,?)";
		$stmt = $db->prepare($sql);
		$data = array($runstats_id,$address_addr,$status_state,$status_reason,$address_addrtype,$hostname_name,$hostname_type);
		$stmt->execute($data);
	}
}//end host foreach

?>

</td></tr></table>
</body></html>
<?php

######  LAST LINE TO XML FILE
#
#</nmaprun>
#
####  You just need to make sure the XML is completed properly.  If the end of the file is </nmaprun> then no worries


ini_set("memory_limit","512M");
ini_set("error_reporting","E_ALL & ~E_NOTICE");
ini_set("max_execution_time","1800");
$nmapxml_file = $argv[1];
$line = `tail -n 10 $nmapxml_file`;
if(!preg_match( '/nmaprun/', $line)){
	exit('XML file not closed our properly.  Please add </nmaprun> to the end of your file.');
} 
$temp = explode(".", $nmapxml_file);
$length = count($temp);
$csvfilename = $temp[0];

if(file_exists($nmapxml_file)) {
         $xml = simplexml_load_file($nmapxml_file);
}
else {
        exit('Failed to open the xml file');
}


$myFile = $csvfilename . ".csv";
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");
fwrite($fh, '"DOMAIN NAME","IP","HOST STATE","INDUSTRY","GEO LAT","GEO LONG","WEB SERVER PORTS","WEB SERVER TYPE","WEB SERVER VERSION","TRACE","GOOGLE MALWARE","SSL OPEN","SSLv2","SSH OPEN","SSHv1","MS-TERM-SVC"' . PHP_EOL);
foreach($xml->host as $host){
	$status_state = $host->status["state"];
	$hostname_name = $host->hostnames->hostname[name];
	foreach($host->address as $address){
		if($address["addrtype"] == "ipv4"){
			$ipv4_address = $address["addr"];
		}

	}
	if($status_state == "up"){	

		$address_addr = $host->address[addr];
		$address_addrtype =$host->address[addrtype]; 
	
		if(isset($host->hostscript)){
			foreach($host->hostscript->script as $hS){
				$script_id = $hS["id"];
				$script_output = $hS["output"];
				if($script_id == "ip-geolocation-geoplugin"){
					preg_match( '/\(lat,lon\):\s*(\-?\d+\.\d+),(\-?\d+\.\d+)/', $script_output, $matches);
					$lat = $matches[1];
					$lon = $matches[2];
				}
			}
		}
		$web_server_port = $web_server_type = $web_server_version = "";
		$sslOpen =  $sslv2 = $sshOpen = $sshv1 = $rdpOpen = "no";	
		foreach($host->ports->port as $port){
			$port_protocol = $port[protocol];
			$port_portid = $port[portid];
			$port_state = $port->state[state];
			$port_service_name = $port->service[name];
			$port_service_product = $port->service[product];
			$port_service_version = $port->service[version];
			$port_service_extrainfo = $port->service[extrainfo];
			
			if(($port_service_name == "http" || $port_service_name == "https") && $port_state == "open"){
				$web_server_port = $port_portid;
				$web_server_type = $port_service_product;
				$web_server_version = $port_service_version;
			}

			if($port_service_name == "https" && $port_state == "open"){
				$sslOpen = "yes";
				if(preg_match('/server supports SSLv2 protocol/',$port_script_output)){
					$sslv2 = "yes";
				}
			}
			if($port_service_name == "ssh" && $port_state == "open"){
				$sshOpen = "yes";
				if(preg_match('/protocol 1\./',$port_service_extrainfo)){
					$sshv1 = "yes";
				}
			}
			
			if($port_service_name == "ms-term-serv" && $port_state == "open"){
				$rdpOpen = "yes";
			}		
			
			if(isset($port->script)){
				foreach($port->script as $script){
					$port_script_id = $script["id"];
					$port_script_output = $script["output"];
					if($port_script_id == "http-methods"){
						preg_match( '/Potentially risky methods:\s*(.*)/', $port_script_output, $matches);
						$http_methods = $matches[1];
					}
					if($port_script_id == "http-google-malware"){
						$http_google_malware = $port_script_output;
					}
				}
			}	
		}//end port foreach
		fwrite($fh, '"' . $hostname_name . '","' . $address_addr . '","' . $status_state . '","' . INDUSTRY . '","' . $lat . '","' . $lon . '","' . $web_server_port . '","' . $web_server_type . '","' . $web_server_version . '","' . $http_methods . '","' . $http_google_malware . '","' . $sslOpen . '","' . $sslv2 . '","' . $sshOpen . '","' . $sshv1 . '","' . $rdpOpen . '"' . PHP_EOL);
	} else {
		fwrite($fh, '"' . $hostname_name . '","' . $address_addr . '","' . $status_state . '","' . INDUSTRY . '","","","","","","","","","","","",""' . PHP_EOL);
	}
}//end host foreach

?>




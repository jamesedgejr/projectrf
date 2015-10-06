<?php

//Database Report
//plugins:  
// MSSQL 11217, 
// MSSQL 10674 [Databases]:Microsoft SQL Server UDP Query Remote Version Disclosure
/*
A &#039;ping&#039; request returned the following information about the remote
SQL instances :

  ServerName   : JADESQL2W
  InstanceName : SHAREPOINTS
  IsClustered  : No
  Version      : 9.00.4035.00
  tcp          : 65520

  ServerName   : JADESQL2W
  InstanceName : SQLUAT
  IsClustered  : No
  Version      : 9.00.4035.00
  tcp          : 63073
*/
// MSSQL 10144 [Service detection]:Microsoft SQL Server TCP/IP Listener Detection
/*
The remote SQL Server version is 9.0.4060.0.
*/
// MySQL 10719 [Databases]:MySQL Server Detection
/*
Version  : 5.1.44-community
Protocol : 10
Server Status : SERVER_STATUS_AUTOCOMMIT
Server Capabilities : 
  CLIENT_LONG_PASSWORD (new more secure passwords)
  CLIENT_FOUND_ROWS (Found instead of affected rows)
  CLIENT_LONG_FLAG (Get all column flags)
  CLIENT_CONNECT_WITH_DB (One can specify db on connect)
  CLIENT_NO_SCHEMA (Don&#039;t allow database.table.column)
  CLIENT_COMPRESS (Can use compression protocol)
  CLIENT_ODBC (ODBC client)
  CLIENT_LOCAL_FILES (Can use LOAD DATA LOCAL)
  CLIENT_IGNORE_SPACE (Ignore spaces before &quot;(&quot;
  CLIENT_PROTOCOL_41 (New 4.1 protocol)
  CLIENT_INTERACTIVE (This is an interactive client)
  CLIENT_SIGPIPE (IGNORE sigpipes)
  CLIENT_TRANSACTIONS (Client knows about transactions)
  CLIENT_RESERVED (Old flag for 4.1 protocol)
  CLIENT_SECURE_CONNECTION (New 4.1 authentication)
*/
//10144 [Service detection]:Microsoft SQL Server TCP/IP Listener Detection
/*
The remote SQL Server version is 10.50.2550.0.
*/
//22073 [Service detection]:Oracle Database Detection??
//10658 [Databases]:Oracle Database tnslsnr Service Remote Version Disclosure
//need valid output



//Web Servers
//plugins:  24260 HyperText Transfer Protocol (HTTP) Information
/*
Protocol version : HTTP/1.1
SSL : no
Keep-Alive : no
Options allowed : HEAD, GET, PUT, POST, DELETE, TRACE, OPTIONS, MOVE, INDEX, MKDIR, RMDIR
Headers :

  Server: Sun-ONE-Web-Server/6.1
  Date: Wed, 28 May 2014 02:55:44 GMT
  Content-length: 13987
  Content-type: text/html
  Last-modified: Thu, 22 May 2014 17:41:33 GMT
  Etag: &quot;36a3-537e36cd&quot;
  Accept-ranges: bytes
  Connection: close
*/
// plugin 57034 [Web Servers]:IBM WebSphere Application Server Detection
/*
  Source  : WebSphere Application Server/7.0
  Version : 7.0
*/
//34460 [Web Servers]:Unsupported Web Server Detection
/*
 Product                : Tomcat
  Installed version      : 5.0.28
  Supported versions     : 7.0.x / 6.0.x
  Additional information : http://wiki.apache.org/tomcat/TomcatVersions
*/
// 38157 [Web Servers]:Microsoft SharePoint Server Detection
/*
The following instance of SharePoint was detected on the remote host :

  Version : 6.0.2.5530
  URL     : http://bluebiz1w.fhlb-pgh.net/
*/

//DNS 72780 Microsoft DNS Server Version Detection
/*
Reported version : Microsoft DNS 6.1.7601 (1DB14556)
Extended version : 6.1.7601.17750
*/
//DNS 72779 DNS Server Version Detection
/*
DNS server answer for &quot;version&quot; :

  Microsoft DNS 6.1.7601 (1DB14556)
*/


//SNMP plugin 10800 SNMP Supported Protocols Detection
/*
System information :
 sysDescr     : Lantronix UDS 0628162 V5.8.0.1 (041102)
 sysObjectID  : 
 sysUptime    : 0d 0h 0m 3s
 sysContact   : 
 sysName      : 
 sysLocation  : 
 sysServices  : 12
*/

//SSH  plugin 10267 [Service detection]:SSH Server Type and Version Information
/*
SSH version : SSH-2.0-Cisco-1.25
SSH supported authentication : keyboard-interactive,password
SSH banner : 
This system is not or public use.  Unauthorized access or use is subject to discipline, criminal or civil sanctions.  All users consent to monitoring.
*/


//SMTP 10263 [Service detection]:SMTP Server Detection
/*
Remote SMTP server banner :

220 bluenhub1s.fhlb-pgh.net ESMTP Service (Lotus Domino Release 8.5.3FP1) ready at Mon, 26 Aug 2013 21:02:25 -0400
*/

include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v1 = new Valitron\Validator($_POST);
$v1->rule('accepted', ['isHTTP','isMSSQL','isDNS','isSSH','isSNMP','isMYSQL']);
$v1->rule('numeric', ['scan_start', 'scan_end']);
$v1->rule('slug','agency');
$v1->rule('regex','report_name', '/^([\w\s_.\[\]():;@-])+$/'); //regex includes alpha/numeric, space, underscore, dash, period, white space, brackets, parentheses, colon, "at" symbol, and semi-colon
if(!$v1->validate()) {
    print_r($v1->errors());
	exit;
} 
$hostArray = $_POST["host"];
foreach($hostArray as $key => $value) {
	if ($value == "REMOVE") unset($hostArray[$key]);
}
$sql = "CREATE temporary TABLE nessus_tmp_hosts (host_name VARCHAR(255), INDEX ndx_host_name (host_name))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($hostArray as $hA){
	$v2 = new Valitron\Validator(array('host' => $hA));
	$v2->rule('regex','host', '/^([\w.-])+$/');
	if(!$v2->validate()) {
		print_r($v2->errors());
		exit;
	} 
	$sql="INSERT INTO nessus_tmp_hosts (host_name) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($hA));
}

$isHTTP = $_POST["isHTTP"];
$isMSSQL = $_POST["isMSSQL"];
$isDNS = $_POST["isDNS"];
$isSSH = $_POST["isSSH"];
$isSNMP = $_POST["isSNMP"];
$isMYSQL = $_POST["isMYSQL"];
$pluginArray = array();
if($isHTTP){ array_push($pluginArray, "24260","57034");}
if($isMSSQL){ array_push($pluginArray, "11217","10674");}
if($isMYSQL){ array_push($pluginArray, "10719");}
if($isDNS){ array_push($pluginArray, "72780");}
if($isSSH){ array_push($pluginArray, "10267");}
if($isSNMP){ array_push($pluginArray, "10800");}
$sql = "CREATE temporary TABLE nessus_tmp_pluginID (pluginID VARCHAR(255), INDEX ndx_host_name (pluginID))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($pluginArray as $pA){
	$sql="INSERT INTO nessus_tmp_pluginID (pluginID) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($pA));
}

$agency = $_POST["agency"];
$report_name = $_POST["report_name"];
$scan_start = $_POST["scan_start"];
$scan_end = $_POST["scan_end"];

date_default_timezone_set('UTC');
$myDir = getcwd() . "/csvfiles/";
$myFileName = $agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");

$sql = "SELECT DISTINCT
			nessus_tags.ip_addr,
			nessus_tags.mac_addr,
			nessus_tags.fqdn,
			nessus_tags.netbios,
			nessus_tags.operating_system,
			nessus_tags.system_type,
			nessus_results.pluginID,
			nessus_results.pluginName,
			nessus_results.port,
			nessus_results.service,
			nessus_results.protocol,
			nessus_results.plugin_output
		FROM
			nessus_results
			Inner Join nessus_tags ON nessus_tags.tagID = nessus_results.tagID
			Inner Join nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
			Inner Join nessus_tmp_pluginID ON nessus_tmp_pluginID.pluginID = nessus_results.pluginID
		WHERE
			nessus_results.agency = ? AND
			nessus_results.report_name = ? AND
			nessus_results.scan_start = ? AND
			nessus_results.scan_end = ?
";
$stmt = $db->prepare($sql);
$data = array($agency, $report_name, $scan_start, $scan_end);
$stmt->execute($data);
fwrite($fh, "\"HOSTNAME FQDN\",\"HOSTNAME NETBIOS\",\"IP\",\"OS\",\"PORT/PROTOCOL\",\"SERVICE\",\"PRODUCT\",\"EXTRA1\",\"EXTRA2\",\"EXTRA3\"\n");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	$pluginID = $row['pluginID'];
	$pluginName = $row['pluginName'];
	$ip_addr = $row['ip_addr'];
	$mac_addr = $row['mac_addr'];
	$fqdn = $row['fqdn'];
	$netbios = $row['netbios'];
	$operating_system = $row['operating_system'];
	$system_type = $row['system_type'];
	$port = $row['port'];
	$service = $row['service'];
	$protocol = $row['protocol'];
	$database_version = $mssql_path = $mssql_named_instance = $mssql_recommended_version = $mssql_server_name = $mssql_is_clustered = $mssql_tcp = $mssql_np = $mysql_protocol = $mysql_server_status = $mysql_server_capabilities = array();

	if($pluginID =="11217"){
		$instance_count = processMSSQLPluginOutput($row['plugin_output'], $pluginID);
		for($x = 1;$x<=$instance_count;$x++){
			//echo "STUFF:" . $database_version[$x] . "," . $mssql_path[$x] . "," .$mssql_named_instance[$x] . "," .$mssql_recommended_version[$x] . "," .$mssql_server_name[$x] . "<hr>";
			fwrite($fh, "\"$fqdn\",\"$netbios\",\"$ip_addr\",\"$operating_system\",\"$port/$protocol\",\"$service\",\"$database_version[$x]\",\"$mssql_path[$x]\",\"$mssql_named_instance[$x]\",\"$mssql_server_name[$x]\",\n");
		}
	}
	if($pluginID =="10674"){
		$instance_count = processMSSQLPluginOutput($row['plugin_output'], $pluginID);
		for($x = 1;$x<=$instance_count;$x++){
			//echo "STUFF:" . $database_version[$x] . "," . $mssql_path[$x] . "," .$mssql_named_instance[$x] . "," .$mssql_recommended_version[$x] . "," .$mssql_server_name[$x] . "<hr>";
			fwrite($fh, "\"$fqdn\",\"$netbios\",\"$ip_addr\",\"$operating_system\",\"$port/$protocol\",\"$service\",\"$database_version[$x]\",\"$mssql_path[$x]\",\"$mssql_named_instance[$x]\",\"$mssql_server_name[$x]\",\n");
		}
	}
	if($pluginID =="24260"){
		processWWWPluginOutput($row['plugin_output'], $pluginID);
		fwrite($fh, "\"$fqdn\",\"$netbios\",\"$ip_addr\",\"$operating_system\",\"$port/$protocol\",\"$service\",\"$webserver_version\",\"$options_allowed\",\"$location\",\"\",\n");
	}
	if($pluginID =="57034"){
		processWWWPluginOutput($row['plugin_output'], $pluginID);
		fwrite($fh, "\"$fqdn\",\"$netbios\",\"$ip_addr\",\"$operating_system\",\"$port/$protocol\",\"$service\",\"$webserver_source\",\"$webserver_version\",\"\",\"\",\n");
	}
	if($pluginID == "72780"){
		processDNSPluginOutput($row['plugin_output'], $pluginID);
		fwrite($fh, "\"$fqdn\",\"$netbios\",\"$ip_addr\",\"$operating_system\",\"$port/$protocol\",\"$service\",\"$ms_dns_reported_version\",\"$ms_dns_extended_version\",\"\",\"\",\n");
	}
	if($pluginID == "10267"){
		processSSHPluginOutput($row['plugin_output'], $pluginID);
		fwrite($fh, "\"$fqdn\",\"$netbios\",\"$ip_addr\",\"$operating_system\",\"$port/$protocol\",\"$service\",\"$ssh_version\",\"$ssh_supported_authentication\",\"$ssh_banner\",\"\",\n");
	}
	if($pluginID == "10719"){
		processMYSQLPluginOutput($row['plugin_output'], $pluginID);
		fwrite($fh, "\"$fqdn\",\"$netbios\",\"$ip_addr\",\"$operating_system\",\"$port/$protocol\",\"$service\",\"$mysql_version\",\"$mysql_protocol\",\"$mysql_server_status\",\"$mysql_server_capabilities\",\n");
	}
}//end while

function processMSSQLPluginOutput($plugin_output, $pluginID) 
{
	global $database_version;global $mssql_path;global $mssql_named_instance;global $mssql_recommended_version;
	global $mssql_server_name;global $mssql_is_clustered;global $mssql_tcp;global $mssql_np;
	//echo "PLUGINOUTPUT:" . $plugin_output . "<hr>";
	
	//If more than one instance then break them up on double newline
	$plugin_output_instance = explode("\n\n",$plugin_output);
	//for($x=0;$x<$instance_count;$x++){
	$instance_count = 0;
	foreach($plugin_output_instance as $poI){
		if(!empty($poI)){
			$instance_count++;
			//echo "POI:  " . $poI . "<br>";
			$plugin_output_array = explode("\n",$poI);
			foreach ($plugin_output_array as $poA){
				//echo "POA:" . $poA . "<br>";
				$typeArray = explode(":",$poA);
				switch (trim($typeArray[0])) {
					case "Version":
						$database_version[$instance_count] = trim($typeArray[1]);
						break;
					case "Path":
						$mssql_path[$instance_count] = trim($typeArray[1]);
						break;
					case "Named Instance":
						$mssql_named_instance[$instance_count] = trim($typeArray[1]);
						break;
					case "Recommended Version":
						$mssql_recommended_version[$instance_count] = trim($typeArray[1]);
						break;
					case "ServerName":
						$mssql_server_name[$instance_count] = trim($typeArray[1]);
						break;
					case "InstanceName":
						$mssql_named_instance[$instance_count] = trim($typeArray[1]);
						break;
					case "IsClustered":
						$mssql_is_clustered[$instance_count] = trim($typeArray[1]);
						break;
					case "tcp":
						$mssql_tcp[$instance_count] = trim($typeArray[1]);
						break;
					case "np":
						$mssql_np[$instance_count] = trim($typeArray[1]);
						break;
				}//end switch
			}//end foreach
		}//end if empty
	}//end foreach
	return $instance_count;
}

function processWWWPluginOutput($plugin_output, $pluginID)
{
	global $webserver_version;global $location;global $options_allowed; global $webserver_version;global $webserver_source;
	$plugin_output_array = explode("\n",$plugin_output);
	foreach ($plugin_output_array as $poA){
		$typeArray = explode(":",$poA);
		switch (trim($typeArray[0])) {
			case "Server":
				$webserver_version = trim($typeArray[1]);
				break;
			case "Options allowed":
				$options_allowed = trim($typeArray[1]);
				break;
			case "Location":
				$location = trim($typeArray[1]);
				break;
			case "Source":
				$webserver_source = trim($typeArray[1]);
				break;
			case "Version":
				$webserver_version = trim($typeArray[1]);
				break;				
			default:
				//echo $typeArray[0] . "<br>";
				break;				
		}
	}
}

function processDNSPluginOutput($plugin_output, $pluginID)
{
	global $ms_dns_reported_version;global $ms_dns_extended_version;
	$plugin_output_array = explode("\n",$plugin_output);
	foreach ($plugin_output_array as $poA){
		$typeArray = explode(":",$poA);
		switch (trim($typeArray[0])) {
			case "Reported version":
				$ms_dns_reported_version = trim($typeArray[1]);
				break;
			case "Extended version":
				$ms_dns_extended_version = trim($typeArray[1]);
				break;
			default:
				//echo $typeArray[0] . "<br>";
				break;				
		}
	}
}

function processSNMPPluginOutput($plugin_output, $pluginID)
{
	global $sysDescr; global $sysObjectID; global $sysUptime; global $sysContact; global $sysName; global $sysLocation; global $sysServices;
	$plugin_output_array = explode("\n",$plugin_output);
	foreach ($plugin_output_array as $poA){
		$typeArray = explode(":",$poA);
		switch (trim($typeArray[0])) {
			case "sysDescr":
				$sysDescr = trim($typeArray[1]);
				break;
			case "sysObjectID":
				$sysObjectID = trim($typeArray[1]);
				break;
			case "sysUptime":
				$sysUptime = trim($typeArray[1]);
				break;
			case "sysContact":
				$sysContact = trim($typeArray[1]);
				break;
			case "sysName":
				$sysName = trim($typeArray[1]);
				break;
			case "sysLocation":
				$sysLocation = trim($typeArray[1]);
				break;	
			case "sysServices":
				$sysServices = trim($typeArray[1]);
				break;			
			default:
				//echo $typeArray[0] . "<br>";
				break;				
		}
	}
}

function processSSHPluginOutput($plugin_output, $pluginID)
{
	global $ssh_version; global $ssh_supported_authentication; global $ssh_banner; 
	$plugin_output_array = explode("\n",$plugin_output);
	foreach ($plugin_output_array as $poA){
		$typeArray = explode(":",$poA);
		switch (trim($typeArray[0])) {
			case "SSH version":
				$ssh_version = trim($typeArray[1]);
				break;
			case "SSH supported authentication":
				$ssh_supported_authentication = trim($typeArray[1]);
				break;
			case "SSH banner":
				$poA_last_index = count($plugin_output_array) - 1;
				$poA_current_index = key($plugin_output_array);
				$ssh_banner = "";
				for($x=$poA_current_index;$x<=$poA_last_index;$x++){
					$ssh_banner .= $plugin_output_array[$x];
				}
				break;			
			default:
				//echo $typeArray[0] . "<br>";
				break;				
		}
	}
}

function processMYSQLPluginOutput($plugin_output, $pluginID)
{
	global $mysql_version; global $mysql_protocol; global $mysql_server_status; global $mysql_server_capabilities;
	$plugin_output_array = explode("\n",$plugin_output);
	foreach ($plugin_output_array as $poA){
		$typeArray = explode(":",$poA);
		switch (trim($typeArray[0])) {
			case "Version":
				$mysql_version = trim($typeArray[1]);
				break;
			case "Protocol":
				$mysql_protocol = trim($typeArray[1]);
				break;			
			case "Server Status":
				$mysql_server_status = trim($typeArray[1]);
				break;
			case "Server Capabilities":
				$poA_last_index = count($plugin_output_array) - 1;
				$poA_current_index = key($plugin_output_array);
				$mysql_server_capabilities = "";
				for($x=$poA_current_index;$x<=$poA_last_index;$x++){
					$mysql_server_capabilities .= $plugin_output_array[$x];
				}
				break;			
			default:
				//echo $typeArray[0] . "<br>";
				break;				
		}
	}
}
 
?>
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title>NESSUS PORT REPORT</title>
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
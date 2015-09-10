<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$agency = $_POST["agency"];
?>
<html>
<head>
<title> NMAP XML TO CSV </title>
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
<?php
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
	exit('Failed to open the xml file');
} 

date_default_timezone_set('UTC');
$myDir = getcwd() . "/csvfiles/";
$myFileName = $agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");

fwrite($fh, "\"IP\",\"HOSTNAME\",\"FQDN\",\"OS\",\"PRODUCT\",\"SERVICE\",\"PORT\"\n");
$current_ip = "";
foreach($xml->host as $host){
	$ipv4_address = $hostname_name = $os = "";
	$status = $host->status["state"];
	if($status == "up"){
	  foreach($host->address as $address){
		  if($address["addrtype"] == "ipv4"){
			  $ipv4_address = $address["addr"];
		  }
	  } 
	  $hostname_name = "(nmap)" . $host->hostnames->hostname[name];
	  if($hostname_name == "(nmap)"){
		$nessusSQL = "SELECT DISTINCT 
					nessus_tags.netbios,
					nessus_tags.fqdn
				FROM 	nessus_results
					Inner Join nessus_tags ON nessus_tags.tagID = nessus_results.tagID
				WHERE 
					nessus_tags.ip_addr = ? AND nessus_results.agency = ?";
		$stmt = $db->prepare($nessusSQL);
		$array = array($ipv4_address,$agency);
		$stmt->execute($array);
		$nessusRow = $stmt->fetch(PDO::FETCH_ASSOC);
		$hostname_name = "(nessus)" . strtolower($nessusRow["netbios"]);
		$hostname_fqdn = "(nessus)" . strtolower($nessusRow["fqdn"]);
	  }

	  $nessusSQL = "SELECT DISTINCT 
				nessus_tags.operating_system 
			FROM 
			 	nessus_results
				Inner Join nessus_tags On nessus_tags.tagID = nessus_results.tagID
			WHERE 
				nessus_tags.ip_addr = ? AND nessus_results.agency = ?";
	  $stmt = $db->prepare($nessusSQL);
	  $array = array($ipv4_address,$agency);
	  $stmt->execute($array);
	  $nessusRow = $stmt->fetch(PDO::FETCH_ASSOC);
	  $os = "(nessus)" . $nessusRow["operating_system"];
	  $os = str_replace("Enterprise", "Ent", $os);
	  $os = str_replace("Standard", "Std", $os);
	  $os = str_replace("Service Pack", "SP", $os);
	  $os = str_replace("Microsoft", "", $os);
	  $os = str_replace("Edition", "Ed", $os);
	  $os = str_replace("(English)", "", $os);
	  if($os == "(nessus)"){
	  	$os = "(nmap)" . $host->os->osmatch["name"];
	  }
	  foreach($host->ports->port as $port){
		  $port_service_product = $port_service_name = $port_portid = $port_protocol = "";
		  $port_protocol = $port[protocol];
		  $port_portid = $port[portid];
		  $port_service_name = $port->service[name];
		  $port_service_product = $port->service[product];
		  $port_service_version = $port->service[version];
		  $port_state = $port->state[state];
		  //if($status == "up" && $port_service_name != "tcpwrapped" && $port_service_name != "msrpc" && $port_state == "open"){
		  if($status == "up" && $port_state == "open"){
			//if($current_ip != $ipv4_address){
			//	fwrite($fh, "\"$ipv4_address\",\"$hostname_name\",\"$os\",");
			//	$current_ip = $ipv4_address;
			//} else {
			//	fwrite($fh, "\"\",\"\",\"\",");
			//}
			fwrite($fh, "\"$ipv4_address\",\"$hostname_name\",\"$hostname_fqdn\",\"$os\",\"$port_service_product $port_service_version\",\"$port_service_name\",\"$port_portid/$port_protocol\"\n");
		  }
	  }//end port foreach
	}//end status up if
}
?>
<table width="100%">
  <tr><td>
	<hr>
	<p align="center"><a href="csvfiles/<?php echo "$myFileName";?>">Click Here</a> to download the CSV file.</p>
	<hr>
</td></tr></table>
</td></tr></table>
</body></html>


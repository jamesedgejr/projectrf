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
$isUp = $_POST["isUp"];
$isDown = $_POST["isDown"];
$isOpen = $_POST["isOpen"];
$isClosed = $_POST["isClosed"];
$isFiltered = $_POST["isFiltered"];
$isOpenFiltered = $_POST["isOpenFiltered"];


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
$myFileName = "nmap_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");

fwrite($fh, "\"IP\",\"HOSTNAME\",\"MAC\",\"VENDOR\",\"OS\",\"PRODUCT/VERSION\",\"SERVICE\",\"PORT/PROTOCOL\",\"PORT STATE\",\"HOST STATE\"\n");
foreach($xml->host as $host){
	$status = $host->status["state"];
	foreach($host->address as $address){
	  if($address["addrtype"] == "ipv4"){
		  $ipv4_address = $address["addr"];
	  }
	  if($address["addrtype"] == "mac"){
		  $mac_address = $address["addr"];
		  $mac_vendor = $address["vendor"];
	  }
	}		  
	if($isDown && $status == "down"){
		fwrite($fh, "\"$ipv4_address\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"$status\"\n");
	}
	$hostname_name = $host->hostnames->hostname[name];
	  $os = $host->os->osmatch["name"];
	  foreach($host->ports->port as $port){
		  $port_protocol = $port[protocol];
		  $port_portid = $port[portid];
		  $port_state = $port->state[state];
		  $port_service_name = $port->service[name];
		  $port_service_product = $port->service[product];
		  $port_service_version = $port->service[version];
		  if($isUp && $status == "up"){
			if($isOpen && $port_state =="open"){
				fwrite($fh, "\"$ipv4_address\",\"$hostname_name\",\"$mac_address\",\"$mac_vendor\",\"$os\",\"$port_service_product $port_service_version\",\"$port_service_name\",\"$port_protocol/$port_portid\",\"$port_state\",\"$status\"\n");
			}
			if($isClosed && $port_state =="closed"){
				fwrite($fh, "\"$ipv4_address\",\"$hostname_name\",\"$mac_address\",\"$mac_vendor\",\"$os\",\"$port_service_product/$port_service_version\",\"$port_service_name\",\"$port_protocol/$port_portid\",\"$port_state\",\"$status\"\n");
			}
			if($isFiltered && $port_state =="filtered"){
				fwrite($fh, "\"$ipv4_address\",\"$hostname_name\",\"$mac_address\",\"$mac_vendor\",\"$os\",\"$port_service_product/$port_service_version\",\"$port_service_name\",\"$port_protocol/$port_portid\",\"$port_state\",\"$status\"\n");
			}
			if($isOpenFiltered && $port_state =="open|filtered"){
				fwrite($fh, "\"$ipv4_address\",\"$hostname_name\",\"$mac_address\",\"$mac_vendor\",\"$os\",\"$port_service_product/$port_service_version\",\"$port_service_name\",\"$port_protocol/$port_portid\",\"$port_state\",\"$status\"\n");
			}
		  }
	  }//end port foreach
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

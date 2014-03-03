<?php ?>
<html>
<head><title>Parse Nessus v1 .nessus XML file</title>
<style type="text/css">
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

$agency = $_POST["agency"];
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

include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );

?>

<form action="parse_v1_step2.php" method="POST">
<table align="center" width="500">
<tr><td colspan="3"><p>This form exists because version 1 of the .nessus XML file doesn't correctly assign an ip address if you scanned using a hostname.  If you scanned the target based on its host name then the ip address will be incorrect.  This form gives you a chance to properly correct the mistake in the XML document.</p></td></tr>
	<tr>
	   <td><p>DNS</p></td>
	   <td><p>Netbios</p></td>
	   <td><p>IP Address</p></td>
	</tr>
<?php
foreach($xml->Report->ReportHost as $ReportHost){
	$netbios_name = $ReportHost->netbios_name;
	$dns_name = $ReportHost->dns_name;
	$host_name = $ip_addr = $ReportHost->HostName;
	$num_ports = $ReportHost->num_ports;

	//remove the dots...why?  I forget why as I originally wrote this in 2008 or so.
	$host = "";
	$tempArray = explode(".",$host_name);
	foreach ($tempArray as $t){
		$host = $host . $t;
	}


	if($num_ports != "0"){
?>

	<tr>
	  <td><p><?php echo "$dns_name";?></p></td>
	  <td><p><?php echo "$netbios_name";?></p></td>
	  <td><p><input name="<?php echo "host$host";?>" type="text" value="<?php echo "$ip_addr";?>"></p></td>
	</tr>
<?php
	}//end if
}//end foreach
?>
<tr>
	<td><p>Change IP?</p></td>
	<td><p><input type="radio" value="y" name="changeIP">Yes</p></td>
	<td><p><input type="radio" value="n" name="changeIP" checked>No</p></td>
</tr>
<tr><td><input type="submit" value="submit" /></td><td><input type="hidden" name="agency" value="<?php echo "$agency"?>" /><input type="hidden" name="uploadfile" value="<?php echo "$uploadfile"?>" /></td></tr>
</table>
</form>


</td></tr></table>
</body>
</html>

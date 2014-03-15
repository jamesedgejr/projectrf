<?php

#Database Report
#plugins:  
# MSSQL 11217, 10674
# MySQL 10719

include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );ifError($db);

$hostArray = $_POST["host"];
foreach($hostArray as $key => $value) {
	if ($value == "REMOVE") unset($hostArray[$key]);
}
$sql = "CREATE temporary TABLE nessus_tmp_hosts (host_name VARCHAR(255), INDEX ndx_host_name (host_name))";
$result = $db->query($sql);ifError($result);
foreach ($hostArray as $hA){
	$sql="INSERT INTO nessus_tmp_hosts (host_name) VALUES ('$hA')";
	$result = $db->query($sql);ifError($result);	
}

$agency = $_POST["agency"];
$report_name = $_POST["report_name"];
$scan_start = $_POST["scan_start"];
$scan_end = $_POST["scan_end"];

date_default_timezone_set('UTC');
$myDir = "/var/www/projectRF/nessus/csvfiles/";
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
nessus_results.protocol,
nessus_results.plugin_output
FROM
nessus_results
Inner Join nessus_tags ON nessus_tags.tagID = nessus_results.tagID
Inner Join nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
WHERE
nessus_results.pluginID =  '10719' AND
nessus_results.agency = '$agency' AND
nessus_results.report_name = '$report_name' AND
nessus_results.scan_start = '$scan_start' AND
nessus_results.scan_end = '$scan_end'
";
echo $sql . "<br>";

$result = $db->query($sql);ifError($result);

while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){

	$pluginID = $row['pluginID'];
	$pluginName = $row['pluginName'];
	$ip_addr = $row['ip_addr'];
	$mac_addr = $row['mac_addr'];
	$fqdn = $row['fqdn'];
	$netbios = $row['netbios'];
	$operating_system = $row['operating_system'];
	$system_type = $row['system_type'];
	$port = $row['port'];
	$protocol = $row['protocol'];
	$database_version = $mssql_path = $mssql_named_instance = $mssql_recommended_version = $mssql_server_name = $mssql_is_clustered = $mssql_tcp = $mssql_np = $mysql_protocol = $mysql_server_status = $mysql_server_capabilities = array();

	$instance_count = processPluginOutput($row['plugin_output'], $pluginID);
	//echo "INSTANCE" . $instance_count . "<br>";
	for($x = 1;$x<=$instance_count;$x++){
		echo "STUFF:" . $database_version[$x] . "," . $mssql_path[$x] . "," .$mssql_named_instance[$x] . "," .$mssql_recommended_version[$x] . "," .$mssql_server_name[$x] . "<br>";
	}
	//echo "<hr>";


	//write to text file
}//end while

?>


<?php

function ifError($error)
{
	if (PEAR::isError($error)) {
		echo 'Standard Message: ' . $error->getMessage() . "</br>";
		echo 'Standard Code: ' . $error->getCode() . "</br>";
		echo 'DBMS/User Message: ' . $error->getUserInfo() . "</br>";
		echo 'DBMS/Debug Message: ' . $error->getDebugInfo() . "</br>";
		exit;
	}
}

function processPluginOutput($plugin_output, $pluginID) 
{
	global $database_version;global $mssql_path;global $mssql_named_instance;global $mssql_recommended_version;
	global $mssql_server_name;global $mssql_is_clustered;global $mssql_tcp;global $mssql_np;
	echo "PLUGINOUTPUT:" . $plugin_output . "<br>";
	
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
?>
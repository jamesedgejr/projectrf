<?php

#Database Report
#plugins:  
# MSSQL 11217
# MySQL 10719

include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
ifError($db);

$hostPost = $_POST["host"];
foreach($hostPost as $key => $value) {
	if ($value == "REMOVE") unset($hostPost[$key]);
}
$sql = "CREATE temporary TABLE nessus_tmp_hosts (host_name VARCHAR(255))";
$result = $db->query($sql);
ifError($result);
foreach ($hostPost as $hP){
	$sql="INSERT INTO nessus_tmp_hosts (host_name) VALUES ('$hP')";
	$result = $db->query($sql);ifError($result);	
}

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
Inner Join nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_results.host_name
WHERE
nessus_results.pluginID =  '11217'
";
$result = $db->query($sql);ifError($result);

while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){

$ip_addr = $row['ip_addr'];
$mac_addr = $row['mac_addr'];
$fqdn = $row['fqdn'];
$netbios = $row['netbios'];
$operating_system = $row['operating_system'];
$system_type = $row['system_type'];
$port = $row['port'];
$protocol = $row['protocol'];
$plugin_output = $row['plugin_output'];

}

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

?>
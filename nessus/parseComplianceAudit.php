<html>
<head><title>Completed upload of Nessus Compliance audit file.</title>
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
$uploaddir = sys_get_temp_dir();
$uploadfile = tempnam(sys_get_temp_dir(), basename($_FILES['userfile']['name']));
$audit_file_name =  $_FILES['userfile']['name'];
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


include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
$data = file_get_contents($uploadfile);
$new_variables = array(); //for debugging these insane .audit file.  What new values will the come up with???
/*
<item>
	name: "Minimum password age"
	value: "7"
</item>
*/
/*
preg_match_all("/\<item\>(.*?)\<\/item\>/s",$data,$item_matches, PREG_PATTERN_ORDER);
$count = count($item_matches[1]);
for ($i = 0; $i < $count; $i++) {
	$check_type = "default";
	$custom_item_type = "DEFAULT_MISC";
	$description =  $value_data  = "";
	
	$all_item_values_array = explode("\n",$item_matches[1][$i]);
	$all_item_values_array_count = count($all_item_values_array);
	$all_item_values_array_count--;
	for($x = 1; $x < $all_item_values_array_count; $x++){
		$item_values = explode(":", $all_item_values_array[$x]);
		$item_name = array_shift($item_values);
		$item_value = implode(":", $item_values);

		//the description has a bunch of whitespace on either side and I'm going to be lazy and just trim a bunch of times.
		for($y=0;$y<4;$y++){
			$item_name = trim($item_name);
			$item_value = trim($item_value);
		}
		switch ($item_name) {
			case "name":
				$description = addslashes(str_replace('"', '', $item_value));
				break;
			case "value":
				$value_data = addslashes(str_replace('"', '', $item_value));
				break;
		}

		//There is no value_type for these default checks.  Just a name and value.  I'm making up my own categories based on text found in the name.
		if(stripos($description, 'password') !== FALSE){
			$custom_item_type = "DEFAULT_SYSTEM_ACCESS";
		} elseif (stripos($description, 'account') !== FALSE) {
			$custom_item_type = "DEFAULT_SYSTEM_ACCESS";
		} elseif (stripos($description, 'system log') !== FALSE) {
			$custom_item_type = "DEFAULT_SYSTEM_LOG";
		} elseif (stripos($description, 'security log') !== FALSE) {
			$custom_item_type = "DEFAULT_SECURITY_LOG";
		} elseif (stripos($description, 'application log') !== FALSE) {
			$custom_item_type = "DEFAULT_APPLICATION_LOG";
		} elseif (stripos($description, 'audit') !== FALSE) {
			$custom_item_type = "DEFAULT_EVENT_AUDIT";
		} elseif (stripos($description, 'devices:') !== FALSE) {
			$custom_item_type = "DEFAULT_REGISTRY_VALUES";
		} elseif (stripos($description, 'interactive logon:') !== FALSE) {
			$custom_item_type = "DEFAULT_REGISTRY_VALUES";
		} elseif (stripos($description, 'network access:') !== FALSE) {
			$custom_item_type = "DEFAULT_REGISTRY_VALUES";
		} elseif (stripos($description, 'shutdown:') !== FALSE) {
			$custom_item_type = "DEFAULT_REGISTRY_VALUES";
		} elseif (stripos($description, 'microsoft network server:') !== FALSE) {
			$custom_item_type = "DEFAULT_REGISTRY_VALUES";
		} elseif (stripos($description, 'microsoft network client:') !== FALSE) {
			$custom_item_type = "DEFAULT_REGISTRY_VALUES";
		} elseif (stripos($description, 'domain member:') !== FALSE) {
			$custom_item_type = "DEFAULT_REGISTRY_VALUES";
		} else {
			$custom_item_type = "DEFAULT_MISC";
		}
	}
	//alert the user of the checks that have not category yet defined.
	if($custom_item_type == "DEFAULT_MISC"){
		$new_variables[] = $description;
	}

	//$sql = "INSERT INTO nessus_audit_file (agency, report_name, check_type, custom_item_type, description, value_data) VALUES ('$agency', '$report_name', '$check_type', '$custom_item_type', '$description', '$value_data')";
	//$result = $db->query($sql);ifError($result);

}
*/
/*
<custom_item> 
   type: REGISTRY_SETTING
   description: "HKLM\System\CurrentControlSet\Control\Lsa\LimitBlankPasswordUse"
   value_type: POLICY_DWORD
   value_data: 1
   reg_key: "HKLM\System\CurrentControlSet\Control\Lsa"
   reg_item: "LimitBlankPasswordUse"
   reg_type: REG_DWORD
</item>

<custom_item>
 type		: PASSWORD_POLICY
 description	: "2.2.2.6 Store Passwords using Reversible Encryption: Disabled"
 info		: "In order to support some applications and their authentication, Microsoft permits the ability to store passwords using reversible encryption."
 info		: "If at all possible, this should be avoided.  This option is disabled by default, and should remain so.  Any application that requires"
 info		: "reversible encryption for passwords is purposely putting systems at risk."
 info		: "ref: http://www.cisecurity.org/tools2/windows/CIS_Win2003_MS_Benchmark_v2.0.pdf Ch. 2 pg. 10"
 value_type	: POLICY_DWORD
 value_data	: 0
 password_policy: REVERSIBLE_ENCRYPTION
</item>


*/

preg_match_all("/\<custom_item\>(.*?)\<\/custom_item\>/s",$data,$custom_item_matches, PREG_PATTERN_ORDER);
$count = count($custom_item_matches[1]);
for ($i = 0; $i < $count; $i++) {
	$check_type = "custom";
	$check_policy = $custom_item_type = $description = $value_type = $value_data = $service_name = $svc_option = $acl_option = $file_element = $reg_key = $reg_item = $info_element = $group_name = "";
	$all_values_array = explode("\n",$custom_item_matches[1][$i]);
	$all_values_array_count = count($all_values_array);
	$all_values_array_count--;
	for($x = 1; $x < $all_values_array_count; $x++){
		$values = explode(":", $all_values_array[$x]);
		$name = array_shift($values);
		$value = implode(":", $values);

		//the description has a bunch of whitespace on either side and I'm going to be lazy and just trim a bunch of times.
		for($y=0;$y<4;$y++){
			$name = trim($name);
			$value = trim($value);
		}
		switch ($name) {
			case "type":
				$custom_item_type = addslashes(str_replace('"', '', $value));
				break;
			case "description":
				$description = addslashes(str_replace('"', '', $value));
				break;
			case "value_type":
				$value_type = addslashes(str_replace('"', '', $value));
				break;
			case "value_data":
				$value_data = addslashes(str_replace('"', '', $value));
				break;
			case "service":
				$service = addslashes(str_replace('"', '', $value));
				break;
			case "service_name":
				$service_name = addslashes(str_replace('"', '', $value));
				break;
			case "svc_option":
				$svc_option = addslashes(str_replace('"', '', $value));
				break;
			case "acl_option":
				$acl_option = addslashes(str_replace('"', '', $value));
				break;
			case "file":
				$file_element = addslashes(str_replace('"', '', $value));
				break;
			case "reg_key":
				$reg_key = addslashes(str_replace('"', '', $value));
				break;
			case "reg_item":
				$reg_item = addslashes(str_replace('"', '', $value));
				break;
			case "reg_option":
				$reg_option = addslashes(str_replace('"', '', $value));
				break;
			case "reg_type":
				$reg_type = addslashes(str_replace('"', '', $value));
				break;
			case "info":
				$info_element .= addslashes(str_replace('"', '', $value)) . "\n";
				break;
			case "account_type":
				$account_type = addslashes(str_replace('"', '', $value));
				break;
			case "check_type":
				$custom_item_check_type = addslashes(str_replace('"', '', $value));
				break;
			case "right_type":
				$right_type = addslashes(str_replace('"', '', $value));
				break;
			case "group_name":
				$group_name = addslashes(str_replace('"', '', $value));
				break;
			case (preg_match('/.*_policy/', $name) ? true : false) :
				$check_policy = addslashes(str_replace('"', '', $value));
				break;
			default:
				$new_variables[] = $name;
		}
	}
	$sql = "INSERT INTO nessus_audit_file (
				account_type,
				acl_option,
				audit_file_name,
				check_policy,
				check_type,
				custom_item_check_type,
				custom_item_type,
				description,
				file_element,
				group_name,
				info_element,
				reg_item,
				reg_key,
				reg_option,
				reg_type,
				right_type,
				service,
				service_name,
				svc_option,
				value_data,
				value_type
			) VALUES (
				'$account_type',
				'$acl_option',
				'$audit_file_name',
				'$check_policy',
				'$check_type',
				'$custom_item_check_type',
				'$custom_item_type',
				'$description',
				'$file_element',
				'$group_name',
				'$info_element',
				'$reg_item',
				'$reg_key',
				'$reg_option',
				'$reg_type',
				'$right_type',
				'$service',
				'$service_name',
				'$svc_option',
				'$value_data',
				'$value_type'
			)";
	echo $sql . "<hr>";
	$result = $db->query($sql);ifError($result);
}

echo "<p>Below are a list of \"variables\" that I have never seen in a Nessus .audit file.  If they look meaningful and would like to see them in a report then email me at projectrf(at)jedge.com</p>";
foreach(array_unique($new_variables) as $nV){

	echo $nV . "<br>";
}

?>

</td></tr></table>
</body>
</html>
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
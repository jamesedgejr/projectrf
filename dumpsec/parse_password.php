<html>
<head>
<title> DumpSec Parse </title>
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

include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v = new Valitron\Validator($_POST);
$v->rule('slug', ['agency', 'filetype']);
if(!$v->validate()) {

    print_r($v->errors());
	exit;
} 

ini_set("memory_limit","256M");
$filename = $_FILES['passfile']['name'];
$filetype = $_POST["filetype"];
$uploaddir = sys_get_temp_dir();
$uploadfile = tempnam(sys_get_temp_dir(), basename($_FILES['passfile']['name']));
if (move_uploaded_file($_FILES['passfile']['tmp_name'], $uploadfile)) {
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



$row = 0;
$agency = $_POST["agency"];

if (($handle = fopen($uploadfile, "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ":")) !== FALSE) {

		if($filetype == "ndtsxtractNTLM"){
			$sql_update = "UPDATE dumpsec_user_table SET dumpsec_user_table.NTLMHash = ? WHERE dumpsec_user_table.agency = ? AND dumpsec_user_table.UserName = ?";
			
			$value1 = preg_replace('/\s+/', '', $data[1]); //ntdsxtract NTLM result hash
			$value2 = $agency;
			$value3 = $data[0]; //ntdsxtract NTLM result username
		}
		if($filetype == "cudaHashcat"){
			$sql_update = "UPDATE dumpsec_user_table SET dumpsec_user_table.password = ? WHERE dumpsec_user_table.agency = ? AND dumpsec_user_table.NTLMHash = ?";
			
			$value1 = $data[1]; //ntdsxtract NTLM result hash
			$value2 = $agency;
			$value3 = $data[0]; //ntdsxtract NTLM result username
		}
		$stmt = $db->prepare($sql_update);
		$sql_data = array($value1,$value2,$value3);
		$stmt->execute($sql_data);

		$row++; 
	}// end while
	fclose($handle);
}// end if

?>

</td></tr></table>
</body></html>



<html>
<head><title>Completed Parse of Mimikatz file.</title>
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
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v = new Valitron\Validator($_POST);
$v->rule('slug', 'agency');
if(!$v->validate()) {

    print_r($v->errors());
	exit;
} 
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
	$mimikatz_fileArray = file($uploadfile);
} else { 
	exit('Failed to open the xml file');
} 


$numLines = count($mimikatz_fileArray);

for($x=0;$x<=$numLines;$x++) {

	if (strpos($mimikatz_fileArray[$x],'Primary') !== false) {
	
		$temp = explode(":",$mimikatz_fileArray[$x+1]);
		$username = strtolower(trim($temp[1]));
		$temp = explode(":",$mimikatz_fileArray[$x+2]);
		$domain = trim($temp[1]);
		$temp = explode(":",$mimikatz_fileArray[$x+3]);
		$ntlm = trim($temp[1]);
		$temp = explode(":",$mimikatz_fileArray[$x+4]);
		$sha1 = trim($temp[1]);
		
		for($y = $x;$y<=$x+10;$y++){
			if (strpos($mimikatz_fileArray[$y],'wdigest') !== false) {
				$temp = explode(":",$mimikatz_fileArray[$y+3]);
				$password = trim($temp[1]);
			}
		}

		echo $username ."|". $domain ."|". $ntlm ."|". $sha1 ."|". $password . "<br>";
	}


	//	echo "Line #<b>{$line_num}</b> : " . htmlspecialchars($line) . "<br />\n";	
}

?>
</td></tr></table>
</body>
</html>
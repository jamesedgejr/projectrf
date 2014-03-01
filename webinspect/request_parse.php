<?php 
echo <<<END
<html>
<head><title>Parse WebInspect XML</title>
<style type="text/css">
p {font-size: 70%}
a {text-decoration: none}
a:hover {text-decoration: underline}
</style>
</head>
<body>

END;


$agency = $_POST["agency"];
$application = $_POST["application"];

$uploaddir = '/tmp/upload_';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
   echo "<p align=\"center\"><b>File is valid, and was successfully uploaded.</b></p>";
} else { 
   echo "<p align=\"center\"><b>Error Uploading the File</b></p>";
   exit; 
} 

include('../main/config.php'); 
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" ); 


if(file_exists($uploadfile)) { 
	$xml = simplexml_load_file($uploadfile);
	echo "<p align=\"center\"><b>XML file successfully opened.</b></p>"; 
	//echo "$parsefile"; 
}
else { 
	echo "<p align=\"center\"><b>Failed to open the xml file.</b>< /p>"; 
	exit;
} 
echo "<hr>";
foreach($xml->Request as $Request){
	$request_method = $Request->Method;
	$request_path = $Request->Path;
	$request_fullquery = addslashes($Request->FullQuery);
	$request_fullpostdata = addslashes($Request->FullPostData);
	$request_cookie = addslashes($Request->Cookie);
	if($request_method == "POST"){
	echo "PATH:  $request_path<br>FULLQUERY:  $request_fullquery<br>FULL POST:  $request_fullpostdata<br>$request_cookie<br><br>";
	}
		

}

echo <<<END
</body>
</html>
END;
?>

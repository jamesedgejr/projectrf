<HTML>
<head>
<title>Dynamic List Boxes in PHP</title>
<style type="text/css">
p {font-size: 70%}
a {text-decoration: none}
a:hover {text-decoration: underline}
</style>
</head>
<BODY>
<table width="100%"><tr>
	<td width="20%" valign="top">
	<?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">
<?php
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
	echo "<p align=\"center\"><b>HTML file successfully opened.</b></p>"; 
} 
else { 
	echo "<p align=\"center\"><b>Failed to open the html file.</b></p>"; 
	exit; 
}



$sql = "TRUNCATE TABLE webinspect_80053"; 
$result = $db->query($sql); 

$lines = file($uploadfile); 
$count = count($lines); 

for ($x = 0;$x < $count;$x++){
	if(preg_match("|<td\scolspan=\"5\"\sbgcolor=\"gray\"[^>]+>|siU", $lines[$x], $out)){ 
		$y = $x + 1; 
		$category = trim($lines[$y]);		
			
	}
	if(preg_match("|<td\salign=\"center\"\sclass=\"ComplianceReport-tdThreatID[^>]+>|siU", $lines[$x], $out)){ 
		$y = $x + 1;
		$vulnID = trim($lines[$y]); 
		$sql = "INSERT INTO webinspect_80053 (vulnID, nist_category) VALUES ('$vulnID', '$category')";  
		$result = $db->query($sql);		
		//echo "$category:$vulnID\n";	 
	}
}

//preg_match_all("|<[^>]+>(.*)</[^>]+>|iU", $data, $out, PREG_PATTERN_ORDER);   
//preg_match_all("|<td\scolspan=\"5\"\sbgcolor=\"gray\"[^>]+>(.*)</[^>]+>|siU", $data, $out, PREG_PATTERN_ORDER);  
//preg_match_all("|<td\salign=\"center\"\sclass=\"ComplianceReport-tdThreatID[^>]+>(.*)</[^>]+>|siU", $data, $out, PREG_PATTERN_ORDER);  
?>
</td></tr></table>
</body>
</html>

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
<table width="100%"><tr>
	<td width="20%" valign="top">
END;

include '../main/menu.php';

echo <<<END
</td>
<td valign="top"><hr>
END;

$agency = $_POST["agency"];
$application = $_POST["application"];
$upload_choice = $_POST["upload_choice"];
$upload_path = $_POST["upload_path"];


if($upload_choice == "y"){
	$uploaddir = '/tmp/upload_';
	$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
	   echo "<p align=\"center\"><b>File is valid, and was successfully uploaded.</b></p>";
	} else { 
	   echo "<p align=\"center\"><b>Error Uploading the File</b></p>";
	   exit; 
	} 
}
else {$uploadfile = $upload_path;}
include('../main/config.php'); 
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" ); 

//$uploadfile = "/tmp/upload_board.xml";
if(file_exists($uploadfile)) { 
	$reader = new XMLReader();
	$reader->open($uploadfile);
	echo "<p align=\"center\"><b>XML file successfully opened.</b></p>"; 
}
else { 
	echo "<p align=\"center\"><b>Failed to open the xml file.</b></p>"; 
	exit;
} 
echo "<hr>";

$issueArray = array();

$start = time();
echo "$start<br>";
while ($reader->read()) {
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "URL"){
		$reader->read();
		$url = $reader->value;
		$url_split1 = explode("?", $url);
		$url_split2 = explode("/", $url_split1[0]);
		
		$last_value = count($url_split2);
		$last_value = $last_value - 1;
		$url_file = $url_split2[$last_value];
		$url_folder = $url_split2[4];
		$url = addslashes($url);

	}
//-----------------------------------
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Host"){
		$reader->read();
		$host = $reader->value;
	}
//-----------------------------------
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Port"){
		$reader->read();
		$port = $reader->value;
	}
//-----------------------------------
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "VulnerabilityID"){
		$reader->read();
		$vulnID = $reader->value;
	}
//-----------------------------------
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Severity"){
		$reader->read();
		$severity = addslashes($reader->value);
	}
//-----------------------------------
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Severity"){
		$reader->read();$reader->read();
		$name = addslashes($reader->value);
	}

//-----------------------------------
	if($reader->value == "Summary"){
		$reader->read();$reader->read();$reader->read();
		$summary = addslashes($reader->value);
	}

//-----------------------------------
	if($reader->value == "Implication"){
		$reader->read();$reader->read();$reader->read();
		$implication = addslashes($reader->value);
	}
//-----------------------------------
	if($reader->value == "Execution"){
		$reader->read();$reader->read();$reader->read();
		$execution = addslashes($reader->value);
	}
//-----------------------------------
	if($reader->value == "Fix"){
		$reader->read();$reader->read();$reader->read();
		$fix = addslashes($reader->value);
	}
//-----------------------------------
	if($reader->value == "Reference Info"){
		$reader->read();$reader->read();$reader->read();
		$referenceinfo = addslashes($reader->value);
	}
//-----------------------------------
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Issue"){
		$sql = "INSERT INTO webinspect_xml (agency, application, url, host, port, vulnID, name, severity, summary, execution, implication, fix, referenceinfo, url_file, url_folder) VALUES ('$agency', '$application', '$url', '$host', '$port', '$vulnID', '$name', '$severity', '$summary', '$execution', '$implication', '$fix', '$referenceinfo', '$url_file', '$url_folder')";
		$result = $db->query($sql);
		$issueArray[] = mysql_insert_id();
		$summary = "";
		$execution = "";
		$implication = "";
		$fix = "";
		$referenceinfo = "";
	}
//-----------------------------------
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "RawRequest"){
		$reader->read();
		$rawrequest = addslashes($reader->value);
	}
//-----------------------------------
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Request"){
		while($reader->read()){
			if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Method"){
				$reader->read();
				$method = $reader->value;
			}
			if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Path"){
				$reader->read();
				$path = $reader->value;
			}
			if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "FullQuery"){
				$reader->read();
				$fullquery = addslashes($reader->value);
			}
			if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "FullPostData"){
				$reader->read();
				$fullpostdata = addslashes($reader->value);
			}
			if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Cookie"){
				$reader->read();
				$cookie = addslashes($reader->value);
				break;//we are breaking here because the cookie element comes up a second time in the request
			}
		}
	}


	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Session"){
		//echo "URL:$url<br>HOST:$host<br>NAME:$issueName<br>SUMMARY:$summary<br>IMPLICATION:$implication<br>METHOD:$method<br>PATH:$path<br>FULLQUERY:$fullquery<br>FULLPOSTDATA:$fullpostdata<br>COOKIE:$cookie<br>REFERENCE:$referenceinfo<br>RAW:$rawrequest<br>";

		foreach($issueArray as $iA){
			$sql = "UPDATE webinspect_xml SET rawrequest='$rawrequest', request_method='$method', request_path='$path', request_fullquery='$fullquery', request_fullpostdata='$fullpostdata', request_cookie='$cookie' WHERE id=$iA";
			
			$result = $db->query($sql);
		}

		$issueArray = array();

		$url = "";
		$host = "";
		$port = "";
		$vulnID = "";
		$name = "";
		$severity = "";
		$summary = "";
		$execution = "";
		$implication = "";
		$fix = "";
		$referenceinfo = "";
		$rawrequest = "";
		$method = "";
		$path = "";
		$fullquery = "";
		$fullpostdata = "";
		$cookie = "";
	
	}//end if END_ELEMENT Session

}//end read while

$end = time();
echo "$end<Br>";

echo <<<END
</td></tr></table>
</body>
</html>
END;
?>

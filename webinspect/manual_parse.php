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


//to manually parse a file you need to file in some information
//enter the agency, application, and path to the file to parse
$agency = "KSU";
$application = "VIC";
$uploadfile = "/home/edge/vic.xml";

include('../main/config.php'); 
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" ); 


if(file_exists($uploadfile)) { 
	$xml = simplexml_load_file($uploadfile);
	echo "<p align=\"center\"><b>XML file successfully opened.</b></p>"; 
}
else { 
	echo "<p align=\"center\"><b>Failed to open the xml file.</b>< /p>"; 
	exit;
} 
echo "<hr>";
foreach($xml->Session as $session){
	$url = addslashes($session->URL);
	$host = $session->Host;
	$port = $session->Port;
	/*
	These fields added 7/24/06	
	Removed 7/14/08
	*/
	//$raw_request = addslashes($session->RawRequest);
	//$raw_response = $session->RawResponse;
	/*
	These fields added 12/18/07
	*/
	$request_method = $session->Request->Method;
	$request_path = $session->Request->Path;
	$request_fullquery = addslashes($session->Request->FullQuery);
	$request_fullpostdata = addslashes($session->Request->FullPostData);
	$request_cookie = addslashes($session->Request->Cookie);
	
	
	foreach($session->Issues->Issue as $issue){
		$vulnID = $issue->VulnerabilityID;
		$name = addslashes($issue->Name);
		$severity = addslashes($issue->Severity);
		
		$summary = "";
		$implication = "";
		$execution = "";
		$fix = "";
		$referenceinfo = "";
		foreach($issue->ReportSection as $reportsection){
			if($reportsection->Name == "Summary"){$summary = addslashes($reportsection->SectionText);}
			if($reportsection->Name == "Implication"){$implication = addslashes($reportsection->SectionText);}
			if($reportsection->Name == "Execution"){$execution = addslashes($reportsection->SectionText);}
			if($reportsection->Name == "Fix"){$fix = addslashes($reportsection->SectionText);}
			if($reportsection->Name == "Reference Info"){$referenceinfo = addslashes($reportsection->SectionText);}
		}//end reportsection foreach		
		
		

			/* Must check to see if agency/url/host/vulnID/application/request combination are already in the table */
			$sql_lookup = "SELECT * FROM webinspect_xml WHERE agency='$agency' AND url='$url' AND host='$host' AND vulnID='$vulnID' AND rawrequest='$raw_request' AND application='$application'";		
			$result_lookup = $db->query($sql_lookup);
			$row_lookup = $result_lookup->numRows();
			if($row_lookup == ""){
				$sql = "INSERT INTO webinspect_xml (agency, application, url, host, port, vulnID, name, severity, summary, execution, implication, fix, referenceinfo, request_method, request_path, request_fullquery, request_fullpostdata, request_cookie) VALUES ('$agency', '$application', '$url', '$host', '$port', '$vulnID', '$name', '$severity', '$summary', '$execution', '$implication', '$fix', '$referenceinfo', '$request_method', '$request_path', '$request_fullquery', '$request_fullpostdata', '$request_cookie')";

				$result = $db->query($sql);
				echo "exit<br>";exit;
			
			}//end if lookup if it is table or now
			else{echo "<b>DUPLICATE RECORD: $agency, $host, $application, $vulnID<br>$raw_request</b><br><br><br>";}
		
	}
}

echo <<<END
</td></tr></table>
</body>
</html>
END;
?>

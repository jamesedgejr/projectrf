<html>
<head><title>Completed upload of Nessus v1 XML file.</title>
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
/*
Version 1 of the Nessus XML file does not have any reference to the Plugin Family.  
I use this when organizing the plugins during report generation so we kinda need that.
I parsed all the nasl files to create pluginID.to.Family.csv which we will now load
into an array called pluginFamilyIndex with pluginID as the index.
*/
	$file = fopen("pluginID.to.Family.csv", "r");
	$pluginFamilyIndex = array();
	while (($line = fgets($file)) !== false){
		$lineArray = explode(",",$line);
		$pluginFamilyIndex[$lineArray[0]] = trim($lineArray[1]);
	}
	$pluginFamilyIndex[0] = "No Plugin ID";
	fclose($file);

$agency = $_POST["agency"];
$uploadfile = $_POST["uploadfile"];
if(file_exists($uploadfile)) { 
	$xml = simplexml_load_file($uploadfile);
} 
else { 
	exit('Failed to open the xml file');
} 

include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

/*
<ReportName>test2</ReportName>
<StartTime>Sun Mar 02 01:16:08 2014</StartTime>
<StopTime>Sun Mar 02 01:18:26 2014</StopTime>
*/
$report_name = $xml->Report->ReportName;
$scan_start = strtotime($xml->Report->StartTime);
$scan_end = strtotime($xml->Report->StopTime);
$notInIndex = array();
foreach($xml->Report->ReportHost as $ReportHost){
/*
<HostName>192.168.1.60</HostName>
<startTime>Sun Mar 02 01:16:13 2014</startTime>
<stopTime>Sun Mar 02 01:18:24 2014</stopTime>
<netbios_name>WIN2K-DC</netbios_name>
<mac_addr>08:00:27:44:14:c6</mac_addr>
<dns_name>WIN2K-DC</dns_name>
<os_name>Microsoft Windows 2000 Server Service Pack 4 (English)</os_name>
*/
	$fqdn = $ReportHost->dns_name;
	$host_end = strtotime($ReportHost->stopTime);
	$host_name = $ReportHost->HostName;
	//remove the dots to match up with POST data
	$tempArray = explode(".",$host_name);
	$host = "";
	foreach ($tempArray as $t){
		$host = $host . $t;
	}
	$changeIP = $_POST["changeIP"];
	if($changeIP == "y"){
	   $ip_addr = $_POST["host$host"];
	} else {
		$ip_addr = $host_name;
	}
	$host_start = strtotime($ReportHost->startTime);
	$netbios = $ReportHost->netbios_name;
	$mac_addr = $ReportHost->mac_addr;
	$operating_system = $ReportHost->os_name;
	$sql = "INSERT INTO nessus_tags 
				(fqdn,host_end,host_name,host_start,ip_addr,mac_addr,netbios,operating_system)
			VALUES 
				(?,?,?,?,?,?,?,?)
			";	
	$stmt = $db->prepare($sql);
	$sql_data = array($fqdn,$host_end,$host_name,$host_start,$ip_addr,$mac_addr,$netbios,$operating_system);
	$db->execute($sql_data);
	//$sql = "SELECT LAST_INSERT_ID()";
	$tagID = $db->lastInsertId();
	foreach ($ReportHost->ReportItem as $ReportItem){
		/*
		<ReportItem>
		<port>dce-rpc (1046/tcp)</port><severity>3</severity>
		<pluginID>13852</pluginID>
		<pluginName>MS04-022: Microsoft Windows Task Scheduler Remote Overflow (841873) (uncredentialed check)</pluginName>
		<data>Synopsis :\n\nArbitrary code can be executed on the remote host.\n\nDescription :\n\nThere is a flaw in the Task Scheduler application which could allow a\nremote attacker to execute code remotely.  There are many attack vectors\nfor this flaw.  An attacker, exploiting this flaw, would need to either\nhave the ability to connect to the target machine or be able to coerce a\nlocal user to either install a .job file or browse to a malicious\nwebsite.\n\nSee also :\n\nhttp://technet.microsoft.com/en-us/security/bulletin/ms04-022\n\nSolution :\n\nMicrosoft has released a set of patches for Windows 2000, XP and 2003.\n\nRisk factor :\n\nCritical / CVSS Base Score : 10.0\n(CVSS2#AV:N/AC:L/Au:N/C:C/I:C/A:C)\n\nPlugin output :\n- C:\\WINNT\\system32\\Shell32.dll has not been patched\n    Remote version : 5.0.3700.6705\n    Should be : 5.0.3900.6975\n\nCVE : CVE-2004-0212\nBID : 10708\nOther references : OSVDB:7798,MSFT:MS04-022\n</data></ReportItem>
		<ReportItem>
		*/
		$dataArray = array();
		$portTemp1 = explode(" ", $ReportItem->port);
		$service = htmlspecialchars($portTemp1[0], ENT_QUOTES);
		$portTemp2 = explode('/', trim($portTemp1[1], "()"));
		$port = htmlspecialchars($portTemp2[0], ENT_QUOTES);
		$protocol = htmlspecialchars($portTemp[1], ENT_QUOTES);
		$severity = htmlspecialchars($ReportItem->severity, ENT_QUOTES);
		$pluginID = htmlspecialchars($ReportItem->pluginID, ENT_QUOTES);
		
		if (array_key_exists($pluginID, $pluginFamilyIndex)) {
			$pluginFamily = htmlspecialchars($pluginFamilyIndex[$pluginID], ENT_QUOTES);
		} else {
			$pluginFamily = "Not in Index";
			$notInIndex[] = $pluginID;
		}
		$file = file_get_contents("http://www.tenable.com/plugins/index.php?view=single&id=?pluginID");
		if(preg_match("/<p><strong>Family:</strong>([\w:]+)<\/p>/",$file,$matches)){
			echo $matches[1] . "<br>";
		}
		$pluginName = htmlspecialchars($ReportItem->pluginName, ENT_QUOTES);
		$data = $ReportItem->data;
		$dataArray = explode('\n\n', $data);
		for($x=0;$x<count($dataArray);$x++){
			if($dataArray[$x] == 'Synopsis :'){
					$synopsis = htmlspecialchars($dataArray[$x+1]);
			}
			if($dataArray[$x] == 'Description :'){
					$description = "";
					for($y=$x+1;$y<$x+5;$y++){
						if($dataArray[$y] == 'See also :' || $dataArray[$y] == 'Solution :' || $dataArray[$y] == 'Risk factor :'){
							break;
						} else {
							$description = $description . "\n" . htmlspecialchars($dataArray[$y], ENT_QUOTES);
						}
					}
			}
			if($dataArray[$x] == 'See also :'){
					$see_also = htmlspecialchars($dataArray[$x+1]);
			}
			if($dataArray[$x] == 'Solution :'){
					$solution = "";
					for($y=$x+1;$y<$x+5;$y++){
						if($dataArray[$y] == 'Risk factor :'){
							break;
						} else {
							$solution = $solution . "\n" . htmlspecialchars($dataArray[$y], ENT_QUOTES);
						}
					}
			}
			if($dataArray[$x] == 'Risk factor :'){
					//Risk factor :\n\nCritical / CVSS Base Score : 10.0\n(CVSS2#AV:N/AC:L/Au:N/C:C/I:C/A:C)
					$temp1 = explode(' : ', $dataArray[$x+1]);
					$temp2 = explode(' / ', $temp1[0]);
					$risk_factor = trim($temp2[0], '\n');
					$temp3 = explode('\n', $temp1[1]);
					$cvss_base_score = htmlspecialchars($temp3[0], ENT_QUOTES);
					$cvss_vector = htmlspecialchars($temp3[1], ENT_QUOTES);
					$cvss_temporal_score = $cvss_temporal_vector = "";
			}
			if($dataArray[$x] == 'Plugin output :'){
				$plugin_output = htmlspecialchars($dataArray[$x+1]);
			}
			//CVE : CVE-2009-4074,CVE-2010-0027,CVE-2010-0244,CVE-2010-0245,CVE-2010-0246,CVE-2010-0247,CVE-2010-0248,CVE-2010-0249\nBID : 37815,37883,37884,37891,37892,37893,37894,37895\nOther references : OSVDB:60660,OSVDB:61697,OSVDB:61909,OSVDB:61910,OSVDB:61911,OSVDB:61912,OSVDB:61913,OSVDB:61914,MSFT:MS10-002,Secunia:38209,CWE:399\n
			if(preg_match("/CVE : ([CVE,-\d]+)/", $dataArray[$x], $cveArray)){
				$cveList = "," . $cveArray[1];
			}
			if(preg_match_all("/BID : ([\d,]*)/", $dataArray[$x], $bidArray)){
				$bidList = "," . implode(',',$bidArray[1]);
			}
			if(preg_match_all("/OSVDB:(\d+)/", $dataArray[$x], $osvdbArray)){
				$osvdbList = "," . implode(',',$osvdbArray[1]);
			}
			if(preg_match_all("/MSFT:(MS\d\d-\d+)/", $dataArray[$x], $msftArray)){
				$msftList = "," . implode(',',$msftArray[1]);
			}
			if(preg_match_all("/CERT:(\d+)/", $dataArray[$x], $certArray)){
				$certList = "," . implode(',',$certArray[1]);
			}
			if(preg_match_all("/IAVA:(\d+-A-\d+)/", $dataArray[$x], $iavaArray)){
				$iavaList = "," . implode(',',$iavaArray[1]);
			}
			if(preg_match_all("/CWE:(\d+)/", $dataArray[$x], $cweArray)){
				$cweList = "," . implode(',',$cweArray[1]);
			}
			if(preg_match_all("/Secunia:(\d+)/", $dataArray[$x], $secuniaArray)){
				$secuniaList = "," . implode(',',$secuniaArray[1]);
			}
		}
		$sql = "INSERT INTO nessus_results 	
					(
					agency, 
					bidList, 
					certList,
					cveList, 
					cvss_base_score, 
					cvss_temporal_score, 
					cvss_temporal_vector, 
					cvss_vector, 
					cweList,
					description, 
					iavaList, 
					msftList, 
					osvdbList, 
					plugin_output, 
					pluginFamily,
					pluginID, 
					pluginName, 
					port, 
					protocol, 
					report_name, 
					risk_factor, 
					scan_end, 
					scan_start, 
					secuniaList, 
					see_also, 
					service, 
					severity, 
					solution, 
					synopsis, 
					tagID
					) 
				VALUES 
					(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt = $db->prepare($sql);
		$sql_data = array($agency,$bidList,$certList,$cveList,$cvss_base_score,$cvss_temporal_score,$cvss_temporal_vector,$cvss_vector,$cweList,$description,$iavaList,$msftList,$osvdbList,$plugin_output,$pluginFamily,$pluginID,$pluginName,$port,$protocol,$report_name,$risk_factor,$scan_end,$scan_start,$secuniaList,$see_alsoList,$service,$severity,$solution,$synopsis,$tagID[0]);
		$stmt->execute($sql_data);
	}

}

$unique_notInIndex = array_unique($notInIndex);
if(!empty($unique_notInIndex)){
	echo "<p>This parse script found Nessus pluginIDs that are not in the pluginID.to.Family.csv.  This CSV file exists because version 1 of the Nessus XML files does not include the Plugin Family.</p>";
	echo "<p>I parsed all nasl files to create an index of plugin ID to plugin Family.  Some plugins are closed binary files so those will have to manually added to the index.</p>";
	echo "<p>Visit the links below to identify the plugin family and manually add it to the csv file in the nessus folder.  And if you like you can let the software author know at projectrf@jedge.com</p>";
	foreach($unique_notInIndex as $nI){
			echo "<a href=\"http://www.tenable.com/plugins/index.php?view=single&id=$nI\" target=\"_blank\">http://www.tenable.com/plugins/index.php?view=single&id=$nI</a><br>";
	}
}
?>
</td></tr></table>
</body>
</html>


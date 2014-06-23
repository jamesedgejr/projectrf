<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

$hostArray = $_POST["host"];
foreach($hostArray as $key => $value) {
	if ($value == "REMOVE") unset($hostArray[$key]);
}
$sql = "CREATE temporary TABLE nessus_tmp_hosts (host_name VARCHAR(255), INDEX ndx_host_name (host_name))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($hostArray as $hA){
	$sql="INSERT INTO nessus_tmp_hosts (host_name) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($hA));
}
$sql = "CREATE temporary TABLE nessus_tmp_low2pwn (pluginID VARCHAR(5), INDEX ndx_pluginID (pluginID))";
$stmt = $db->prepare($sql);
$stmt->execute();

$agency = $_POST["agency"];
$report_name = $_POST["report_name"];
$scan_start = $_POST["scan_start"];
$scan_end = $_POST["scan_end"];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title><?php echo "$agency - Nessus Vulnerability Report - $report_name";?></title>

<style type'text/css'>
table.main {
margin-left:auto;
margin-right:auto;
background-color: #a1a1a1;
width: 95%;
text-align: left
}
table.main2 {
margin-left:auto;
margin-right:auto;
background-color: #a1a1a1;
text-align: left
}
table.execMain {
background-color: #a1a1a1;
text-align: left
}
table.machine {
width: 100%;
text-align: left
}
td.top {
height: 20px;
color: #555555;
font-weight: bold;
font-family: tahoma;
font-size: 10pt;
background-color: #a2b5cd
}
td.left {
border-top: 1px solid #a1a1a1;
height: 18px;
width: 200px;
color: #555555;
font-weight: bold;
font-family: tahoma;
font-size: 10pt;
vertical-align: top;
background-color: #dcdcdc
}

pre.left {
height: 18px;
width: 200px;
color: #555555;
font-weight: bold;
font-family: tahoma;
font-size: 10pt;
vertical-align: top;
background-color: #dcdcdc
}

td.right {
border-top: 1px solid #a1a1a1;
border-left: 1px solid #a1a1a1;
color: #000000;
font-family: tahoma;
font-size: 8pt;
background-color: white
}

pre.right {
color: #000000;
font-family: tahoma;
font-size: 8pt;
background-color: white
}

td.line1 {
border-top: 1px solid #a1a1a1;
border-left: 1px solid #a1a1a1;
color: #000000;
font-family: tahoma;
font-size: 10pt;
vertical-align: top;
background-color: #dcdcdc

}
td.line2 {
border-top: 1px solid #a1a1a1;
border-left: 1px solid #a1a1a1;
color: #000000;
font-family: tahoma;
font-size: 10pt;
background-color: white
}
pre {
 white-space: pre-wrap;       /* css-3 */
 white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
 white-space: -pre-wrap;      /* Opera 4-6 */
 white-space: -o-pre-wrap;    /* Opera 7 */
 word-wrap: break-word;       /* Internet Explorer 5.5+ */
}
div.text-container {
    margin: 0 auto;   
}

.short-text {
    overflow: hidden;
    height: 5em;
}

.full-text{
    height: auto;
}

.show-more {
    padding: 5px 0;
    text-align: left;
}
</style>


</head>
<body>

<?php

$low2pwnArray = array();
if($_POST["isWebDAV"]) {
	$low2pwnArray = array("11424","24004"); 
}
//if($_POST["isTraceaxd"]){ array_push($low2pwnArray,"10993"); }
//if($_POST["isAFP"]){ array_push($low2pwnArray,"45374","45380","49289","49308"); }
if($_POST["isSharepoint"]){ 
	$low2pwnArray = array("38157"); 
	$low2pwnCategory = "Microsoft Sharepoint";
	printTable($low2pwnArray, $agency, $report_name, $scan_start, $scan_end, $low2pwnCategory, $db);
}
//if($_POST["isBrowsableDir"]){ array_push($low2pwnArray,"40984"); }
//if($_POST["isJbossTomcat"]){ array_push($low2pwnArray,"11218","33869","23842"); }
if($_POST["isNullSession"]){ 
	$low2pwnArray = array("26920","10398");
	$low2pwnCategory = "Null Session";
	printTable($low2pwnArray, $agency, $report_name, $scan_start, $scan_end, $low2pwnCategory, $db); 
}
//if($_POST["isFCKeditor"]){ array_push($low2pwnArray,"17239","21187","21573","21780","24003","39790","39806"); }
//if($_POST["isNFS"]){ array_push($low2pwnArray,"11356","42256"); }
//if($_POST["isAdmin"]){ array_push($low2pwnArray,"18183","18184","18185","17219","10273","11123","12295","17219","20345","20727","22016","24239","25089","25763","35724","43401","44328","44392","45344"); }
//if($_POST["isServiceDetect"]){ array_push($low2pwnArray,"42339","10942"); }

?>
</body>
</html>
<?php
function printTable($low2pwnArray, $agency, $report_name, $scan_start, $scan_end, $low2pwnCategory, $db) {
	$sql = "TRUNCATE TABLE nessus_tmp_low2pwn";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	foreach ($low2pwnArray as $l2pA){
		$sql="INSERT INTO nessus_tmp_low2pwn (pluginID) VALUES (?)";
		$stmt = $db->prepare($sql);
		$stmt->execute(array($l2pA));
	}
	
	$main_sql = "SELECT DISTINCT
	nessus_results.pluginID,
	nessus_results.description,
	nessus_results.pluginFamily,
	nessus_results.pluginName,
	nessus_results.synopsis
	FROM
	nessus_results
	INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
	INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
	INNER JOIN nessus_tmp_low2pwn ON nessus_tmp_low2pwn.pluginID = nessus_results.pluginID
	WHERE
		nessus_results.agency = ? AND 
		nessus_results.report_name = ? AND
		nessus_results.scan_start = ? AND
		nessus_results.scan_end = ?
	";

	$where_data = array($agency, $report_name, $scan_start, $scan_end);
	$main_stmt = $db->prepare($main_sql);
	$main_stmt->execute($where_data);
	if(!$main_stmt->rowCount()){
		echo "<hr><p align=\"center\"><b>No Rows were returned.  You didn't select any hosts or none of the hosts you selected are vulnerable to any \"low 2 pwn\" issues.</b></p><hr>";
	}
	while($row = $main_stmt->fetch(PDO::FETCH_ASSOC)){
		$pluginID = $row["pluginID"];
		$description = str_replace("\n\n","<br>", $row["description"]);
		$pluginFamily = $row["pluginFamily"];
		$pluginName = $row["pluginName"];
		$synopsis = str_replace("\n\n","<br>", $row["synopsis"]);
	
	
	?>
	
	
	<table width="100%" class="main">
	<tr>
		<td class="top"><p>
			<?php echo "[$pluginFamily]:  $pluginName"; ?>
		</p></td>
	</tr>	
	</table>
	
	
	<table width="100%" class="main">
	<tr>
		<td class="left"><p>Synopsis:</p></td>
		<td class="right">
			<p><?php
				echo "$synopsis<br>";
			?></p>
		</td>
	</tr>
	<tr>
		<td class="left"><p>Description:</p></td>
		<td class="right">
			<p><?php
					echo "$description<br>";
			?></p>
		</td>
	</tr>
	</table>
	
	<?php
	
	$host_sql = "SELECT DISTINCT
	nessus_tags.host_name,
	nessus_results.plugin_output,
	nessus_results.`port`,
	nessus_results.protocol,
	nessus_results.service,
	nessus_tags.fqdn,
	nessus_tags.ip_addr,
	nessus_tags.mac_addr,
	nessus_tags.netbios,
	nessus_tags.operating_system
	FROM
	nessus_results
	INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
	INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
	INNER JOIN nessus_tmp_low2pwn ON nessus_tmp_low2pwn.pluginID = nessus_results.pluginID
	WHERE
		nessus_results.pluginID = ? AND
		nessus_results.agency = ? AND
		nessus_results.report_name = ? AND
		nessus_results.scan_start = ? AND
		nessus_results.scan_end = ?
	";
	
	$host_stmt = $db->prepare($host_sql);
	$data = array($pluginID, $agency, $report_name, $scan_start, $scan_end);
	$host_stmt->execute($data);
	$num_returned_hosts = $host_stmt->rowCount();
	
	
	?>
	<table width="100%" class="main">
	<tr>
		<td class="left"><p>Affected Machines: <?php if($num_returned_hosts > "5"){echo"($num_returned_hosts)";}?></p></td>
		<td class="right">
			<table class="machine">
			<tr>
			<td class="top"><p>IP Address</p></td>
			<td class="top"><p>MAC</p></td>
			<td class="top"><p>FQDN</p></td>
			<td class="top"><p>NetBIOS</p></td>
			<td class="top"><p>OS</p></td>
			<td class="top"><p>Service</p></td>
			<td class="top"><p>Protocol</p></td>
			</tr>						
			<?php
			while($host_row = $host_stmt->fetch(PDO::FETCH_ASSOC)) {
				$fqdn = $host_row["fqdn"];
				$host_name = $host_row["host_name"];
				$ip_addr = $host_row["ip_addr"];
				$mac_addr = $host_row["mac_addr"];
				$netbios = $host_row["netbios"];
				$operating_system = $host_row["operating_system"];
				$plugin_output = filter_var($host_row["plugin_output"], FILTER_SANITIZE_SPECIAL_CHARS);
				$port = $host_row["port"];
				$protocol = $host_row["protocol"];
				$service = $host_row["service"];
			?>
			<tr>
			<td class="right"><p><?php if($ip_addr == ""){ echo "$host_name";} else {echo"$ip_addr";}?></p></td>
			<td class="right"><p><?php echo "$mac_addr";?></p></td>
			<td class="right"><p><?php echo"$fqdn ";?></p></td>
			<td class="right"><p><?php echo "$netbios";?></p></td>
			<td class="right"><p><?php echo"$operating_system";?></p></td>
			<td class="right"><p><?php echo"$service($port)";?></p></td>
			<td class="right"><p><?php echo"$protocol";?></p></td>
			</tr>
			<tr><td class="right" colspan="7"><pre><?php echo $plugin_output; ?></pre></td></tr>
			<?php
			}//end while
			?>
	</table>
	</td></tr></table>       
	<br><br>

<?php 
	}//endwhile

}//end function
/*
WEBDAV
11424 (WebDAV Detection)
24004 (WebDAV Directory Enumeration)

Trace.axd
10993 (Microsoft ASP.NET Application Tracing trace.axd Information Disclosure)

SHarepoint
38157 (Microsoft SharePoint Server Detection)

Null Session - Honorable Mention
26920 (Microsoft Windows SMB NULL Session Authentication)
10398 (Microsoft Windows SMB LsaQueryInformationPolicy Function NULL Session Domain SID Enumeration)

NFS - Honorable Mention
11356 (NFS Exported Share Information Disclosure)
42256 (NFS Shares World Readable)

FCKeditor - Honorable Mention
17239	FCKeditor for PHP-Nuke Arbitrary File Upload
21187	CubeCart FCKeditor connector.php Arbitrary File Upload
21573	FCKeditor upload.php Type Parameter Arbitrary File Upload
21780	FCKeditor on Apache connector.php Crafted File Extension Arbitrary File Upload
24003	Cuyahoga FCKEditor Misconfiguration Unrestricted File Upload
39790	Adobe ColdFusion FCKeditor 'CurrentFolder' File Upload
39806	FCKeditor 'CurrentFolder' Arbitrary File Upload

AFP
45374	AFP Server Directory Traversal
45380	AFP Server Share Enumeration (guest)
49289	Mac OS X AFP Shared Folders Unauthenticated Access (Security Update 2010-006)
49308	Mac OS X AFP Shared Folders Unauthenticated Access (Security Update 2010-006) (uncredentialed check)

Browsable Directories
40984	Browsable Web Directories

JBoss / Tomcat
11218 (Tomcat /status Information Disclosure)
33869 (JBoss Enterprise Application Platform (EAP) Status Servlet Request Remote Information Disclosure)
23842 (JBoss JMX Console Unrestricted Access)

Log File Injection - Honorable Mention

*/

?>


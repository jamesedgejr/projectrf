<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

$v1 = new Valitron\Validator($_POST);
$v1->rule('accepted', ['isPlugName','isPlugFam']);
$v1->rule('numeric', ['scan_start', 'scan_end']);
$v1->rule('slug','agency');
$v1->rule('alpha','pivot');
$v1->rule('regex','report_name', '/^([\w\s_.\[\]():;@-])+$/'); //regex includes alpha/numeric, space, underscore, dash, period, white space, brackets, parentheses, colon, "at" symbol, and semi-colon
$v1->rule('length',1,['critical','high','medium','low','info']);
$v1->rule('integer',['critical','high','medium','low','info']);
if(!$v1->validate()) {
    print_r($v1->errors());
	exit;
} 

$hostArray = $_POST["host"];
foreach($hostArray as $key => $value) {
	if ($value == "REMOVE") unset($hostArray[$key]);
}
$sql = "CREATE temporary TABLE nessus_tmp_hosts (host_name VARCHAR(255), INDEX ndx_host_name (host_name))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($hostArray as $hA){
	$v2 = new Valitron\Validator(array('host' => $hA));
	$v2->rule('regex','host', '/^([\w.-])+$/');
	if(!$v2->validate()) {
		print_r($v2->errors());
		exit;
	} 
	$sql="INSERT INTO nessus_tmp_hosts (host_name) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($hA));
}
$family = $_POST["family"];
$sql = "CREATE temporary TABLE nessus_tmp_family (pluginFamily VARCHAR(255), INDEX ndx_pluginFamily (pluginFamily))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($family as $f){
	$v3 = new Valitron\Validator(array('family' => $f));
	$v3->rule('regex','family', '/^([\w :.-])+$/');//regex includes alpha/numeric, space, colon, dash, and period
	if(!$v3->validate()) {
		print_r($v3->errors());
		exit;
	} 
	$sql="INSERT INTO nessus_tmp_family (pluginFamily) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($f));
}

$critical = $_POST["critical"];	
$high = $_POST["high"];
$medium = $_POST["medium"];
$low  = $_POST["low"];
$info = $_POST["info"];
$sArray = array($critical, $high, $medium, $low, $info);
$sql = "CREATE temporary TABLE nessus_tmp_severity (severity VARCHAR(255), INDEX ndx_severity (severity))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($sArray as $s){
	if($s != ""){
		$sql="INSERT INTO nessus_tmp_severity (severity) VALUES (?)";
		$stmt = $db->prepare($sql);
		$stmt->execute(array($s));
	}
}

$agency = $_POST["agency"];
$report_name = $_POST["report_name"];
$scan_start = $_POST["scan_start"];
$scan_end = $_POST["scan_end"];
$isPlugName = $_POST["isPlugName"];
$isPlugFam = $_POST["isPlugFam"];
$pivot = $_POST["pivot"];

date_default_timezone_set('UTC');
$myDir = getcwd() . "/csvfiles/";
$myFileName = $agency . "_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");
?>
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title>NESSUS VULNERABILITY MATRIX</title>
<style type="text/css">
p {font-size: 90%}
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
		<hr>
<?php
if($isPlugName != "yes" && $isPlugFam != "yes"){
			echo "You need to select either Plugin Name, Plugin Family, or both.  Deselecting both of them will produce a really useless report.1";
?>
		<hr>
	</td>
</tr></table>
</body>
</html>
<?php
exit;
}

//hosts are left side with vulnerabilities across the top
if($pivot == "left"){
	fwrite($fh, "\"\",\"\",\"\",");
	$sql = "SELECT DISTINCT
				nessus_results.pluginID,
				nessus_results.pluginName,
				nessus_results.pluginFamily,
				nessus_results.risk_factor,
				nessus_results.severity,
				nessus_results.cvss_base_score
			FROM
				nessus_results
			INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
			INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
			INNER JOIN nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
			INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
			WHERE
				nessus_results.agency = ? AND 
				nessus_results.report_name = ? AND
				nessus_results.scan_start = ? AND
				nessus_results.scan_end = ?
			ORDER BY 
				nessus_results.cvss_base_score DESC
			";
	$data = array($agency, $report_name, $scan_start, $scan_end);
	$stmt = $db->prepare($sql);
	$stmt->execute($data);
	$pluginData = $stmt->fetchAll(PDO::FETCH_ASSOC);
	//$pluginData =& $db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
	foreach($pluginData as $pD){
		$pluginName = $pD["pluginName"];
		$pluginName = str_replace("&#039;","'",$pluginName);
		$pluginName = str_replace("&lt;","<",$pluginName);
		if($isPlugName == "yes"){
			fwrite($fh, "\"$pluginName\"");
		}
		fwrite($fh, ",");
	}
	fwrite($fh, "\n");
	
	fwrite($fh, "\"\",\"\",\"\",");
	foreach($pluginData as $pD){
		$pluginFamily = $pD["pluginFamily"];
		$pluginFamily = str_replace("&#039;","'", $pluginFamily);
		$pluginFamily = str_replace("&lt;","<", $pluginFamily);
		if($isPlugFam == "yes"){
			fwrite($fh, "\"$pluginFamily\"");
		}
		fwrite($fh, ",");
	}
	fwrite($fh, "\n");
	fwrite($fh, "\"\",\"\",\"\",");
	foreach($pluginData as $pD){
		$cvss_base_score = $pD["cvss_base_score"];
		$risk_factor_letter = substr($pD["risk_factor"],0,1);
		fwrite($fh, "\"$risk_factor_letter ($cvss_base_score)\",");
	}
	
	fwrite($fh, "\n");
	foreach($hostArray as $hA){
		fwrite($fh, "\"$hA\",");
		//I think having the fqdn and netbios (if available) would be nice!
		
		$host_sql = "SELECT DISTINCT
						nessus_tags.host_name,
						nessus_tags.fqdn,
						nessus_tags.operating_system,
						nessus_tags.netbios
					FROM
						nessus_tags
					Inner Join nessus_results ON nessus_results.tagID = nessus_tags.tagID
					Inner Join nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
					Inner Join nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
					Inner Join nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
					WHERE
						nessus_tags.host_name =  ? AND
						nessus_results.agency =  ? AND
						nessus_results.report_name =  ? AND
						nessus_results.scan_start =  ? AND
						nessus_results.scan_end =  ? 
						
					";
		$host_data = array($hA, $agency, $report_name, $scan_start, $scan_end);
		$host_stmt = $db->prepare($host_sql);
		$host_stmt->execute($host_data);
		$host_row = $host_stmt->fetch(PDO::FETCH_ASSOC);
		if($host_row["fqdn"] == ""){
			$hostname = $host_row["netbios"];
		} else {
			$hostname = $host_row["fqdn"];
		}
		$os = $host_row["operating_system"];
		$os = str_replace("Enterprise", "Ent", $os);
		$os = str_replace("Standard", "Std", $os);
		$os = str_replace("Service Pack", "SP", $os);
		$os = str_replace("Microsoft", "", $os);
		$os = str_replace("Edition", "Ed", $os);
		$os = str_replace("(English)", "", $os);
		fwrite($fh, "\"$hostname\",\"$os\",");
		foreach($pluginData as $pD){
			$pluginID = $pD["pluginID"];
			$lookup_sql = "SELECT DISTINCT
							nessus_results.pluginID
						FROM
							nessus_tags
						Inner Join nessus_results ON nessus_results.tagID = nessus_tags.tagID
						Inner Join nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
						Inner Join nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
						Inner Join nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
						WHERE
							nessus_tags.host_name = ? AND
							nessus_results.agency = ? AND
							nessus_results.report_name = ? AND
							nessus_results.scan_start = ? AND
							nessus_results.scan_end = ? AND
							nessus_results.pluginID = ?
						";
			$lookup_data = array($hA, $agency, $report_name, $scan_start, $scan_end, $pluginID);
			$lookup_stmt = $db->prepare($lookup_sql);
			$lookup_stmt->execute($lookup_data);
			$num_rows = count($lookup_stmt->fetchAll());
			if($num_rows){
				fwrite($fh, "\"X\",");
			} else {
				fwrite($fh, "\"\",");
			}
		}//end foreach
		fwrite($fh, "\n");
	}//end OF HOST FOREACH
} else {//end pivot left ---------------------------------------------------------------------------------------------------------------------------------------------
//hosts are across the top and vulnerabilities are along the left side

	if($isPlugFam == "yes" && $isPlugName == "yes"){
		$spacer = "\"\",\"\",\"\",";
	} elseif ($isPlugFam != "yes" && $isPlugName == "yes"){
		$spacer = "\"\",\"\",";
	} elseif ($isPlugFam == "yes" && $isPlugName != "yes"){
		$spacer = "\"\",\"\",";
	} else {
		echo "You need to select either Plugin Name, Plugin Family, or both.  Deselecting both of them will produce a really useless report.2";
		exit;
	}

	$host_sql = "SELECT DISTINCT
				nessus_tags.host_name,
				nessus_tags.ip_addr,
				nessus_tags.fqdn,
				nessus_tags.operating_system,
				nessus_tags.netbios
			FROM
				nessus_tags
			Inner Join nessus_results ON nessus_results.tagID = nessus_tags.tagID
			Inner Join nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
			Inner Join nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
			Inner Join nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
			WHERE
				nessus_results.agency =  ? AND
				nessus_results.report_name =  ? AND
				nessus_results.scan_start =  ? AND
				nessus_results.scan_end =  ? 
			ORDER BY 
				nessus_tags.ip_addr ASC 
			";
	$host_data = array($agency, $report_name, $scan_start, $scan_end);
	$host_stmt = $db->prepare($host_sql);
	$host_stmt->execute($host_data);
	$host_row = $host_stmt->fetchAll(PDO::FETCH_ASSOC);
	fwrite($fh, $spacer);
	foreach($host_row as $hR){
		$ip_addr = $hR["ip_addr"];
		fwrite($fh, "\"$ip_addr\",");
	}
	fwrite($fh, "\n");
	fwrite($fh, $spacer);
	foreach($host_row as $hR){
		if($host_row["fqdn"] == ""){
			$hostname = $hR["netbios"];
		} else {
			$hostname = $hR["fqdn"];
		}
		fwrite($fh, "\"$hostname\",");
	}
	fwrite($fh, "\n");
	fwrite($fh, $spacer);
	foreach($host_row as $hR){
		$os = $hR["operating_system"];
		$os = str_replace("Enterprise", "Ent", $os);
		$os = str_replace("Standard", "Std", $os);
		$os = str_replace("Service Pack", "SP", $os);
		$os = str_replace("Microsoft", "", $os);
		$os = str_replace("Edition", "Ed", $os);
		$os = str_replace("(English)", "", $os);
		fwrite($fh, "\"$os\",");
	}
	fwrite($fh, "\n");
	$pluginID_sql = "SELECT DISTINCT
				nessus_results.pluginID,
				nessus_results.pluginName,
				nessus_results.pluginFamily,
				nessus_results.cvss_base_score,
				nessus_results.risk_factor
			FROM
				nessus_results
			INNER JOIN nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
			INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
			WHERE
				nessus_results.agency =  ? AND
				nessus_results.report_name =  ? AND
				nessus_results.scan_start =  ? AND
				nessus_results.scan_end =  ? 
			ORDER BY nessus_results.cvss_base_score DESC
			";
	$pluginID_data = array($agency, $report_name, $scan_start, $scan_end);
	$pluginID_stmt = $db->prepare($pluginID_sql);
	$pluginID_stmt->execute($pluginID_data);
	$pluginID_row = $pluginID_stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($pluginID_row as $pR){
		$pluginID = $pR["pluginID"];
		$pluginName = $pR["pluginName"];
		$pluginName = str_replace("&#039;","'",$pluginName);
		$pluginName = str_replace("&lt;","<",$pluginName);
		$pluginFamily = $pR["pluginFamily"];
		$pluginFamily = str_replace("&#039;","'",$pluginFamily);
		$pluginFamily = str_replace("&lt;","<",$pluginFamily);
		if($isPlugFam == "yes" && $isPlugName == "yes"){
			fwrite($fh, "\"$pluginFamily\",\"$pluginName\",");
		} elseif ($isPlugFam != "yes" && $isPlugName == "yes"){
			fwrite($fh, "\"$pluginName\",");
		} elseif ($isPlugFam == "yes" && $isPlugName != "yes"){
			fwrite($fh, "\"$pluginFamily\",");
		} else {
			echo "You need to select either Plugin Name, Plugin Family, or both.  Deselecting both of them will produce a really useless report.3";
			exit;
		}
		$cvss_base_score = $pR["cvss_base_score"];
		$risk_factor_letter = substr($pR["risk_factor"],0,1);
		fwrite($fh, "\"$risk_factor_letter ($cvss_base_score)\",");
		//we need to lookup and find if the host is vulnerable to the vulnerability
		foreach($host_row as $hR){
			$host_name = $hR["host_name"];
			$lookup_sql = "SELECT DISTINCT
							nessus_results.pluginID
					FROM
						nessus_tags
					Inner Join nessus_results ON nessus_results.tagID = nessus_tags.tagID
					Inner Join nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
					Inner Join nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
					Inner Join nessus_tmp_severity ON nessus_tmp_severity.severity = nessus_results.severity
					WHERE
						nessus_tags.host_name = ? AND
						nessus_results.agency = ? AND
						nessus_results.report_name = ? AND
						nessus_results.scan_start = ? AND
						nessus_results.scan_end = ? AND 
						nessus_results.pluginID = ?		
			";
			$lookup_data = array($host_name, $agency, $report_name, $scan_start, $scan_end, $pluginID);
			$lookup_stmt = $db->prepare($lookup_sql);
			$lookup_stmt->execute($lookup_data);
			$num_rows = count($lookup_stmt->fetchAll(PDO::FETCH_ASSOC));
			if($num_rows == 0){
				fwrite($fh, "\"\",");
			}
			else {
				fwrite($fh, "\"X\",");
			}
		}
		fwrite($fh, "\n");
		
	}
}


?>


		<p align="center"><a href="csvfiles/<?php echo "$myFileName";?>">Click Here</a> to download the CSV file.</p>
		<hr>
	</td>
</tr></table>
</body>
</html>

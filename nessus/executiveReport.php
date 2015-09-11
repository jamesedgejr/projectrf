<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v1 = new Valitron\Validator($_POST);
$v1->rule('accepted', ['cover']);
$v1->rule('numeric', ['scan_start', 'scan_end']);
$v1->rule('slug', ['agency', 'byVuln']);
$v1->rule('regex','report_name', '/^([\w _.-])+$/'); //regex includes alpha/numeric, space, underscore, dash, and period
$v1->rule('regex',['w1','w2'], '/^([\w\s_.\[\]():;-])+$/'); //regex includes alpha/numeric, space, underscore, dash, period, white space, brackets, parentheses, colon, and semi-colon
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

$agency = $_POST["agency"];
$report_name = $_POST["report_name"];
$scan_start = $_POST["scan_start"];
$scan_end = $_POST["scan_end"];
$byVuln = $_POST["byVuln"];

$diff_seconds = $scan_end - $scan_start;
$diff_hours = floor($diff_seconds/3600);
$diff_seconds -= $diff_hours * 3600;
$diff_minutes = floor($diff_seconds/60);
$diff_seconds -= $diff_minutes * 60;


/*------------------------------------------------------------------
    PHP Code for gathering data for Host Vulnerability Statistics 
--------------------------------------------------------------------*/
$sql = 	"SELECT DISTINCT
	nessus_tags.host_name
FROM
	nessus_results
INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
WHERE
	nessus_results.agency = ? AND 
	nessus_results.report_name = ? AND
	nessus_results.scan_start = ? AND
	nessus_results.scan_end = ?
";
$whereArray = array($agency, $report_name, $scan_start, $scan_end);
$stmt = $db->prepare($sql);
$stmt->execute($whereArray);
$exec_hosts["Totals"] = array(critical => "0", high => "0", medium => "0", low => "0", info => "0");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	$host = $row["host_name"];
	$exec_hosts[$host] = array(critical => "0", high => "0", medium => "0", low => "0", info => "0");
}

	$sql = "SELECT
	  nessus_tags.host_name,
	  nessus_results.severity,
	  nessus_results.cveList
	FROM
	  nessus_results
	INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
	INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
	INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
	WHERE
		nessus_results.agency = ? AND 
		nessus_results.report_name = ? AND
		nessus_results.scan_start = ? AND
		nessus_results.scan_end = ?
	";
	$stmt = $db->prepare($sql);
	$stmt->execute($whereArray);
	$total_rows = $stmt->rowCount();
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$severity = $row["severity"];
		$host_name = $row["host_name"];
		$cveList = explode(",", $row["cveList"]);
		$cveCount = count($cveList) - 1;		
		if($byVuln == "plugin"){
			switch ($severity) {
				case "4":
					$exec_hosts[$host_name]["critical"]++;
					$exec_hosts["Totals"]["critical"]++;
					break;
				case "3":
					$exec_hosts[$host_name]["high"]++;
					$exec_hosts["Totals"]["high"]++;
					break;
				case "2":
					$exec_hosts[$host_name]["medium"]++;
					$exec_hosts["Totals"]["medium"]++;
					break;
				case "1":
					$exec_hosts[$host_name]["low"]++;
					$exec_hosts["Totals"]["low"]++;
					break;
				case "0":
					$exec_hosts[$host_name]["info"]++;
					$exec_hosts["Totals"]["info"]++;
					break;
			}
		}
		if($byVuln == "cve"){
			switch ($severity) {
				case "4":
					$exec_hosts[$host_name]["critical"]=$exec_hosts[$host_name]["critical"]+$cveCount;
					$exec_hosts["Totals"]["critical"]=$exec_hosts["Totals"]["critical"]+$cveCount;
					break;
				case "3":
					$exec_hosts[$host_name]["high"]=$exec_hosts[$host_name]["high"]+$cveCount;
					$exec_hosts["Totals"]["high"]=$exec_hosts["Totals"]["high"]+$cveCount;
					break;
				case "2":
					$exec_hosts[$host_name]["medium"]=$exec_hosts[$host_name]["medium"]+$cveCount;
					$exec_hosts["Totals"]["medium"]=$exec_hosts["Totals"]["medium"]+$cveCount;
					break;
				case "1":
					$exec_hosts[$host_name]["low"]=$exec_hosts[$host_name]["low"]+$cveCount;
					$exec_hosts["Totals"]["low"]=$exec_hosts["Totals"]["low"]+$cveCount;
					break;
				case "0":
					$exec_hosts[$host_name]["info"]=$exec_hosts[$host_name]["info"]+$cveCount;
					$exec_hosts["Totals"]["info"]=$exec_hosts["Totals"]["info"]+$cveCount;
					break;
			}
		}
		
		
		
		uasort($exec_hosts, 'sortByHigh');
	}
/*---------------------------------------------------------------
    PHP Code for gathering data for Operating System Statistics 
-----------------------------------------------------------------*/

$os_sql = "SELECT DISTINCT
nessus_tags.operating_system
FROM
nessus_results
INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
INNER JOIN nessus_tmp_family ON nessus_results.pluginFamily = nessus_tmp_family.pluginFamily
INNER JOIN nessus_tmp_hosts ON nessus_tags.host_name = nessus_tmp_hosts.host_name
WHERE
	nessus_results.agency = ? AND 
	nessus_results.report_name = ? AND
	nessus_results.scan_start = ? AND
	nessus_results.scan_end = ?
";
$os_stmt = $db->prepare($os_sql);
$os_stmt->execute($whereArray);
while ($os_row = $os_stmt->fetch(PDO::FETCH_ASSOC)){
	$operating_system = $os_row["operating_system"];
	$operating_system = str_replace('\n', " or<br>", $operating_system);
	$exec_os[$operating_system] = array(critical => "0", high => "0", medium => "0", low => "0", info => "0");
}
$sql = "SELECT
nessus_tags.operating_system,
nessus_results.severity,
nessus_results.cveList
FROM
nessus_results
INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
INNER JOIN nessus_tmp_family ON nessus_results.pluginFamily = nessus_tmp_family.pluginFamily
INNER JOIN nessus_tmp_hosts ON nessus_tags.host_name = nessus_tmp_hosts.host_name
WHERE
	nessus_results.agency = ? AND 
	nessus_results.report_name = ? AND
	nessus_results.scan_start = ? AND
	nessus_results.scan_end = ?
";
$stmt = $db->prepare($sql);
$stmt->execute($whereArray);
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	$severity = $row["severity"];
	$operating_system = $row["operating_system"];
	$operating_system = str_replace('\n', " or<br>", $operating_system);
	$cveList = explode(",", $row["cveList"]);
	$cveCount = count($cveList) - 1;	
	if($byVuln == "plugin"){
		switch ($severity) {
			case "4":
				$exec_os[$operating_system]["critical"]++;
				break;
			case "3":
				$exec_os[$operating_system]["high"]++;
				break;
			case "2":
				$exec_os[$operating_system]["medium"]++;
				break;
			case "1":
				$exec_os[$operating_system]["low"]++;
				break;
			case "0":
				$exec_os[$operating_system]["info"]++;
				break;
		}
	}
	if($byVuln=="cve"){
		switch ($severity) {
			case "4":
				$exec_os[$operating_system]["critical"]=$exec_os[$operating_system]["critical"]+$cveCount;
				break;
			case "3":
				$exec_os[$operating_system]["high"]=$exec_os[$operating_system]["high"]+$cveCount;
				break;
			case "2":
				$exec_os[$operating_system]["medium"]=$exec_os[$operating_system]["medium"]+$cveCount;
				break;
			case "1":
				$exec_os[$operating_system]["low"]=$exec_os[$operating_system]["low"]+$cveCount;
				break;
			case "0":
				$exec_os[$operating_system]["info"]=$exec_os[$operating_system]["info"]+$cveCount;
				break;
		}
	}
}
uasort($exec_os, 'sortByHigh');
/*---------------------------------------------------------------
    PHP Code for gathering data for Nessus Plugin Family 
-----------------------------------------------------------------*/
$fam_sql = "SELECT
	nessus_results.pluginFamily,
	Count(nessus_results.severity) AS sevCount
FROM
	nessus_results
INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
WHERE
	nessus_results.agency = ? AND 
	nessus_results.report_name = ? AND
	nessus_results.scan_start = ? AND
	nessus_results.scan_end = ? AND
	nessus_results.pluginID != '0' AND 	
	nessus_results.severity != '0'
GROUP BY
	nessus_results.pluginFamily
ORDER BY
	sevCount DESC
LIMIT 0, 3
";
$fam_stmt = $db->prepare($fam_sql);
$fam_stmt->execute($whereArray);
while ($fam_row = $fam_stmt->fetch(PDO::FETCH_ASSOC)){
	$pluginFamily = $fam_row["pluginFamily"];
	$exec_fam[$pluginFamily] = array(critical => "0", high => "0", medium => "0", low => "0");
	
	$sql = "SELECT
		nessus_results.pluginFamily,
		nessus_results.severity,
		nessus_results.cveList
	FROM
		nessus_results
	INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
	INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
	INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
	WHERE
		nessus_results.agency = ? AND 
		nessus_results.report_name = ? AND
		nessus_results.scan_start = ? AND
		nessus_results.scan_end = ? AND
		nessus_results.pluginFamily = ?
	";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($agency, $report_name, $scan_start, $scan_end, $pluginFamily));
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$severity = $row["severity"];
		$pluginFamily = $row["pluginFamily"];
		$cveList = explode(",", $row["cveList"]);
		$cveCount = count($cveList) - 1;
		if($byVuln == "plugin"){
			switch ($severity) {
				case "4":
					$exec_fam[$pluginFamily]["critical"]++;
					break;
				case "3":
					$exec_fam[$pluginFamily]["high"]++;
					break;
				case "2":
					$exec_fam[$pluginFamily]["medium"]++;
					break;
				case "1":
					$exec_fam[$pluginFamily]["low"]++;
					break;
			}
		}
		if($byVuln == "cve"){
			switch ($severity) {
				case "4":
					$exec_fam[$pluginFamily]["critical"]=$exec_fam[$pluginFamily]["critical"]+$cveCount;
					break;
				case "3":
					$exec_fam[$pluginFamily]["high"]=$exec_fam[$pluginFamily]["high"]+$cveCount;
					break;
				case "2":
					$exec_fam[$pluginFamily]["medium"]=$exec_fam[$pluginFamily]["medium"]+$cveCount;
					break;
				case "1":
					$exec_fam[$pluginFamily]["low"]=$exec_fam[$pluginFamily]["low"]+$cveCount;
					break;
			}
		}
	}
}
/* -------------------------------------------------------- */
$whocreated = str_replace("\n","<br>", $_POST["w1"]);
$whofor = str_replace("\n","<br>", $_POST["w2"]);
$cover = $_POST["cover"];
$isStyle = "style_nessus.css";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title><?php echo "$agency - Nessus Vulnerability Report";?></title>
<link rel="stylesheet" type="text/css" href="../main/<?php echo "$isStyle";?>" />
</head>
<body>
<?php
//include cover page
if($cover == "yes"){
?>
	<table class="execMain" style="width: 850px;">
		<tr>
			<td class="top" style="text-align:center;">
				<p>NESSUS - Network Vunlerability Scanner</p>
			</td>
		</tr>
	</table><br>
	<table class="execMain" style="width: 850px;">
		<tr>
			<td colspan="2" class="top" style="text-align: center;font-size: 20pt;">
				<p>Confidential Information</p>

			</td>
		</tr>
		<tr>
			<td colspan="2" class="right">
				<p>The following report contains confidential information. Do not
				distribute, email, fax, or transfer via any electronic mechanism unless
				it has been approved by the recipient company's security policy. All
				copies and backups of this document should be saved on protected
				storage at all times. Do not share any of the information contained
				within this report with anyone unless they are authorized to view the
				information.</p>
			</td>
		</tr>
		<tr>
			<td class="left">
				<p><b>Created By:</b></p>
			</td>
			<td class="left">
				<p><b>Created For:</b></p>
			</td>
		</tr>
		<tr>
			<td width="50%" valign="top" class="right">
				<p><?php echo "$whocreated";?></p>
			</td>
			<td width="50%" valign="top" class="right">
				<p><?php echo "$whofor";?></p>
			</td>
		</tr>
	</table>
	<br style="page-break-after: always;" clear="all">
<?php
}//end include cover page
?>
<table width="850px"><tr><td width="600px"></td><td class="right" align="left"><p>Report Created:  <?php echo date("F j, Y, g:i a");?></p></td></tr></table>
<table width="850px">
	<tr>
		<td class="top" width="850px" style=" float:left; height:3px; width:850px;"></td>
	</tr>
</table>
<table width="850px">
	<tr>
		<td class="left" style="width:150px"><p>Scan Name:</p></td>
		<td class="right"><p><?php echo "$agency - $report_name" ?></p></td>
		<td class="left" style="width:150px"><p></p>Scan Start:</p></td>
		<td class="right"><p><?php echo date("F j, Y, g:i a", $scan_start); ?></p></td>
	</tr>
	<tr>
		<td class="left" style="width:100px"><p>Scan Duration:</p></td>
		<td class="right"><p><?php printf('%d hours, %d minutes, %d seconds', $diff_hours, $diff_minutes, $diff_seconds); ?></p></td>
		<td class="left" style="width:100px"><p>Scan End:</p></td>
		<td class="right"><p><?php echo date("F j, Y, g:i a", $scan_end); ?></p></td>
	</tr>
</table>
<table width="850px">
	<tr><td class="left">Nessus Plugin Families</td></tr>
	<tr><td class="right">
	<?php  
		$printFamily = "";
		foreach($family as $f){ 
			if($f == ""){
				$printFamily .= "Information Only, ";
			} else {
				$printFamily .= "$f, ";
			} 
		}
		$printFamily = substr($printFamily,0,-2);
		echo "$printFamily";	
	?>
	</td></tr>
</table>
<table width="850px">
	<tr><td class="left">Selected Hosts</td></tr>
	<tr><td class="right">
	<?php  
		$lineCount = 1;
		echo "<table><tr>";
		foreach($hostArray as $hA){ 
			if($lineCount%10 == 1) {	echo "<tr>"; }
			echo "<td class=right><p>" . $hA . "</p></td>";
			if($lineCount%10 == 0) {	echo "</tr>"; }
			$lineCount++;
		}
		if($lineCount%10 != 0) { echo "</tr></table>"; } else { echo "</table>";};
	?>
	</td></tr>
</table>
<table width="850px">
	<tr>
		<td class="top" width="850px" style=" float:left; height:3px; width:850px;"></td>
	</tr>
</table>
</br></br>
<table width="850px">
  <tr><td valign="top" class="top" width="300">
    <table>
	  <tr>
        <td class="top" align="center" valign="top"><img src="<?php echo "images/vuln_chart.php?critical=" . $exec_hosts["Totals"]["critical"] . "&high=" . $exec_hosts["Totals"]["high"] . "&medium=" . $exec_hosts["Totals"]["medium"] . "&low=" . $exec_hosts["Totals"]["low"];?>"></img></td>
	  </tr>
	  <tr>
	    <td>
		<table>
		  <tr><td class="left" align="center">Level</td><td class="left" align="center">Count</td><td class="left" align="center">%</td></tr>
		  <tr><td class="right" align="center">Critical</td><td class="right" align="center"><?php echo $exec_hosts["Totals"]["critical"]; ?></td><td class="right" align="center"><?php $percent = 100 * ($exec_hosts["Totals"]["critical"]/($exec_hosts["Totals"]["critical"]+$exec_hosts["Totals"]["high"]+$exec_hosts["Totals"]["medium"]+$exec_hosts["Totals"]["low"]));$percent = number_format($percent, 0);echo "$percent"; ?></td></tr>
		  <tr><td class="right" align="center">High</td><td class="right" align="center"><?php echo $exec_hosts["Totals"]["high"]; ?></td><td class="right" align="center"><?php $percent = 100 * ($exec_hosts["Totals"]["high"]/($exec_hosts["Totals"]["critical"]+$exec_hosts["Totals"]["high"]+$exec_hosts["Totals"]["medium"]+$exec_hosts["Totals"]["low"]));$percent = number_format($percent, 0);echo "$percent"; ?></td></tr>
		  <tr><td class="right" align="center">Medium</td><td class="right" align="center"><?php echo $exec_hosts["Totals"]["medium"]; ?></td><td class="right" align="center"><?php $percent = 100 * ($exec_hosts["Totals"]["medium"]/($exec_hosts["Totals"]["critical"]+$exec_hosts["Totals"]["high"]+$exec_hosts["Totals"]["medium"]+$exec_hosts["Totals"]["low"]));$percent = number_format($percent, 0);echo "$percent"; ?></td></tr>
		  <tr><td class="right" align="center">Low</td><td class="right" align="center"><?php echo $exec_hosts["Totals"]["low"]; ?></td><td class="right" align="center"><?php $percent = 100 * ($exec_hosts["Totals"]["low"]/($exec_hosts["Totals"]["critical"]+$exec_hosts["Totals"]["high"]+$exec_hosts["Totals"]["medium"]+$exec_hosts["Totals"]["low"]));$percent = number_format($percent, 0);echo "$percent"; ?></td></tr>
		</table>
	    </td>
	  </tr>
	</table>
	</td><td width="25" class="right"></td>
	<td class="right" valign="top">
	<table width="500">
	<tr><td colspan="6" align="center" class="top"><p>Top Ten Vulnerable Host (By Severity)</p></td></tr>
	<tr>
	  <td class="line1">Host</td>
	  <td class="line1">Critical</td>
	  <td class="line1">High</td>
	  <td class="line1">Medium</td>
	  <td class="line1">Low</td>
	  <td class="line1">Chart</td>
	</tr>
<?php
	
	unset($exec_hosts['Totals']);
	$linecount=0;
	$count=0;
	foreach ($exec_hosts as $key1 => $value1){
		$tdClass = ($linecount%2) ? "line1":"line2";
		echo "<tr>";
			echo "<td class=\"$tdClass\">$key1</td>";
			echo "<td class=\"$tdClass\">" . $value1["critical"] . "</td>";
			echo "<td class=\"$tdClass\">" . $value1["high"] . "</td>";
			echo "<td class=\"$tdClass\">" . $value1["medium"] . "</td>";
			echo "<td class=\"$tdClass\">" . $value1["low"] . "</td>";
			echo "<td align=\"center\" class=\"$tdClass\">";
				echo "<a href=\"images/host_vuln_chart.php?title=$key1%20Vulnerabilities&critical=" . $value1["critical"] . "&high=" . $value1["high"] . "&medium=". $value1["medium"] . "&low=". $value1["low"] . "\" target=\"_blank\">";
				echo "<img src=\"../main/pie_chart_icon.png\" border=\"0\"></img>";
				echo "</a>";
			echo "</td>";
		echo "</tr>\n";
		$linecount++;
		$count++;
		if($count == 10){
			break;
		}
	}
	
?>	
	</table>
	</td>
  </tr>
</table>
</br></br></br></br>
<table>
	<tr><td align="center" colspan="6" width="850"><img src="images/os_chart.php?scan_start=<?php echo "$scan_start";?>&scan_end=<?php echo "$scan_end";?>&agency=<?php echo "$agency";?>&report_name=<?php echo "$report_name";?>&byVuln=<?php echo "$byVuln";?>"></img></td></tr>
	<tr>
		<td class="top"><p>Operating System</p></td>
		<td class="top" width="90" align="center"><p>Critical</p></td>
		<td class="top" width="90" align="center"><p>High</p></td>
		<td class="top" width="90" align="center"><p>Medium</p></td>
		<td class="top" width="90" align="center"><p>Low</p></td>
	</tr>
<?php
	$linecount=0;
	foreach ($exec_os as $key1 => $value1){
		$tdClass = ($linecount%2) ? "line1":"line2";
		echo "<tr>";
			echo "<td class=\"$tdClass\">$key1</td>";
			echo "<td class=\"$tdClass\">" . $value1["critical"] . "</td>";
			echo "<td class=\"$tdClass\">" . $value1["high"] . "</td>";
			echo "<td class=\"$tdClass\">" . $value1["medium"] . "</td>";
			echo "<td class=\"$tdClass\">" . $value1["low"] . "</td>";
		echo "</tr>\n";
		$linecount++;
	}
?>
</table>
<p style="page-break-before: always"></br></p>
<table width="850">
	<tr><td align="center" colspan="5" width="850"><img src="images/family_chart.php?scan_start=<?php echo "$scan_start";?>&scan_end=<?php echo "$scan_end";?>&agency=<?php echo "$agency";?>&report_name=<?php echo "$report_name";?>&byVuln=<?php echo "$byVuln";?>"></img></td></tr>
	<tr>
		<td class="top"><p>Nessus Plugin Family</p></td>
		<td class="top" width="90" align="center"><p>Critical</p></td>
		<td class="top" width="90" align="center"><p>High</p></td>
		<td class="top" width="90" align="center"><p>Medium</p></td>
		<td class="top" width="90" align="center"><p>Low</p></td>
	</tr>
<?php
	$linecount=0;
	foreach ($exec_fam as $key1 => $value1){
		$tdClass = ($linecount%2) ? "line1":"line2";
		echo "<tr>";
			echo "<td class=\"$tdClass\">$key1</td>";
			echo "<td class=\"$tdClass\">" . $value1["critical"] . "</td>";
			echo "<td class=\"$tdClass\">" . $value1["high"] . "</td>";
			echo "<td class=\"$tdClass\">" . $value1["medium"] . "</td>";
			echo "<td class=\"$tdClass\">" . $value1["low"] . "</td>";
		echo "</tr>\n";
		$linecount++;
	}
?>
</table>
<?php
/*------------------------------------------------------
  PHP MySQL query for most vulnerabilities found
--------------------------------------------------------*/
?>
</br>
</br>
</br>
<table width="850">
	<tr>
		<td class="top"><p>Nessus Plugin Name</p></td>
		<td class="top" width="150" align="center"><p>Family</p></td>
		<td class="top" width="75" align="center"><p>Severity</p></td>
		<td class="top" width="50" align="center"><p>Count</p></td>
	</tr>
<?php

$sql = "SELECT
	nessus_results.pluginName,
	nessus_results.pluginFamily,
	nessus_results.severity,
	Count(nessus_results.pluginName) AS pluginCount
FROM
	nessus_results
INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
WHERE
	(nessus_results.severity = '3' OR nessus_results.severity = '2') AND
	pluginName != '' AND
	nessus_results.agency = ? AND 
	nessus_results.report_name = ? AND
	nessus_results.scan_start = ? AND
	nessus_results.scan_end = ?
GROUP BY nessus_results.pluginName
ORDER BY pluginCount DESC
LIMIT 0,10";
$stmt = $db->prepare($sql);
$stmt->execute($whereArray);
$linecount=0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	$pluginName = $row["pluginName"];
	$pluginFamily = $row["pluginFamily"];
		switch ($row["severity"]) {
			case "4":
				$severity = "Critical";
				break;
			case "3":
				$severity = "High";
				break;
			case "2":
				$severity = "Medium";
				break;
			case "1":
				$severity = "Low";
				break;
		}
	$pluginCount = $row["pluginCount"];
	$tdClass = ($linecount%2) ? "line1":"line2";
	echo "<tr>";
		echo "<td class=\"$tdClass\">" . $pluginName . "</td>";
		echo "<td class=\"$tdClass\">" . $pluginFamily . "</td>";
		echo "<td class=\"$tdClass\">" . $severity . "</td>";
		echo "<td class=\"$tdClass\">" . $pluginCount . "</td>";
	echo "</tr>\n";
	$linecount++;
}

?>
</table>
</body>
</html>

<?php

function sortByHigh($a, $b) { 
	return strnatcmp($b['critical'], $a['critical']); 
} // sort alphabetically by name 


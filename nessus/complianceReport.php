<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );ifError($db);
$agency = $_POST["agency"];
$report_name = $_POST["report_name"];
$scan_start = $_POST["scan_start"];
$scan_end = $_POST["scan_end"];

$hostPost = $_POST["host"];
foreach($hostPost as $key => $value) {
	if ($value == "REMOVE") unset($hostPost[$key]);
}
$sql = "CREATE temporary TABLE nessus_temp_hosts (host_name VARCHAR(255))";
$result = $db->query($sql);ifError($result);
foreach ($hostPost as $hP){
	$sql="INSERT INTO nessus_temp_hosts (host_name) VALUES ('$hP')";
	$result = $db->query($sql);ifError($result);	
}
$itemTypePost = $_POST["itemType"];
$sql = "CREATE temporary TABLE nessus_temp_itemType (custom_item_type VARCHAR(255))";
$result = $db->query($sql);ifError($result);
foreach ($itemTypePost as $iTP){
	$sql="INSERT INTO nessus_temp_itemType (custom_item_type) VALUES ('$iTP')";
	$result = $db->query($sql);ifError($result);	
}


$diff_seconds = $scan_end - $scan_start;
$diff_hours = floor($diff_seconds/3600);
$diff_seconds -= $diff_hours * 3600;
$diff_minutes = floor($diff_seconds/60);
$diff_seconds -= $diff_minutes * 60;


/*------------------------------------------------------------------
    PHP Code for gathering data for Host Vulnerability Statistics 
--------------------------------------------------------------------*/
$exec_hosts["Totals"] = array(failed => "0", error => "0", passed => "0", info => "0");
foreach ($hostPost as $hP){
	$exec_hosts[$hP] = array(failed => "0", error => "0", passed => "0", info => "0");
}

$sql = 	"SELECT
			nessus_compliance_results.host_name,
			nessus_compliance_results.severity
		FROM
			nessus_compliance_results
		INNER JOIN nessus_temp_hosts ON nessus_temp_hosts.host_name = nessus_compliance_results.host_name
		INNER JOIN nessus_audit_file ON nessus_compliance_results.description = nessus_audit_file.description
		INNER JOIN nessus_temp_itemType ON nessus_audit_file.custom_item_type = nessus_temp_itemType.custom_item_type
		WHERE
			nessus_compliance_results.agency = '$agency' AND
			nessus_compliance_results.report_name = '$report_name' AND
			nessus_compliance_results.scan_start = '$scan_start' AND
			nessus_compliance_results.scan_end = '$scan_end'
		";
$result = $db->query($sql);ifError($result);
$total_rows = $result->numRows();

while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){
	$severity = $row["severity"];
	$host_name = $row["host_name"];
	switch ($severity) {
		case "3":
			$exec_hosts[$host_name]["failed"]++;
			$exec_hosts["Totals"]["failed"]++;
			break;
		case "2":
			$exec_hosts[$host_name]["error"]++;
			$exec_hosts["Totals"]["error"]++;
			break;
		case "1":
			$exec_hosts[$host_name]["passed"]++;
			$exec_hosts["Totals"]["passed"]++;
			break;
	}
	uasort($exec_hosts, 'sortByFailed');
}

/*---------------------------------------------------------------
    PHP Code for gathering data for Operating System Statistics 
-----------------------------------------------------------------*/

$os_sql = 	"SELECT DISTINCT
				nessus_compliance_results.operating_system
			FROM
				nessus_compliance_results
			INNER JOIN nessus_temp_hosts ON nessus_temp_hosts.host_name = nessus_compliance_results.host_name
			INNER JOIN nessus_audit_file ON nessus_compliance_results.description = nessus_audit_file.description
			INNER JOIN nessus_temp_itemType ON nessus_audit_file.custom_item_type = nessus_temp_itemType.custom_item_type
			WHERE
				nessus_compliance_results.agency = '$agency' AND
				nessus_compliance_results.report_name = '$report_name' AND
				nessus_compliance_results.scan_start = '$scan_start' AND
				nessus_compliance_results.scan_end = '$scan_end'
			";
$os_result = $db->query($os_sql);ifError($os_result);
while ($os_row = $os_result->fetchRow(DB_FETCHMODE_ASSOC)){
	$operating_system = $os_row["operating_system"];
	$exec_os[$operating_system] = array(failed => "0", error => "0", passed => "0", info => "0");
}
$sql = 	"SELECT
			nessus_compliance_results.operating_system,
			nessus_compliance_results.severity
		FROM
			nessus_compliance_results
		INNER JOIN nessus_temp_hosts ON nessus_temp_hosts.host_name = nessus_compliance_results.host_name
		INNER JOIN nessus_audit_file ON nessus_compliance_results.description = nessus_audit_file.description
		INNER JOIN nessus_temp_itemType ON nessus_audit_file.custom_item_type = nessus_temp_itemType.custom_item_type
		WHERE
			nessus_compliance_results.agency = '$agency' AND
			nessus_compliance_results.report_name = '$report_name' AND
			nessus_compliance_results.scan_start = '$scan_start' AND
			nessus_compliance_results.scan_end = '$scan_end'
		";
$result = $db->query($sql);ifError($result);
while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){
	$severity = $row["severity"];
	$operating_system = $row["operating_system"];
	switch ($severity) {
		case "3":
			$exec_os[$operating_system]["failed"]++;
			break;
		case "2":
			$exec_os[$operating_system]["error"]++;
			break;
		case "1":
			$exec_os[$operating_system]["passed"]++;
			break;
		case "0":
			$exec_os[$operating_system]["info"]++;
			break;
	}
}
uasort($exec_os, 'sortByFailed');
/*---------------------------------------------------------------
    PHP Code for gathering data for Compliance Item Type 
-----------------------------------------------------------------*/
$comp_sql = "SELECT
				nessus_audit_file.custom_item_type,
				Count(nessus_compliance_results.severity) AS sevCount
			FROM
				nessus_compliance_results
			INNER JOIN nessus_temp_hosts ON nessus_temp_hosts.host_name = nessus_compliance_results.host_name
			INNER JOIN nessus_audit_file ON nessus_compliance_results.description = nessus_audit_file.description
			INNER JOIN nessus_temp_itemType ON nessus_audit_file.custom_item_type = nessus_temp_itemType.custom_item_type
			WHERE
				nessus_compliance_results.agency = '$agency' AND
				nessus_compliance_results.report_name = '$report_name' AND
				nessus_compliance_results.scan_start = '$scan_start' AND
				nessus_compliance_results.scan_end = '$scan_end' AND
				nessus_compliance_results.pluginID != '0' AND 	
				nessus_compliance_results.severity != '0'
			GROUP BY
				nessus_audit_file.custom_item_type
			ORDER BY
				sevCount DESC
			LIMIT 0, 3
			";
$comp_result = $db->query($comp_sql);ifError($comp_result);
while ($comp_row = $comp_result->fetchRow(DB_FETCHMODE_ASSOC)){
	$itemType = $comp_row["custom_item_type"];
	$exec_itemType[$itemType] = array(failed => "0", error => "0", passed => "0");
	
	$sql = "SELECT
		nessus_audit_file.custom_item_type,
		nessus_compliance_results.severity
		FROM
			nessus_compliance_results
		INNER JOIN nessus_temp_hosts ON nessus_temp_hosts.host_name = nessus_compliance_results.host_name
		INNER JOIN nessus_audit_file ON nessus_compliance_results.description = nessus_audit_file.description
		INNER JOIN nessus_temp_itemType ON nessus_audit_file.custom_item_type = nessus_temp_itemType.custom_item_type
		WHERE
			nessus_compliance_results.agency = '$agency' AND
			nessus_compliance_results.report_name = '$report_name' AND
			nessus_compliance_results.scan_start = '$scan_start' AND
			nessus_compliance_results.scan_end = '$scan_end' AND
			nessus_compliance_results.pluginID != '0' AND 	
			nessus_compliance_results.severity != '0' AND
			nessus_audit_file.custom_item_type = '$itemType'
	";
	$result = $db->query($sql);ifError($result);
	while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$severity = $row["severity"];
		$itemType = $row["custom_item_type"];	
		switch ($severity) {
			case "3":
				$exec_itemType[$itemType]["failed"]++;
				break;
			case "2":
				$exec_itemType[$itemType]["error"]++;
				break;
			case "1":
				$exec_itemType[$itemType]["passed"]++;
				break;
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
if($cover == "y"){
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
				<p>The folpasseding report contains confidential information. Do not
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
	<tr><td class="left">Nessus Compliance Types</td></tr>
	<tr><td class="right">
	<?php  
		$printComplianceType = "";
		foreach($itemTypePost as $cTP){ 
				$printComplianceType .= "$cTP, ";
		}
		$printComplianceType = substr($printComplianceType,0,-2);
		echo "$printComplianceType";	
	?>
	</td></tr>
</table>
<table width="850px">
	<tr><td class="left">Selected Hosts</td></tr>
	<tr><td class="right">
	<?php  
		$printHost = "";
		foreach($hostPost as $hP){ 
			$printHost .= "| $hP ";
		}
		$printHost .= "|";
		echo "$printHost";	
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
        <td class="top" align="center" valign="top"><img src="<?php echo "images/compliance_chart.php?failed=" . $exec_hosts["Totals"]["failed"] . "&error=" . $exec_hosts["Totals"]["error"] . "&passed=" . $exec_hosts["Totals"]["passed"];?>"></img></td>
	  </tr>
	  <tr>
	    <td>
		<table>
		  <tr><td class="left" align="center">Level</td><td class="left" align="center">Count</td><td class="left" align="center">%</td></tr>
		  <tr><td class="right" align="center">Failed</td><td class="right" align="center"><?php echo $exec_hosts["Totals"]["failed"]; ?></td><td class="right" align="center"><?php $percent = 100 * ($exec_hosts["Totals"]["failed"]/($exec_hosts["Totals"]["failed"]+$exec_hosts["Totals"]["error"]+$exec_hosts["Totals"]["passed"]+$exec_hosts["Totals"]["info"]));$percent = number_format($percent, 0);echo "$percent"; ?></td></tr>
		  <tr><td class="right" align="center">Error</td><td class="right" align="center"><?php echo $exec_hosts["Totals"]["error"]; ?></td><td class="right" align="center"><?php $percent = 100 * ($exec_hosts["Totals"]["error"]/($exec_hosts["Totals"]["failed"]+$exec_hosts["Totals"]["error"]+$exec_hosts["Totals"]["passed"]+$exec_hosts["Totals"]["info"]));$percent = number_format($percent, 0);echo "$percent"; ?></td></tr>
		  <tr><td class="right" align="center">Passed</td><td class="right" align="center"><?php echo $exec_hosts["Totals"]["passed"]; ?></td><td class="right" align="center"><?php $percent = 100 * ($exec_hosts["Totals"]["passed"]/($exec_hosts["Totals"]["failed"]+$exec_hosts["Totals"]["error"]+$exec_hosts["Totals"]["passed"]+$exec_hosts["Totals"]["info"]));$percent = number_format($percent, 0);echo "$percent"; ?></td></tr>
		</table>
	    </td>
	  </tr>
	</table>
	</td><td width="25" class="right"></td>
	<td class="right" valign="top">
	<table width="500">
	<tr><td colspan="6" align="center" class="top"><p>Top Ten Vulnerable Host (By Fails)</p></td></tr>
	<tr>
	  <td class="line1">Host</td>
	  <td class="line1">Failed</td>
	  <td class="line1">Error</td>
	  <td class="line1">Passed</td>
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
			echo "<td class=\"$tdClass\">" . $value1["failed"] . "</td>";
			echo "<td class=\"$tdClass\">" . $value1["error"] . "</td>";
			echo "<td class=\"$tdClass\">" . $value1["passed"] . "</td>";
			echo "<td align=\"center\" class=\"$tdClass\">";
				echo "<a href=\"images/host_compliance_chart.php?title=$key1%20Vulnerabilities&failed=" . $value1["failed"] . "&error=". $value1["error"] . "&passed=". $value1["passed"] . "&info=" . $value1["info"] . "\" target=\"_blank\">";
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
<?php


$sql = "SELECT DISTINCT
			nessus_compliance_results.description,
			nessus_compliance_results.severity,
			nessus_compliance_results.remoteValue,
			nessus_compliance_results.policyValue,
			nessus_audit_file.custom_item_type
		FROM
			nessus_compliance_results
		INNER JOIN nessus_temp_hosts ON nessus_temp_hosts.host_name = nessus_compliance_results.host_name
		INNER JOIN nessus_audit_file ON nessus_compliance_results.description = nessus_audit_file.description
		INNER JOIN nessus_temp_itemType ON nessus_audit_file.custom_item_type = nessus_temp_itemType.custom_item_type
		WHERE
			nessus_compliance_results.agency = '$agency' AND
			nessus_compliance_results.report_name = '$report_name' AND
			nessus_compliance_results.scan_start = '$scan_start' AND
			nessus_compliance_results.scan_end = '$scan_end' AND
			nessus_compliance_results.severity = '3'
	";
$result = $db->query($sql);ifError($result);

while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){
	$description = $row["description"];
	$severity = $row["severity"];
	$remoteValue = $row["remoteValue"];
	$policyValue = $row["policyValue"];
	$itemType = $row["custom_item_type"];
	echo "<br></br>";
	echo "<table width=\"850px\">";
	echo "<tr><td colspan=\"2\" class=\"left\"><pre>". $description ."</pre></td></tr>";
	echo "<tr><td class=\"left\"><p>Policy Value</p></td><td class=\"left\"><p>Remote Value</p></td></tr>";
	echo "<tr><td class=\"right\" valign=\"top\">". nl2br($policyValue) ."</td><td class=\"right\" valign=\"top\">". nl2br($remoteValue) ."</td></tr>";
	echo "</table>";
	
	echo "<table width=\"850px\">";
	echo "<tr><td class=\"top\"><p>IP Address</p></td><td class=\"top\"><p>MAC</p></td><td class=\"top\"><p>FQDN</p></td><td class=\"top\"><p>NetBIOS</p></td><td class=\"top\"><p>OS</p></td>";
	$host_sql = "SELECT
					nessus_compliance_results.host_name,
					nessus_tags.ip_addr,
					nessus_tags.mac_addr,
					nessus_tags.fqdn,
					nessus_tags.netbios,
					nessus_tags.operating_system
				FROM
					nessus_compliance_results
				INNER JOIN nessus_tags ON nessus_compliance_results.tagID = nessus_tags.tagID
				INNER JOIN nessus_temp_hosts ON nessus_temp_hosts.host_name = nessus_compliance_results.host_name
				INNER JOIN nessus_audit_file ON nessus_compliance_results.description = nessus_audit_file.description
				INNER JOIN nessus_temp_itemType ON nessus_audit_file.custom_item_type = nessus_temp_itemType.custom_item_type
				WHERE
					nessus_compliance_results.agency = '$agency' AND
					nessus_compliance_results.report_name = '$report_name' AND
					nessus_compliance_results.severity = '3' AND
					nessus_audit_file.custom_item_type = '$itemType' AND
					nessus_compliance_results.description = '$description' AND
					nessus_compliance_results.remoteValue = '$remoteValue' AND
					nessus_compliance_results.policyValue = '$policyValue'
				";
	$host_result = $db->query($host_sql);ifError($host_result);
	while($host_row = $host_result->fetchRow(DB_FETCHMODE_ASSOC)){
		$host_name = $host_row["host_name"];
		$ip_addr = $host_row["ip_addr"];
		$mac_addr = $host_row["mac_addr"];
		$fqdn = $host_row["fqdn"];
		$netbios = $host_row["netbios"];
		$operating_system = $host_row["operating_system"];
		?>
		<tr>
		  <td class="right"><p><?php if($ip_addr == ""){ echo "$host_name";} else {echo"$ip_addr";}?></p></td>
		  <td class="right"><p><?php echo "$mac_addr";?></p></td>
		  <td class="right"><p><?php echo"$fqdn ";?></p></td>
		  <td class="right"><p><?php echo "$netbios";?></p></td>
		  <td class="right"><p><?php echo"$operating_system";?></p></td>
		</tr>
		<?php
	}
echo "</table>";

}

?>




</table>
</body>
</html>

<?php

function sortByFailed($a, $b) { 
	return strnatcmp($b['failed'], $a['failed']); 
} // sort alphabetically by name 

function ifError($error)
{
	if (PEAR::isError($error)) {
		echo 'Standard Message: ' . $error->getMessage() . "</br>";
		echo 'Standard Code: ' . $error->getCode() . "</br>";
		echo 'DBMS/User Message: ' . $error->getUserInfo() . "</br>";
		echo 'DBMS/Debug Message: ' . $error->getDebugInfo() . "</br>";
		exit;
	}
}
?>
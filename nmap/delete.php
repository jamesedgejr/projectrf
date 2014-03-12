<?php
//total work in progress...this shit is sooooo broken.
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

$report = $_POST["report"];
if(isset($report)){
	foreach($report as $key =>$value){
		if ($value == "jksdfKAEFJEK") unset($report[$key]);
	}
	foreach($report as $r){
		$temp = explode(":", $r);
		$agency = $temp[0];
		$filename = $temp[1];
		$nmaprun_start = $temp[2];
		$finish_time = $temp[3];
		$nmapid_port_sql = "SELECT
								nmap_runstats_xml.id AS runID,
								nmap_hosts_xml.id AS hostsID,
								nmap_ports_xml.id AS portID
							FROM
								nmap_runstats_xml
							INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
							INNER JOIN nmap_ports_xml ON nmap_ports_xml.host_id = nmap_hosts_xml.id
							WHERE
								nmap_runstats_xml.agency = '$agency' AND
								nmap_runstats_xml.filename = '$filename' AND
								nmap_runstats_xml.nmaprun_start = '$nmaprun_start' AND
								nmap_runstats_xml.finished_time = '$finish_time'
							";
		$nmapid_port_stmt = $db->prepare($nmapid_port_sql);
		$data = array($agency, $filename, $nmaprun_start, $finish_time);
		$nmapid_port_stmt->execute($data);
		$nmapid__port_result =& $db->getAll($nmapid_port_sql, array(), DB_FETCHMODE_ORDERED | DB_FETCHMODE_FLIPPED);
		$nmapid_portNSE_sql = "SELECT
						nmap_nse_xml.id as nsePortID
					FROM
						nmap_runstats_xml
					INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
					INNER JOIN nmap_ports_xml ON nmap_ports_xml.host_id = nmap_hosts_xml.id
					INNER JOIN nmap_nse_xml ON nmap_nse_xml.host_or_port_id = nmap_ports_xml.id
					WHERE
						nmap_runstats_xml.agency = '$agency' AND
						nmap_runstats_xml.filename = '$filename' AND
						nmap_runstats_xml.nmaprun_start = '$nmaprun_start' AND
						nmap_runstats_xml.finished_time = '$finish_time'
					";
		$nmapid__portNSE_result =& $db->getAll($nmapid_portNSE_sql, array(), DB_FETCHMODE_ORDERED | DB_FETCHMODE_FLIPPED);
		$nmapid_hostNSE_sql = "SELECT
								nmap_nse_xml.id AS nseHostID
							FROM
								nmap_runstats_xml
							INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
							INNER JOIN nmap_nse_xml ON nmap_nse_xml.host_or_port_id = nmap_hosts_xml.id
							WHERE
								nmap_runstats_xml.agency = '$agency' AND
								nmap_runstats_xml.filename = '$filename' AND
								nmap_runstats_xml.nmaprun_start = '$nmaprun_start' AND
								nmap_runstats_xml.finished_time = '$finish_time'
							";
		$nmapid__hostNSE_result =& $db->getAll($nmapid_hostNSE_sql, array(), DB_FETCHMODE_ORDERED | DB_FETCHMODE_FLIPPED);
		
		$runstatsArray = array_values(array_unique($nmapid__port_result[0]));
		$hostsArray = array_values(array_unique($nmapid__port_result[1]));
		$portsArray = array_values(array_unique($nmapid__port_result[2]));
		$nseArray = array_merge(array_unique($nmapid__portNSE_result[0]), array_unique($nmapid__hostNSE_result[0]));
		for($x=0;$x<count($runstatsArray);$x++){
			$delete_sql = "DELETE FROM nmap_runstats_xml WHERE nmap_runstats_xml.id = '$runstatsArray[$x]'";
			$delete_result = $db->query($delete_sql);ifError($delete_result);	
		}
		for($x=0;$x<count($hostsArray);$x++){
			$delete_sql = "DELETE FROM nmap_hosts_xml WHERE nmap_hosts_xml.id = '$hostsArray[$x]'";
			$delete_result = $db->query($delete_sql);ifError($delete_result);	
		}
		for($x=0;$x<count($portsArray);$x++){
			$delete_sql = "DELETE FROM nmap_ports_xml WHERE nmap_ports_xml.id = '$portsArray[$x]'";
			$delete_result = $db->query($delete_sql);ifError($delete_result);	
		}
		for($x=0;$x<count($nseArray);$x++){
			$delete_sql = "DELETE FROM nmap_nse_xml WHERE nmap_nse_xml.id = '$nseArray[$x]'";
			$delete_result = $db->query($delete_sql);ifError($delete_result);	
		}
	}
}

$agency_sql = 	"SELECT DISTINCT
					nmap_runstats_xml.agency,
					nmap_runstats_xml.filename,
					nmap_runstats_xml.nmaprun_start,
					nmap_runstats_xml.finished_time
				FROM
					nmap_runstats_xml
				";
$agency_result = $db->query($agency_sql);ifError($plugin_result);

?>

<HTML>
<head>
<title>DELETE NMAP VULNERABILITY REPORTS</title>
<script>
function selectAll(selectBox,selectAll) {
    // have we been passed an ID
    if (typeof selectBox == "string") {
        selectBox = document.getElementById(selectBox);
    }

    // is the select box a multiple select box?
    if (selectBox.type == "select-multiple") {
        for (var i = 0; i < selectBox.options.length; i++) {
            selectBox.options[i].selected = selectAll;
        }
    }
}
</script>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
select {font-family: courier new}
</style>
</head>
<BODY>
<table width="100%"><tr><td width="200px" valign="top"><?php include '../main/menu.php'; ?></td>
<td>
<table style="text-align: left; width: 850px;" border="0" cellpadding="0" cellspacing="0" valign="top">
    <tr>
      <td style="width: 600px;text-align: center;">
	  <form name="f1"  action="" method="post">
	  <p align="center">[ Nmap Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.  Click submit to delete the report.<br>You can select more than one report for deletion.</p>
	  <p><input type="button" name="Button" value="Select All" onclick="selectAll('reportselectall',true)" /></p>
  	  <select MULTIPLE NAME="report[]" SIZE="10"  style="width:600px;margin:5px 0 5px 0;" id="reportselectall">
		<option value="jksdfKAEFJEK" selected>[Agency]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Report Name]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Date/Time]]</option>
			<?php
			while($agency_row = $agency_result->fetchRow(DB_FETCHMODE_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["filename"], 20));
				$formatedDate = date("D M d H:i:s Y", $agency_row["finished_time"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $agency_row["agency"] . ":" . $agency_row["filename"] . ":" . $agency_row["nmaprun_start"] . ":" . $agency_row["finished_time"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select><br>
	  <INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
	  </form>
	  </td>
	</tr>
    <tr>
      <td align="center">
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<?php  include '../main/footer.php'; ?>
      </td>
    </tr>
</table>
</td></tr></table>
</body>
</html>
<?php
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
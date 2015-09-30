<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

$v1 = new Valitron\Validator($_POST);
$v1->rule('slug','new_agency');
$v1->rule('regex',['new_scan_name','new_filename'], '/^([\w _.-])+$/'); //regex includes alpha/numeric, space, underscore, dash, and period
if(!$v1->validate()) {
    print_r($v1->errors());
	exit;
} 

$report = $_POST["report"];
$new_agency = $_POST["new_agency"];
$new_scan_name = $_POST["new_scan_name"];
$new_filename = $_POST["new_filename"];
$new_scan_id = rand(100000000000000,999999999999999);
$agencyArray = $scan_nameArray = $scanStartTimeArray = $scanEndTimeArray = $sortedStartTime = $sortedEndTime = $scan_idArray = array();
if(isset($report) && isset($new_agency) && isset($new_scan_name)){
	foreach($report as $key => $value){
		if ($value == "none") unset($report[$key]);
	}
	foreach($report as $r){
		$temp = explode(":@:", $r);
		$v2 = new Valitron\Validator($temp);
		$v2->rule('slug', '0');//validate agency
		$v2->rule('regex',['1','2'],'/^([\w _.-])+$/'); //regex includes alpha/numeric, space, underscore, dash, and period
		$v2->rule('numeric',['3','4','5']);//validate scan_start and scan_end
		if(!$v2->validate()) {
			print_r($v2->errors());
			exit;
		} 
		$agencyArray[] = $temp[0];
		$filenameArray[] = $temp[1];
		$scan_nameArray[] = $temp[2];
		$scanStartTimeArray[] = $temp[3];
		$scanEndTimeArray[] = $temp[4];
		$scan_idArray[] = $temp[5];
	}
	$sortedStartTime = $scanStartTimeArray;
	$sortedEndTime = $scanEndTimeArray;
	sort($sortedStartTime);
	rsort($sortedEndTime);
	for($x=0;$x<count($agencyArray);$x++){
		$nexpose_scans_sql = "UPDATE nexpose_scans
				SET
					nexpose_scans.agency = ?,
					nexpose_scans.filename = ?,
					nexpose_scans.scan_name = ?,
					nexpose_scans.scan_startTime = ?,
					nexpose_scans.scan_endTime = ?,
					nexpose_scans.scan_id = ?
				WHERE
					nexpose_scans.agency = ? AND
					nexpose_scans.filename = ? AND
					nexpose_scans.scan_name = ? AND
					nexpose_scans.scan_startTime = ? AND
					nexpose_scans.scan_endTime = ? AND
					nexpose_scans.scan_id = ?
				";
		$nexpose_scans_stmt = $db->prepare($nexpose_scans_sql);
		$nexpose_scans_data = array($new_agency, $new_filename, $new_scan_name, $sortedStartTime[0], $sortedEndTime[0], $new_scan_id, $agencyArray[$x], $filenameArray[$x], $scan_nameArray[$x], $scanStartTimeArray[$x], $scanEndTimeArray[$x], $scan_idArray[$x]);
		$nexpose_scans_stmt->execute($nexpose_scans_data);
		
		$nexpose_nodes_sql = "UPDATE nexpose_nodes
				SET
					nexpose_nodes.agency = ?,
					nexpose_nodes.filename = ?
				WHERE
					nexpose_nodes.agency = ? AND
					nexpose_nodes.filename = ?
				";
		$nexpose_nodes_stmt = $db->prepare($nexpose_nodes_sql);
		$nexpose_nodes_data = array($new_agency, $new_filename, $agencyArray[$x], $filenameArray[$x]);
		$nexpose_nodes_stmt->execute($nexpose_nodes_data);

		$nexpose_endpoints_sql = "UPDATE nexpose_endpoints
				SET
					nexpose_endpoints.agency = ?,
					nexpose_endpoints.filename = ?
				WHERE
					nexpose_endpoints.agency = ? AND
					nexpose_endpoints.filename = ?
				";
		$nexpose_endpoints_stmt = $db->prepare($nexpose_endpoints_sql);
		$nexpose_endpoints_data = array($new_agency, $new_filename, $agencyArray[$x], $filenameArray[$x]);
		$nexpose_endpoints_stmt->execute($nexpose_endpoints_data);

		$nexpose_device_fingerprints_sql = "UPDATE nexpose_device_fingerprints
				SET
					nexpose_device_fingerprints.agency = ?,
					nexpose_device_fingerprints.filename = ?
				WHERE
					nexpose_device_fingerprints.agency = ? AND
					nexpose_device_fingerprints.filename = ?
				";
		$nexpose_device_fingerprints_stmt = $db->prepare($nexpose_device_fingerprints_sql);
		$nexpose_device_fingerprints_data = array($new_agency, $new_filename, $agencyArray[$x], $filenameArray[$x]);
		$nexpose_device_fingerprints_stmt->execute($nexpose_device_fingerprints_data);

		$nexpose_endpoint_fingerprints_sql = "UPDATE nexpose_endpoint_fingerprints
				SET
					nexpose_endpoint_fingerprints.agency = ?,
					nexpose_endpoint_fingerprints.filename = ?
				WHERE
					nexpose_endpoint_fingerprints.agency = ? AND
					nexpose_endpoint_fingerprints.filename = ?
				";
		$nexpose_endpoint_fingerprints_stmt = $db->prepare($nexpose_endpoint_fingerprints_sql);
		$nexpose_endpoint_fingerprints_data = array($new_agency, $new_filename, $agencyArray[$x], $filenameArray[$x]);
		$nexpose_endpoint_fingerprints_stmt->execute($nexpose_endpoint_fingerprints_data);

		$nexpose_tests_sql = "UPDATE nexpose_tests
				SET
					nexpose_tests.agency = ?,
					nexpose_tests.filename = ?,
					nexpose_tests.scan_id = ?
				WHERE
					nexpose_tests.agency = ? AND
					nexpose_tests.filename = ? AND
					nexpose_tests.scan_id = ?
				";
		$nexpose_tests_stmt = $db->prepare($nexpose_tests_sql);
		$nexpose_tests_data = array($new_agency, $new_filename, $new_scan_id, $agencyArray[$x], $filenameArray[$x], $scan_idArray[$x]);
		$nexpose_tests_stmt->execute($nexpose_tests_data);
	}
}

$merge_sql = 	"SELECT DISTINCT
					nexpose_scans.agency,
					nexpose_scans.filename,
					nexpose_scans.scan_name,
					nexpose_scans.scan_startTime,
					nexpose_scans.scan_endTime,
					nexpose_scans.scan_id
				FROM
					nexpose_scans
				";
$merge_stmt = $db->prepare($merge_sql);
$merge_stmt->execute();
?>

<HTML>
<head>
<title>MERGE NEXPOSE REPORTS</title>
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
<table style="text-align: left; width: 950px;" border="0" cellpadding="0" cellspacing="0" valign="top">
    <tr>
      <td style="width: 950px;text-align: center;">
	  <form action="merge.php" method="post">
	  <p align="center">[ Merge Nexpose Reports ]</p>
	  <p align="center">From the list select Agency/Report name that you wish to merge/combine.  
	  <br>The combined results will take the earliest start time and the latest end time.
	  <br>Enter the Agency/Company/Scan Title you want to call the merged data.</p>
	  <p><input type="button" name="Button" value="Select All" onclick="selectAll('reportselectall',true)" /></p>
  	  <select MULTIPLE NAME="report[]" SIZE="10"  style="width:900px;margin:5px 0 5px 0;" id="reportselectall">
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Scan Name]",70)).str_replace(' ','&nbsp;',str_pad("[Date]",20))."</option>";
			while($merge_row = $merge_stmt->fetch(PDO::FETCH_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($merge_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($merge_row["scan_name"], 70));
				//$formatedDate = date("D M d H:i:s Y", $merge_row["scan_endTime"]);
				//$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				$value3 = str_replace(' ','&nbsp;',str_pad($merge_row["scan_endTime"], 20));
				echo "<option value='" . $merge_row["agency"] . ":@:" . $merge_row["filename"] . ":@:" . $merge_row["scan_name"] . ":@:" . $merge_row["scan_startTime"] . ":@:" . $merge_row["scan_endTime"] . ":@:". $merge_row["scan_id"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select><br>
	  <p>Enter New Name for Agengy/Company:<br><input name="new_agency" type="text" /></p>
	  <p>Enter New Scan Name for the Report:<br><input name="new_scan_name" type="text" /></p>
	  <p>Enter New Filename for the Report:<br><input name="new_filename" type="text" /></p>
	  <br>
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
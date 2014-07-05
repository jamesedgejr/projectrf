<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

$report = $_POST["report"];
$newAgencyName = $_POST["newAgencyName"];
$newReportName = $_POST["newReportName"];
$agencyArray = $reportNameArray = $scanStartArray = $scanEndArray = $sortedStart = $sortedEnd = array();
if(isset($report) && isset($newAgencyName) && isset($newReportName)){
	foreach($report as $key => $value){
		if ($value == "none") unset($report[$key]);
	}
	foreach($report as $r){
		$temp = explode(":", $r);
		$agencyArray[] = $temp[0];
		$reportNameArray[] = $temp[1];
		$scanStartArray[] = $temp[2];
		$scanEndArray[] = $temp[3];
	}
	$sortedStart = $scanStartArray;
	$sortedEnd = $scanEndArray;
	sort($sortedStart);
	rsort($sortedEnd);
	for($x=0;$x<count($agencyArray);$x++){
		$sql = "UPDATE nessus_results
				SET
					nessus_results.agency = ?,
					nessus_results.report_name = ?,
					nessus_results.scan_start = ?,
					nessus_results.scan_end = ?
				WHERE
					nessus_results.agency = ? AND
					nessus_results.report_name = ? AND
					nessus_results.scan_start = ? AND
					nessus_results.scan_end = ?
				";
		$stmt = $db->prepare($sql);
		$stmt->execute(array($newAgencyName, $newReportName, $sortedStart[0], $sortedEnd[0], $agencyArray[$x], $reportNameArray[$x], $scanStartArray[$x], $scanEndArray[$x]));
	}
}

$merge_sql = 	"SELECT DISTINCT 
					nessus_results.agency, 
					nessus_results.report_name, 
					nessus_results.scan_start, 
					nessus_results.scan_end 
				FROM 
					nessus_results
				";
$merge_stmt = $db->prepare($merge_sql);
$merge_stmt->execute();
?>

<HTML>
<head>
<title>MERGE NESSUS REPORTS</title>
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
	  <p align="center">[ Merge Nessus Reports ]</p>
	  <p align="center">From the list select Agency/Report name that you wish to merge/combine.  
	  <br>The combined results will take the earliest start time and the latest end time.
	  <br>Enter the Agency/Company/Report Title you want to call the merged data.</p>
	  <p><input type="button" name="Button" value="Select All" onclick="selectAll('reportselectall',true)" /></p>
  	  <select MULTIPLE NAME="report[]" SIZE="10"  style="width:900px;margin:5px 0 5px 0;" id="reportselectall">
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Report Name]",70)).str_replace(' ','&nbsp;',str_pad("[Date]",20))."</option>";
			while($merge_row = $merge_stmt->fetch(PDO::FETCH_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($merge_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($merge_row["report_name"], 70));
				$formatedDate = date("D M d H:i:s Y", $merge_row["scan_end"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $merge_row["agency"] . ":" . $merge_row["report_name"] . ":" . $merge_row["scan_start"] . ":" . $merge_row["scan_end"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select><br>
	  <p>Enter New Name for Agengy/Company:<br><input name="newAgencyName" type="text" /></p>
	  <p>Enter New Name for the Report:<br><input name="newReportName" type="text" /></p>
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
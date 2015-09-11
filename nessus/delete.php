<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);


$report = $_POST["report"];
if(isset($report)){
	foreach($report as $key =>$value){
		if ($value == "none") unset($report[$key]);
	}
	foreach($report as $r){
		$temp = explode(":", $r);
		$v = new Valitron\Validator($temp);
		$v->rule('slug', '0');//validate agency
		$v->rule('regex','1','/^([\w _.-])+$/'); //regex includes alpha/numeric, space, underscore, dash, and period
		$v->rule('numeric',['2','3']);//validate scan_start and scan_end
		if($v->validate()) {

		} else {
			print_r($v->errors());
			exit;
		} 
		$agency = $temp[0];
		$report_name = $temp[1];
		$scan_start = $temp[2];
		$scan_end = $temp[3];

		$tagid_sql = "SELECT DISTINCT tagID FROM nessus_results WHERE nessus_results.agency = ? AND nessus_results.report_name = ? AND nessus_results.scan_start = ? AND nessus_results.scan_end = ?";
		$tagid_data = array($agency, $report_name, $scan_start, $scan_end);
		$tagid_stmt = $db->prepare($tagid_sql);
		$tagid_stmt->execute($tagid_data);
		while($tagid_row = $tagid_stmt->fetch(PDO::FETCH_ASSOC)){
			$tagID = $tagid_row["tagID"];
			$delete_tagID_sql = "DELETE FROM nessus_tags WHERE nessus_tags.tagID = ?";
			$delete_tagID_data = array($tagID);
			$delete_tagID_stmt = $db->prepare($delete_tagID_sql);
			$delete_tagID_stmt->execute($delete_tagID_data);
		}
		
		$delete_results_sql = "DELETE FROM nessus_results
						WHERE
							nessus_results.agency = '$agency' AND 
							nessus_results.report_name = '$report_name' AND
							nessus_results.scan_start = '$scan_start' AND
							nessus_results.scan_end = '$scan_end'
						";
		$delete_results_data = array($agency, $report_name, $scan_start, $scan_end);
		$delete_results_stmt = $db->prepare($delete_results_sql);
		$delete_results_stmt->execute($delete_results_data);
	
	}
}

$agency_sql = 	"SELECT DISTINCT 
					nessus_results.agency, 
					nessus_results.report_name, 
					nessus_results.scan_start, 
					nessus_results.scan_end 
				FROM 
					nessus_results
				";

$agency_stmt = $db->prepare($agency_sql);
$agency_stmt->execute();

?>

<HTML>
<head>
<title>DELETE NESSUS VULNERABILITY REPORT</title>
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
	  <form action="delete.php" method="post">
	  <p align="center">[ Delete Nessus Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.  Click submit to delete the report.<br>You can select more than one report for deletion.</p>
	  <p><input type="button" name="Button" value="Select All" onclick="selectAll('reportselectall',true)" /></p>
  	  <select MULTIPLE NAME="report[]" SIZE="10"  style="width:600px;margin:5px 0 5px 0;" id="reportselectall">
		<option value="none" selected>[Agency]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Report Name]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Date/Time]</option>
			<?php
			while($agency_row = $agency_stmt->fetch(PDO::FETCH_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["report_name"], 20));
				$formatedDate = date("D M d H:i:s Y", $agency_row["scan_end"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $agency_row["agency"] . ":" . $agency_row["report_name"] . ":" . $agency_row["scan_start"] . ":" . $agency_row["scan_end"] . "'>" . $value1 . $value2 . $value3 . "</option>";
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

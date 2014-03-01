<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );


$report = $_POST["report"];
if(isset($report)){
	foreach($report as $key =>$value){
		if ($value == "none") unset($report[$key]);
	}
	foreach($report as $r){
		$temp = explode(":", $r);
		$agency = $temp[0];
		$report_name = $temp[1];
		$scan_start = $temp[2];
		$scan_end = $temp[3];

		$tagid_sql = "SELECT DISTINCT tagID FROM nessus_results WHERE nessus_results.agency = '$agency' AND nessus_results.report_name = '$report_name' AND nessus_results.scan_start = '$scan_start' AND nessus_results.scan_end = '$scan_end'";
		$tagid_result = $db->query($tagid_sql);ifError($tagid_result);
		while($tagid_row = $tagid_result->fetchRow(DB_FETCHMODE_ASSOC)){
			$tagID = $tagid_row["tagID"];
			$delete_sql = "DELETE FROM nessus_tags WHERE nessus_tags.tagID = '$tagID'";
			$delete_result = $db->query($delete_sql);ifError($delete_result);
		}
		
		$delete_sql = "DELETE FROM nessus_results
						WHERE
							nessus_results.agency = '$agency' AND 
							nessus_results.report_name = '$report_name' AND
							nessus_results.scan_start = '$scan_start' AND
							nessus_results.scan_end = '$scan_end'
						";
		$delete_result = $db->query($delete_sql);ifError($delete_result);
	
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
$agency_result = $db->query($agency_sql);ifError($plugin_result);

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
			while($agency_row = $agency_result->fetchRow(DB_FETCHMODE_ASSOC)){
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
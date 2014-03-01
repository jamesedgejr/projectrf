<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
$agency_sql = 	"SELECT DISTINCT
					nmap_runstats_xml.agency,
					nmap_runstats_xml.filename,
					nmap_runstats_xml.nmaprun_start,
					nmap_runstats_xml.finished_time
				FROM
					nmap_runstats_xml
				";
$agency_result = $db->query($agency_sql);ifError($agency_result);
?>

<HTML>
<head>
<title>CREATE JP CSV</title>
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
<td style="text-align: right; width: 850px;" valign="top">
<table style="text-align: left; width: 650px;" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>
	  <form name="f1"  action="jp_csv.php" method="post">
	  <p align="center">[ JP CSV ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.  <br>If you are not JP than you will not find this useful.</p>
  	  <p align="center"><select NAME="agency" SIZE="10"  style="width:600px;margin:5px 0 5px 0;" >
		<option value="" selected>[Agency]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Report Name]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Date/Time]]</option>
			<?php
			while($agency_row = $agency_result->fetchRow(DB_FETCHMODE_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["filename"], 20));
				$formatedDate = date("D M d H:i:s Y", $agency_row["finished_time"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $agency_row["agency"] . ":" . $agency_row["filename"] . ":" . $agency_row["nmaprun_start"] . ":" . $agency_row["finished_time"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select>
	  <INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
	  </form>
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
	  <p>&nbsp;</p>
	  <p>&nbsp;</p>
	  <?php  include '../main/footer.php'; ?>
</td></tr></table>
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
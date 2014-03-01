<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
$agency_sql = 	"SELECT DISTINCT 
			dumpsec_user_table.agency, 
			dumpsec_user_table.Host, 
			dumpsec_user_table.FileDate, 
			dumpsec_user_table.FileName 
		FROM 
			dumpsec_user_table
		";
$agency_result = $db->query($agency_sql);ifError($plugin_result);
?>

<HTML>
<head>
<title>CREATE DUMPSEC REPORT</title>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
select {font-family: courier new}
</style>
</head>
<BODY>
<table width="100%">
	<tr>
		<td width="200px" valign="top"><?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">
	<table style="text-align: left; width: 650px;" border="0" cellpadding="0" cellspacing="0">
     <tr>
      <td valign="top">
	  <form name="f1"  action="report.php" method="post">
	  <p align="center">[ DumpSec Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.</p>
  	  <select NAME="option" SIZE="10"  style="width:800px;margin:5px 0 5px 0;">
		<option value="none" selected>[Agency]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Host]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Date/Time]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[File Name]</option>
			<?php
			while($agency_row = $agency_result->fetchRow(DB_FETCHMODE_ASSOC)){
			  $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 15));
			  $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["Host"], 15));
			  $value3 = str_replace(' ','&nbsp;',str_pad($agency_row["FileDate"], 20));
			  $value4 = str_replace(' ','&nbsp;',str_pad($agency_row["FileName"], 20));
			  echo "<option value='" . $agency_row["agency"] . "%%" . $agency_row["Host"] . "%%" . $agency_row["FileDate"] . "%%" . $agency_row["FileName"] . "'>" . $value1 . $value2 . $value3 . $value4 . "</option>";
			}
			?>
	  </select><INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT"></form>
	  </td>
	 </tr>
	</table>
</td>
</tr>
<tr>
<td colspan="2">
	<?php include '../main/footer.php'; ?>
</td>
</tr>
</table>
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

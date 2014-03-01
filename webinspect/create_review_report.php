<HTML>
<head>
<title>WebInspect Agency Review Report</title>
<style type="text/css">
p {font-size: 70%}
a {text-decoration: none}
a:hover {text-decoration: underline}
</style>
</head>
<BODY>
<table width="100%"><tr>
	<td width="20%" valign="top">
	<?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">


<?PHP
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
$agency_sql = 	"SELECT DISTINCT agency FROM webinspect_xml";
$agency_result = $db->query($agency_sql);
?>

<TABLE align="center" width="600">
  <tr>
	<td valign="top" width="200">
		<FORM action="review_report.php" method="post">
		<SELECT NAME="agency" style="width:150px;margin:5px 0 5px 0;">
		<option value="none" selected>[choose agency]</option>
		<?php
			while($agency_row = $agency_result->fetchRow()){
				echo "<option value='$agency_row[0]'>$agency_row[0]</option>";
			}
		?>
		</SELECT>
  <TR>
	<TD>
	<INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
	</TD>
  </TR>
</TABLE>
</FORM>

</td></tr></table>
</body>
</html>

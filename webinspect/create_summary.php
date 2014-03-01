<HTML>
<head>
<title>Dynamic List Boxes in PHP</title>
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
$agency = $_POST["agency"];

	$agency_sql = 	"SELECT DISTINCT agency	FROM webinspect_xml";

if($agency != ""){
	$host_sql = 	"SELECT DISTINCT host,application FROM webinspect_xml WHERE webinspect_xml.agency='$agency'	ORDER BY host";
	$host_result = $db->query($host_sql);
}//end if
$agency_result = $db->query($agency_sql);
?>

<TABLE align="center" width="600">
<tr><td valign="top" width="200">
	<table width="100%">
	  <TR>
	    <TD>
		<FORM name="f1"  action="" method="post">
		<SELECT NAME="agency" SIZE="5"  style="width:150px;margin:5px 0 5px 0;" ONCHANGE="f1.submit()" >
		<option value="none" selected>[choose agency]</option>
		<?php
			while($agency_row = $agency_result->fetchRow()){
				echo "<option value='$agency_row[0]'>$agency_row[0]</option>";
			}
		?>
		</SELECT>
	    </TD>
	  </tr>
	  <tr>
	    <TD>
		</form><ForM name="f2" action="summary.php" method="post">
		<?php
		if($agency == ""){
		?>
		<SELECT MULTIPLE NAME="host_application" SIZE="15" style="width:150px;margin:5px 0 5px 0;">
		<OPTION>[no agency selected]</OPTION>
		</SELECT>
		<?php
		}//end if
		else {
		?>
		<SELECT MULTIPLE NAME="host_application[]" SIZE="15" style="width:300px;margin:5px 0 5px 0;">
		<?php
			while($host_row = $host_result->fetchRow()){
				echo "<OPTION value='$host_row[0]:$host_row[1]'>$host_row[0]:$host_row[1]</OPTION>";
			}//end while
		?>
		</SELECT>				
		<?php
		}//end else
		?>
	    </TD>
	  </TR>
	</table>
	</td>
	<td valign="top" width="200">
	<table>
	  <TR>
	    <TD>
		<input type="hidden" name="agency" value="<?php echo "$agency";?>">
		<INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
	    </TD>
	  </TR>
	</table>
	</td>
	</tr>
</TABLE>
</FORM>

</td></tr></table>
</body>
</html>

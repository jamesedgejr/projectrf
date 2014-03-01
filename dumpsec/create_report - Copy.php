<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
$agency_temp = explode("%%", $_POST["agency"]);
$agency = $agency_temp[0];
$Host = $agency_temp[1];
$FileDate = $agency_temp[2];
$FileName = $agency_temp[3];

$agency_sql = 	"SELECT DISTINCT 
			dumpsec_user_table.agency, 
			dumpsec_user_table.Host, 
			dumpsec_user_table.FileDate, 
			dumpsec_user_table.FileName 
		FROM 
			dumpsec_user_table
		";
$agency_result = $db->query($agency_sql);ifError($plugin_result);
if($agency != ""){
	$groups_sql = "SELECT DISTINCT
				dumpsec_user_table.Groups,
				dumpsec_user_table.GroupComment,
				dumpsec_user_table.GroupType
			FROM
				dumpsec_user_table
			WHERE 
				dumpsec_user_table.agency='$agency' AND
				dumpsec_user_table.Host='$Host' AND
				dumpsec_user_table.FileDate='$FileDate' AND
				dumpsec_user_table.FileName='$FileName'
			ORDER BY 
				dumpsec_user_table.GroupType, dumpsec_user_table.Groups
			";
	$groups_result = $db->query($groups_sql);ifError($groups_result);
}//end if

?>

<HTML>
<head>
<title>CREATE DUMPSEC REPORT</title>
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
<td valign="top">
<table style="text-align: left; width: 850px;" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top">
	  <form name="f1"  action="" method="post">
	  <p align="center">[ DumpSec Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.  <br>Then select the groups you want to include.</p>
  	  <select NAME="agency" SIZE="10"  style="width:800px;margin:5px 0 5px 0;" ONCHANGE="f1.submit()" >
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
	  </select>
	  </form>
	<form name="f2" action="report.php" method="post">
		<?php
		//Groups list
		if($agency == ""){
		?>
			<p align="center">[ Groups ]</p>
			<SELECT MULTIPLE NAME="groups" SIZE="25" style="width:800px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Groups ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('groupsselectall',true)" /><br>
			<SELECT MULTIPLE NAME="groups[]" SIZE="20" style="width:800px;margin:5px 0 5px 0;" id="groupsselectall">
			<option value='REMOVE'>[Group Name]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Type]&nbsp;&nbsp;&nbsp;&nbsp;[Group Comment]</option>
		<?php
			while($groups_row = $groups_result->fetchRow(DB_FETCHMODE_ASSOC)){
			  $value1 = str_replace(' ','&nbsp;',str_pad($groups_row["Groups"], 40));
			  $value2 = str_replace(' ','&nbsp;',str_pad($groups_row["GroupType"], 10));
			  $value3 = str_replace(' ','&nbsp;',str_pad($groups_row["GroupComment"], 40));
			  echo "<OPTION value='" . $groups_row["Groups"] . "'>" . $value1 . $value2 . $value3 . "</OPTION>";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>
	  
	<br>
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	  <tr>
	  <td>
		<p>Who created this report?</p>
<p><textarea style="width:300px;margin:5px 0 5px 0;" rows="5" name="w1">
AGENCY OR COMPANY THAT YOU WORK FOR
[YOUR CONTACT INFO]
</textarea></p>
	  </td>
	  <td>
		<p>Who is this report for?</p>
<p><textarea style="width:300px;margin:5px 0 5px 0;" rows="5" name="w2">
[AGENCY]
[PERSON(S) RESPONSIBLE]
</textarea></p>
	  </td>
	  </tr>
	</table>  
	<br><table>
	  <TR>
		<TD>
		<input type="hidden" name="agency" value="<?php echo "$agency";?>">
		<input type="hidden" name="Host" value="<?php echo "$Host";?>">
		<input type="hidden" name="FileDate" value="<?php echo "$FileDate";?>">
		<input type="hidden" name="FileName" value="<?php echo "$FileName";?>">
		<INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
		</TD>
	  </TR>
	</table>

	  </td>
	  </form>
    </tr>
    <tr>
      <td colspan="2">
			<?php include '../main/footer.php'; ?>
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

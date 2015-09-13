<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

$option = explode("%%", $_POST["option"]);
$v = new Valitron\Validator($option);
$v->rule('slug', '0');
$v->rule('regex','1','/^([\w.-])+$/'); 
$v->rule('regex','2','/^([\w \/:])+$/'); 
$v->rule('regex','3','/^([\w _.-])+$/'); 
if(!$v->validate()) {
	print_r($v->errors());
	exit;
} 
$agency = $option[0];
$Host = $option[1];
$FileDate = $option[2];
$FileName = $option[3];

$agency_sql = 	"SELECT DISTINCT 
			dumpsec_user_table.agency, 
			dumpsec_user_table.Host, 
			dumpsec_user_table.FileDate, 
			dumpsec_user_table.FileName 
		FROM 
			dumpsec_user_table
		";
$agency_stmt = $db->prepare($agency_sql);
$agency_stmt->execute();

if($agency != ""){
	$group_sql = "SELECT DISTINCT
					dumpsec_group_table.GroupName,
					dumpsec_group_table.`Comment`,
					dumpsec_group_table.GroupType
				FROM
					dumpsec_group_table
				WHERE
					dumpsec_group_table.Agency =  ? AND
					dumpsec_group_table.Host =  ?
				";
	$group_data = array($agency,$Host);
	$group_stmt = $db->prepare($group_sql);
	$group_stmt->execute($group_data);
}
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
<table width="100%">
	<tr>
		<td width="200px" valign="top"><?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">
	<table style="text-align: left; width: 650px;" border="0" cellpadding="0" cellspacing="0">
     <tr>
      <td valign="top">
	  <form name="f1"  action="" method="post">
	  
	  <p align="center">[ DumpSec Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.</p>
  	  <select NAME="option" SIZE="10"  style="width:800px;margin:5px 0 5px 0;" ONCHANGE="f1.submit()">
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Host]",20)).str_replace(' ','&nbsp;',str_pad("[Date]",20)).str_replace(' ','&nbsp;',str_pad("[File Name]",50))."</option>";
			while($agency_row = $agency_stmt->fetch(PDO::FETCH_ASSOC)){
			  $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			  $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["Host"], 20));
			  $value3 = str_replace(' ','&nbsp;',str_pad($agency_row["FileDate"], 20));
			  $value4 = str_replace(' ','&nbsp;',str_pad($agency_row["FileName"], 50));
			  echo "<option value='" . $agency_row["agency"] . "%%" . $agency_row["Host"] . "%%" . $agency_row["FileDate"] . "%%" . $agency_row["FileName"] . "'>" . $value1 . $value2 . $value3 . $value4 . "</option>";
			}
			?>
	  </select>
	  </form>
	  </td>
	</tr>  
	<tr>
	  <td style="width: 700px;" valign="top"> 
	  <form name="f2" action="report.php" method="post">
	  <input type="hidden" name="MAX_FILE_SIZE" value="2000000000" />	  
		<?php
		//host list
		if($agency == ""){
		?>
			<p align="center">[ Groups ]</p>
			<SELECT MULTIPLE NAME="group" SIZE="25" style="width:1600px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Groups ]</p><p><input type="button" name="Button" value="Select All" onclick="selectAll('groupselectall',true)" /></p>
			<SELECT MULTIPLE NAME="groups[]" SIZE="20" style="width:1600px;margin:5px 0 5px 0;" id="groupselectall">
		<?php
			echo "<option value=\"REMOVE\">".str_replace(' ','&nbsp;',str_pad("[Group Name]", 70)).str_replace(' ','&nbsp;',str_pad("[Group Comment]", 200)).str_replace(' ','&nbsp;',str_pad("[Type]", 10))."</option>";
			while($group_row = $group_stmt->fetch(PDO::FETCH_ASSOC)){
			  $value1 = str_replace(' ','&nbsp;',str_pad($group_row["GroupName"], 70));
			  $value2 = str_replace(' ','&nbsp;',str_pad($group_row["Comment"], 200));
			  $value3 = str_replace(' ','&nbsp;',str_pad($group_row["GroupType"], 10));
			  echo "<OPTION value='" . $group_row["GroupName"] . "'>" . $value1 . $value2 . $value3 . "</OPTION>";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>	 
		</td>
		</tr>
		<tr>
		<td>
	<br><table>
	  <TR>
		<TD><p>
		
		<input type="hidden" name="agency" value="<?php echo "$agency";?>">
		<input type="hidden" name="Host" value="<?php echo "$Host";?>">
		<input type="hidden" name="FileDate" value="<?php echo "$FileDate";?>">
		<input type="hidden" name="FileName" value="<?php echo "$FileName";?>">
		<INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT"></p>
		</TD>
	  </TR>
		<tr>
			<td>
			<p> </p>
			<hr>
			<table>
			  <tr>
				<td colspan=2><input type="checkbox" value="yes" name="includePasswords" >Include Passwords</input></td></tr>
				<tr><td><p>Select Username:Hash file: </p></td><td><input name="user_hashes" type="file" /></td></tr>
				<tr><td><p>Select Hash:Password file: </p></td><td><input name="hashes_pass" type="file" /></td></tr>
			
			</table>
			</td>
		</tr>	  
	  
	</table>	  
	  
	  
	  </form>
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


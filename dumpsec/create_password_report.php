<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
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
?>

<HTML>
<head>
<title>CREATE PASSWORD REPORT</title>
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
	  <form action="password_report.php" method="post">
	  
	  <p align="center">[ Password Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.</p>
  	  <select NAME="option" style="width:800px;margin:5px 0 5px 0;">
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
	  </td>
	</tr>  

		<tr>
		<td><p><INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT"></p>
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


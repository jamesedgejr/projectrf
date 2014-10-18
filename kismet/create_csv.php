<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$agency_sql = 	"SELECT DISTINCT 
			kismet_results_newcore.agency, 
			kismet_results_newcore.location
		FROM 
			kismet_results_newcore
		";
$agency_stmt = $db->prepare($agency_sql);
$agency_stmt->execute();
?>

<HTML>
<head>
<title>CREATE KISMET CSV FILE</title>
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
	  <form name="f1"  action="csvReport.php" method="post">
	  <p align="center">[ Kismet CSV Reports ]</p>
	  <p align="center">Select Agency/Location that you uploaded to the database.</p>
  	  <select NAME="agency" SIZE="10"  style="width:600px;margin:5px 0 5px 0;">
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Location]",20))."</option>";
			while($agency_row = $agency_stmt->fetch(PDO::FETCH_ASSOC)){
			  $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			  $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["location"], 20));
			  echo "<option value='" . $agency_row["agency"] . "%%" . $agency_row["location"] . "'>" . $value1 . $value2 . "</option>";
			}
			?>
	  </select>
	  </td></tr>
	  <tr><td>
      <table style="text-align: left; width: 225px;" border="0" cellpadding="2" cellspacing="2">
           <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">CSV Choices</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isGPS" checked>
			</td>
            <td style="width: 174px;">GPS</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isSignalStr" checked>
			</td>
            <td style="width: 174px;">Signal Strength</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isWC" checked>
			</td>
            <td style="width: 174px;">Wireless Clients</td>
          </tr>
      </table>

      <table style="text-align: left; width: 225px;" border="0" cellpadding="2" cellspacing="2">
           <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Packet Types</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isInfra" checked>
			</td>
            <td style="width: 174px;">Infrastructure</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isProbe" checked>
			</td>
            <td style="width: 174px;">Probe</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="Ad-Hoc" checked>
			</td>
            <td style="width: 174px;">Ad-Hoc</td>
          </tr>
      </table>
	 
	  </td></tr>
	  <tr><td>
		<INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT"></form>
	  </td></tr>
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


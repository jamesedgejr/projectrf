<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

$agency_sql = 	"SELECT DISTINCT 
					nessus_results.agency
				FROM 
					nessus_results
				";
$agency_stmt = $db->prepare($agency_sql);
$agency_stmt->execute();
?>

<HTML>
<head>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
select {font-family: courier new}
</style>
</head>
<BODY>
<table width="100%"><tr><td width="200px" valign="top"><?php include '../main/menu.php'; ?></td>
<td valign="top">
<table style="text-align: left; width: 950px;" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2">
	  <form action="edge2.php" method="post">
	  <p align="center">[ Nessus Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.</p>
  	  <select NAME="agency">
			<?php
			while($agency_row = $agency_stmt->fetch(PDO::FETCH_ASSOC)){
				echo "<option value='" . $agency_row["agency"] . "'>" . $agency_row["agency"] . "</option>";
			}
			?>
	  </select>
	  <p>Vulnerability Category</p>
  	  <select NAME="vuln_cat">
			<option value="isSSLIssues">SSL Certificate Issues</option>
			<option value="isRDPIssues">RDP Issues</option>
			<option value="isSMBIssues">SMB Issues</option>
			<option value="isSSHIssues">SSH Issues</option>
			<option value="isPHPIssues">PHP Issues</option>
			<option value="isHPIssues">HP Issues</option>
			<option value="isMSIssues">Microsoft Bulletins</option>
			<option value="isCleartext">Cleartext Protocols</option>
			<option value="isDatabaseIssues">Database Issues</option>
			<option value="isOutdatedOS">Unsuported OS</option>
			<option value="isInformationLeaks">Information Leakage</option>
			<option value="isCustom">Custom</option>
			<option value="isAllIssues">All Issues</option>
	  </select>
 	  </td>
	</tr>
	<tr><td colspan="2">
	 <table>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Severity</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="4" name="severity" checked>
			</td>
            <td style="width: 174px;">Critical Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="3" name="severity">
			</td>
            <td style="width: 174px;">High Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="2" name="severity">
			</td>
            <td style="width: 174px;">Medium Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="1" name="severity">
			</td>
            <td style="width: 174px;">Low Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="0" name="severity">
			</td>
            <td style="width: 174px;">Information Only</td>
          </tr>
		 </table>
		</td></tr>
	<tr>
	  <td style="width: 700px;" valign="top"> 
	<br>
<table>
	  <TR>
		<TD>
		<INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
		</TD>
	  </TR>
	</table>

	  </td>
      <td style="width: 250px;" valign="top" align="right">
 
	  </form>
      </td>
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


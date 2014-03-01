<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
$agency_temp = explode(":", $_POST["agency"]);
$agency = $agency_temp[0];
$XmlReport_Name = $agency_temp[1];
$agency_sql = 	"SELECT DISTINCT AppScan_IssueTypes.agency, AppScan_IssueTypes.XmlReport_Name FROM AppScan_IssueTypes";
$agency_result = $db->query($agency_sql);ifError($threat_result);

if($agency != ""){
	$url_sql = "SELECT DISTINCT AppScan_Issues.Url, AppScan_Issues.XmlReport_Name, AppScan_Issues.agency FROM AppScan_Issues WHERE AppScan_Issues.XmlReport_Name = '$XmlReport_Name' AND AppScan_Issues.agency = '$agency' ORDER BY AppScan_Issues.Url ASC";
	$url_result = $db->query($url_sql);ifError($url_result);
	$threat_sql = "SELECT DISTINCT AppScan_IssueTypes.agency, AppScan_IssueTypes.XmlReport_Name, AppScan_IssueTypes.threatClassification_name FROM AppScan_IssueTypes WHERE AppScan_IssueTypes.agency = '$agency' AND AppScan_IssueTypes.XmlReport_Name = '$XmlReport_Name' ORDER BY AppScan_IssueTypes.Severity ASC";	$threat_result = $db->query($threat_sql);ifError($threat_result);
}//end if

?>

<HTML>
<head>
<title>CREATE APPSCAN REPORT</title>
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
<table width="100%"><tr><td width="200px" valign="top"><?php include '../main/menu.php'; ?></td><td valign="top">


<table style="text-align: left; width: 850px;" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td style="width: 600px;">
	  <form name="f1"  action="" method="post">
	  <p align="center">[ AppScan Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.  <br>Then select the URLs and the AppScan Threat Classifications you want to include.</p>
  	  <select NAME="agency" SIZE="10"  style="width:600px;margin:5px 0 5px 0;" ONCHANGE="f1.submit()" >
		<option value="none" selected>[Agency]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Report Name]</option>
			<?php
			while($agency_row = $agency_result->fetchRow(DB_FETCHMODE_ASSOC)){
				#obtain date and time of the scan.  In this instance we pull the end time from the last host to finish scanning 
			    $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["XmlReport_Name"], 40));
				echo "<option value='" . $agency_row["agency"] . ":" . $agency_row["XmlReport_Name"] . "'>" . $value1 . $value2  . "</option>";
			}
			?>
	  </select>
	  </form>
	<form name="f2" action="report.php" method="post">
		<?php
		//host list
		if($agency == ""){
		?>
			<p align="center">[ URLs ]</p>
			<SELECT MULTIPLE NAME="Url" SIZE="25" style="width:600px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ URLs ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('urlselectall',true)" />
			<SELECT MULTIPLE NAME="Url[]" SIZE="20" style="width:600px;margin:5px 0 5px 0;" id="urlselectall">
		<?php
			while($url_row = $url_result->fetchRow(DB_FETCHMODE_ASSOC)){
			  echo "<OPTION value='" . $url_row["Url"] . "'>" . $url_row["Url"] . "</OPTION>";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>
		<?php
		//nessus plugin families
		if($agency == ""){
		?>
			<p align="center">[ Threat Classifications ]</p>
			<SELECT MULTIPLE NAME="threat" SIZE="15" style="width:600px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Threat Classifications ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('threatselectall',true)" />
			<SELECT MULTIPLE NAME="threat[]" SIZE="15" style="width:600px;margin:5px 0 5px 0;" id="threatselectall">
		<?php
			while($threat_row = $threat_result->fetchRow(DB_FETCHMODE_ASSOC)){
					echo "<OPTION value='" . $threat_row["threatClassification_name"] . "'>" . $threat_row["threatClassification_name"] . "</OPTION>";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>	 
	<br><table>
	  <TR>
		<TD>
		<input type="hidden" name="agency" value="<?php echo "$agency";?>">
		<input type="hidden" name="XmlReport_Name" value="<?php echo "$XmlReport_Name";?>">
		<INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
		</TD>
	  </TR>
	</table>

 
	  </td>
      <td style="width: 250px;" valign="top" align="right">
      <table style="text-align: left; width: 225px;" border="0" cellpadding="2" cellspacing="2">
           <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Severity</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="High" name="high" checked>
			</td>
            <td style="width: 174px;">High Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="Medium" name="medium" checked>
			</td>
            <td style="width: 174px;">Medium Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="Low" name="low" checked>
			</td>
            <td style="width: 174px;">Low Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="Informational" name="info" checked>
			</td>
            <td style="width: 174px;">Information Only</td>
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
</form>

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
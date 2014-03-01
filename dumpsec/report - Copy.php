<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
ifError($db);

$groupsPost = $_POST["groups"];
foreach($groupsPost as $key => $value) {
	if ($value == "REMOVE") unset($groupsPost[$key]);
}
$groups = $_POST["groups"];
$sql = "CREATE temporary TABLE dumpsec_tmp_groups (groups VARCHAR(255))";
$result = $db->query($sql);ifError($result);
foreach ($groups as $g){
	$sql="INSERT INTO dumpsec_tmp_groups (groups) VALUES ('$g')";
	$result = $db->query($sql);ifError($result);	
}

$agency = $_POST["agency"];
$Host = $_POST["Host"];
$FileDate = $_POST["FileDate"];
$FileName = $_POST["FileName"];
echo $agency . " " . $Host . " " . $FileDate . " " . $FileName . "<br>";
$cover = $_POST["cover"];
$whocreated = str_replace("\n","<br>", $_POST["w1"]);
$whofor = str_replace("\n","<br>", $_POST["w2"]);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title><?php echo "$agency - DumpSec Vulnerability Report";?></title>
<link rel="stylesheet" type="text/css" href="../main/style_nessus.css" />
</head>
<body>
<?php
//include cover page
if($cover == "y"){
?>
	<table class="main" style="width: 600px;">
		<tr>
			<td class="top" style="text-align:center;">
				<p>NESSUS - Network Vunlerability Scanner</p>
			</td>
		</tr>
	</table><br>
	<table class="main" style="width: 600px;">
		<tr>
			<td colspan="2" class="top" style="text-align: center;font-size: 20pt;">
				<p>Confidential Information</p>

			</td>
		</tr>
		<tr>
			<td colspan="2" class="right">
				<p>The following report contains confidential information. Do not
				distribute, email, fax, or transfer via any electronic mechanism unless
				it has been approved by the recipient company's security policy. All
				copies and backups of this document should be saved on protected
				storage at all times. Do not share any of the information contained
				within this report with anyone unless they are authorized to view the
				information.</p>
			</td>
		</tr>
		<tr>
			<td class="left">
				<p><b>Created By:</b></p>
			</td>
			<td class="left">
				<p><b>Created For:</b></p>
			</td>
		</tr>
		<tr>
			<td width="50%" valign="top" class="right">
				<p><?php echo "$whocreated";?></p>
			</td>
			<td width="50%" valign="top" class="right">
				<p><?php echo "$whofor";?></p>
			</td>
		</tr>
	</table>
	<br style="page-break-after: always;" clear="all">
<?php
}//end include cover page
?>


<?php
$main_sql = "SELECT DISTINCT
			dumpsec_user_table.UserName,
			dumpsec_user_table.FullName,
			dumpsec_user_table.AccountType,
			dumpsec_user_table.`Comment`,
			dumpsec_user_table.HomeDrive,
			dumpsec_user_table.HomeDir,
			dumpsec_user_table.Profile,
			dumpsec_user_table.LogonScript,
			dumpsec_user_table.Workstations,
			dumpsec_user_table.PswdCanBeChanged,
			dumpsec_user_table.PswdLastSetTime,
			dumpsec_user_table.PswdRequired,
			dumpsec_user_table.PswdExpires,
			dumpsec_user_table.PswdExpiresTime,
			dumpsec_user_table.AcctDisabled,
			dumpsec_user_table.AcctLockedOut,
			dumpsec_user_table.AcctExpiresTime,
			dumpsec_user_table.LastLogonTime,
			dumpsec_user_table.LastLogonServer,
			dumpsec_user_table.LogonHours
		FROM
			dumpsec_user_table
			Inner Join dumpsec_tmp_groups ON dumpsec_user_table.Groups = dumpsec_tmp_groups.groups
		WHERE
			dumpsec_user_table.Agency =  '$agency' AND
			dumpsec_user_table.Host =  '$Host' AND
			dumpsec_user_table.FileDate =  '$FileDate' AND
			dumpsec_user_table.FileName =  '$FileName' AND
			dumpsec_user_table.AcctDisabled = 'No'
			
		ORDER BY
			dumpsec_user_table.PswdLastSetTime
		";

$main_result = $db->query($main_sql);
ifError($main_result);	

if(!$main_result->numRows()){
	echo "<hr><p align=\"center\"><b>No Rows were returned.  You may have not selected any hosts or there are no hosts with the severity of vulnerability or Nessus Plugin Family you chose to display.</b></p><hr>";
}
while($row = $main_result->fetchRow(DB_FETCHMODE_ASSOC)){
	$FileDateUTC = strtotime($FileDate);
	$PswdLastSetTimeUTC = strtotime($row["PswdLastSetTime"]);
	$passwordAgeDays = ($FileDateUTC - $PswdLastSetTimeUTC) / 86400;
	$passwordAgeYears = $passwordAgeDays / 365;
	
	echo  "\"" . $row["UserName"] . "\",\"" . $row["FullName"] . "\",\"" . $row["Comment"] . "\",\"" . $row["PswdLastSetTime"] . "\",\"" . $passwordAgeDays . "\",\"" . $passwordAgeYears . "\"<br>";

?>


<?php 
}//endwhile
?>
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



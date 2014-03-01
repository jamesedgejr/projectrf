<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
ifError($db);
$option = explode("%%", $_POST["option"]);
$agency = $option[0];
$Host = $option[1];
$FileDate = $option[2];
$FileName = $option[3];
echo $agency . " " . $Host . " " . $FileDate . " " . $FileName . "<br>";

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
echo  "\"User Name\",\"Full Name\",\"Comment\",\"Password Last Changed\",\"Password Age (Days)\",\"Password Age (Years)\"<br>";
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



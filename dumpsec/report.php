<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v = new Valitron\Validator($_POST);
$v->rule('slug', 'agency');
$v->rule('regex','Host','/^([\w.-])+$/'); 
$v->rule('regex','FileDate','/^([\w \/:])+$/'); 
$v->rule('regex','FileName','/^([\w _.-])+$/'); 
if(!$v->validate()) {
	print_r($v->errors());
	exit;
} 
$agency = $_POST["agency"];
$Host = $_POST["Host"];
$FileDate = $_POST["FileDate"];
$FileName = $_POST["FileName"];

$groupsArray = $_POST["groups"];
foreach($groupsArray as $key => $value) {
	if ($value == "REMOVE") unset($groupsArray[$key]);
}
$sql = "CREATE temporary TABLE dumpsec_tmp_groups (GroupName VARCHAR(255), INDEX ndx_GroupName (GroupName))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($groupsArray as $gA){
	$sql="INSERT INTO dumpsec_tmp_groups (GroupName) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($gA));
}

date_default_timezone_set('UTC');
$myDir = getcwd() . "/csvfiles/";
$myFileName = "dumpsec_" . date('mdYHis') . ".csv";
$myFile = $myDir . $myFileName;
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title><?php echo "$agency - DumpSec Vulnerability Report";?></title>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
select {font-family: courier new}
</style>
</head>
<body>

<?php
$main_sql = "SELECT DISTINCT
				dumpsec_user_table.UserName,
				dumpsec_user_table.FullName,
				dumpsec_user_table.`Comment`,
				dumpsec_user_table.HomeDrive,
				dumpsec_user_table.HomeDir,
				dumpsec_user_table.`Profile`,
				dumpsec_user_table.LogonScript,
				dumpsec_user_table.Workstations,
				dumpsec_user_table.PswdLastSetTime,
				dumpsec_user_table.PswdExpires,
				dumpsec_user_table.PswdExpiresTime,
				dumpsec_user_table.AcctDisabled,
				dumpsec_user_table.LastLogonTime,
				dumpsec_user_table.LastLogonServer,
				dumpsec_user_table.PasswordAgeDays,
				dumpsec_user_table.LastLogonAgeDays,
				dumpsec_user_table.NTLMHash,
				dumpsec_user_table.`password`,
				dumpsec_group_table.GroupName,
				dumpsec_group_table.`Comment`
		FROM
			dumpsec_user_table
		INNER JOIN dumpsec_group_table ON dumpsec_user_table.UserName = dumpsec_group_table.GroupMember
		INNER JOIN dumpsec_tmp_groups ON dumpsec_tmp_groups.GroupName = dumpsec_group_table.GroupName
		WHERE
			dumpsec_user_table.Agency =  ? AND
			dumpsec_user_table.Host =  ? AND
			dumpsec_user_table.FileDate =  ? AND
			dumpsec_user_table.FileName =  ? AND
			dumpsec_user_table.AcctDisabled = 'No'
		ORDER BY
			dumpsec_user_table.PasswordAgeDays
		";
$data = array($agency, $Host, $FileDate, $FileName);
$main_stmt = $db->prepare($main_sql);
$main_stmt->execute($data);

$header = array("Username", "Full Name", "User Comment", "Group Name", "Group Comment", "Password Expires", "Password Last Changed", "Last Logon Time", "Password Age (Days)", "NTLM", "Password");
fputcsv($fh, $header);

while($row = $main_stmt->fetch(PDO::FETCH_ASSOC)){

echo "<hr>";
	$UserName = $row["UserName"];
	$FullName = $row["FullName"];
	$UserComment = $row["UserComment"];
	$GroupName = $row["GroupName"];
	$GroupComment = $row["GroupComment"];
	$PswdExpires = $row["PswdExpires"];
	$PswdLastSetTime = $row["PswdLastSetTime"];
	$LastLogonTime = $row["LastLogonTime"];
	$PasswordAgeDays = $row["PasswordAgeDays"];
	$LastLogonAgeDays = $row["LastLogonAgeDays"];
	$NTLMHash = $row["NTLMHash"];
	$password = $row["password"];

	$data_row = array($UserName, $FullName, $UserComment, $GroupName, $GroupComment, $PswdExpires, $PswdLastSetTime, $LastLogonTime, $PasswordAgeDays, $NTLMHash, $password);
	fputcsv($fh, $data_row);

?>


<?php 
}//endwhile
?>
<table width="100%"><tr>
	<td width="20%" valign="top">
	<?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">
		<hr>
		<p align="center"><a href="csvfiles/<?php echo "$myFileName";?>">Click Here</a> to download the CSV file.</p>
		<hr>
	</td>
</tr></table>
</body>
</html>



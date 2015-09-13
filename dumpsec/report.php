<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v = new Valitron\Validator($_POST);
$v->rule('slug', 'agency');
$v->rule('regex','Host','/^([\w.-])+$/'); 
$v->rule('regex','FileDate','/^([\w \/:])+$/'); 
$v->rule('regex','FileName','/^([\w _.-])+$/'); 
$v->rule('accepted', 'includePasswords');
if(!$v->validate()) {
	print_r($v->errors());
	exit;
} 
$agency = $_POST["agency"];
$Host = $_POST["Host"];
$FileDate = $_POST["FileDate"];
$FileName = $_POST["FileName"];
$includePasswords = $_POST["includePasswords"];
if($includePasswords){

	$filename_user_hashes = $_FILES['user_hashes']['name'];
	echo "<hr>" . $filename_user_hashes;
	$uploaddir = sys_get_temp_dir();
	$uploadfile_user_hashes = tempnam(sys_get_temp_dir(), basename($_FILES['user_hashes']['name']));
	if (move_uploaded_file($_FILES['user_hashes']['tmp_name'], $uploadfile_user_hashes)) {
		echo "<hr><p align=\"center\"><b>File ".$uploadfile_user_hashes." is valid, and was successfully uploaded.</b></p><hr>";
		} else { 
			echo "<h1>Upload Error!</h1>";
			echo "An error occurred while executing this script. The file may be too large or the upload folder may have insufficient permissions.";
			echo "<p />";
			echo "Please examine the following items to see if there is an issue";
			echo "<hr><pre>";
			echo "1.  ".$uploaddir." (Temp) directory exists and has the correct permissions.<br />";
			echo "2.  The php.ini file needs to be modified to allow for larger file uploads.<br />";
			echo "3.  The file name is ".$filename_user_hashes.".  If that is blank then its not being uploaded<br />";
			echo "</pre><hr>";
			exit; 
	}

	$filename_hashes_pass = $_FILES['hashes_pass']['name'];
	$uploaddir = sys_get_temp_dir();
	$uploadfile_hashes_pass = tempnam(sys_get_temp_dir(), basename($_FILES['hashes_pass']['name']));
	if (move_uploaded_file($_FILES['hashes_pass']['tmp_name'], $uploadfile_hashes_pass)) {
		echo "<hr><p align=\"center\"><b>File ".$uploadfile_hashes_pass." is valid, and was successfully uploaded.</b></p><hr>";
		} else { 
			echo "<h1>Upload Error!</h1>";
			echo "An error occurred while executing this script. The file may be too large or the upload folder may have insufficient permissions.";
			echo "<p />";
			echo "Please examine the following items to see if there is an issue";
			echo "<hr><pre>";
			echo "1.  ".$uploaddir." (Temp) directory exists and has the correct permissions.<br />";
			echo "2.  The php.ini file needs to be modified to allow for larger file uploads.<br />";
			echo "</pre><hr>";
			exit; 
	}
	$user_hashes_sql = "CREATE temporary TABLE dumpsec_tmp_user_hashes (username VARCHAR(255), hash VARCHAR(255))";
	$user_hashes_stmt = $db->prepare($user_hashes_sql);$user_hashes_stmt->execute();
	$hashes_pass_sql = "CREATE temporary TABLE dumpsec_tmp_hashes_pass (hash VARCHAR(255), password VARCHAR(255))";
	$hashes_pass_stmt = $db->prepare($hashes_pass_sql);$hashes_pass_stmt->execute();
	if (($handle = fopen($uploadfile_user_hashes, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ":")) !== FALSE) {
			echo $data[0] . "::::" . $data[1] . "<br>";
			$sql="INSERT INTO dumpsec_tmp_user_hashes (username,hash) VALUES (?,?)";
			$stmt = $db->prepare($sql);
			$stmt->execute(array($data[0],$data[1]));		
		}
	}
	echo "<hr>";
	if (($handle = fopen($uploadfile_hashes_pass, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ":")) !== FALSE) {
			echo $data[0] . "::::" . $data[1] . "<br>";
			$sql="INSERT INTO dumpsec_tmp_hashes_pass (hash,password) VALUES (?,?)";
			$stmt = $db->prepare($sql);
			$stmt->execute(array($data[0],$data[1]));
		}
	}	

}

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
			dumpsec_user_table.LogonHours";
		if($includePasswords == "yes"){
			$main_sql .= ",
						password_results.password_hash,
						password_results.password_result
						";
		}
$main_sql .="
		FROM
			dumpsec_user_table
		INNER JOIN dumpsec_group_table ON dumpsec_user_table.UserName = dumpsec_group_table.GroupMember
		INNER JOIN dumpsec_tmp_groups ON dumpsec_tmp_groups.GroupName = dumpsec_group_table.GroupName
		";
	if($includePasswords == "yes"){
		$main_sql .= "
			Left Join password_results ON password_results.agency = dumpsec_group_table.Agency AND password_results.username = dumpsec_group_table.GroupMember				
		";
	}	
$main_sql .="	
		WHERE
			dumpsec_user_table.Agency =  ? AND
			dumpsec_user_table.Host =  ? AND
			dumpsec_user_table.FileDate =  ? AND
			dumpsec_user_table.FileName =  ? AND
			dumpsec_user_table.AcctDisabled = 'No'
			
		ORDER BY
			dumpsec_user_table.PswdLastSetTime
		";
$data = array($agency, $Host, $FileDate, $FileName);
$main_stmt = $db->prepare($main_sql);
$main_stmt->execute($data);

fwrite($fh, "\"User Name\",\"Full Name\",\"Comment\",\"Password Expires\",\"Password Last Changed\",\"Last Logon Time\",\"Password Age (Days)\",\"Password Age (Years)\"");
if($includePasswords == "yes"){
	fwrite($fh, ",\"Password\"");
}
fwrite($fh, "\n");
while($row = $main_stmt->fetch(PDO::FETCH_ASSOC)){
	$username = $row["UserName"];
	$fullname = $row["UserName"];
	$comment = $row["Comment"];
	$PswdExpires = $row["PswdExpires"];
	$PswdLastSetTime = $row["PswdLastSetTime"];
	$LastLogonTime = $row["LastLogonTime"];
	
	$FileDateUTC = strtotime($FileDate);
	$PswdLastSetTimeUTC = strtotime($PswdLastSetTime);
	$passwordAgeDays = ($FileDateUTC - $PswdLastSetTimeUTC) / 86400;
	$passwordAgeYears = $passwordAgeDays / 365;

	
	fwrite($fh, "\"$username\",\"$fullname\",\"$comment\",\"$PswdExpires\",\"$PswdLastSetTime\",\"$LastLogonTime\",\"$passwordAgeDays\",\"$passwordAgeYears\"");
	if($includePasswords == "yes"){
		$password_result = $row["password_result"];
		fwrite($fh, ",\"$password_result\"");
	}
	fwrite($fh, "\n");

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



<html>
<head>
<title> DumpSec Parse </title>
<style type="text/css">
p {font-size: 70%}
a {text-decoration: none}
a:hover {text-decoration: underline}
</style>
</head>
<body>
<table width="100%"><tr>
	<td width="20%" valign="top">
	<?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">
<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v = new Valitron\Validator($_POST);
$v->rule('slug', 'agency');
if(!$v->validate()) {

    print_r($v->errors());
	exit;
} 
ini_set("memory_limit","256M");
$filename = $_FILES['userfile']['name'];
$filetype = $_POST["filetype"];
$uploaddir = sys_get_temp_dir();
$uploadfile = tempnam(sys_get_temp_dir(), basename($_FILES['userfile']['name']));
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
	echo "<hr><p align=\"center\"><b>File is valid, and was successfully uploaded.</b></p><hr>";
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
$agency = $_POST["agency"];
$Host = $FileDate = ""; 

if($filetype == "user"){
	$row = 1;
	if (($handle = fopen($uploadfile, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
			if($row == 1){
				$Row1Array = explode(" ", $data[0]);
				$FileDate = $Row1Array[0] . " " . $Row1Array[1] . " " .  $Row1Array[2];
				$Host = trim($Row1Array[9], "\\");	
			}
			$row++; 
			if ($row > 4) {
				$FileDateUTC = strtotime($FileDate);
				$UserName = addslashes($data[0]);
				$FullName = addslashes($data[1]);
				$AccountType = $data[2];
				$Comment = addslashes($data[3]);
				$HomeDrive = $data[4];
				$HomeDir = addslashes($data[5]);
				$Profile = $data[6];
				$LogonScript = $data[7];
				$Workstations = $data[8];
				$PswdCanBeChanged = trim($data[9]);
				$PswdLastSetTime = $data[10];
					$PswdLastSetTimeUTC = strtotime($PswdLastSetTime);
				$PswdRequired = trim($data[11]);
				$PswdExpires = trim($data[12]);
				$PswdExpiresTime = $data[13];
				$AcctDisabled = trim($data[14]);
				$AcctLockedOut = trim($data[15]);
				$AcctExpiresTime = $data[16];
				$LastLogonTime = $data[17];
					$LastLogonTimeUTC = strtotime($LastLogonTime);
				$LastLogonServer = $data[18];
				$LogonHours = $data[19];
				$Sid = $data[20];
				
				if($PswdLastSetTimeUTC != "") {
					$PasswordAgeDays = ($FileDateUTC - $PswdLastSetTimeUTC) / 86400;
				} else {
					$PasswordAgeDays = "none";
				}
				if($LastLogonTimeUTC != ""){
					$LastLogonAgeDays = ($FileDateUTC - $LastLogonTimeUTC) / 86400;
				} else {
					$LastLogonAgeDays = "none";
				}

				
				
				$sql = "INSERT INTO dumpsec_user_table (
						Agency,
						FileDate,
						FileName,
						Host,
						UserName,
						FullName,
						AccountType,
						Comment,
						HomeDrive,
						HomeDir,
						Profile,
						LogonScript,
						Workstations,
						PswdCanBeChanged,
						PswdLastSetTime,
						PswdRequired,
						PswdExpires,
						PswdExpiresTime,
						AcctDisabled,
						AcctLockedOut,
						AcctExpiresTime,
						LastLogonTime,
						LastLogonServer,
						LogonHours,
						PasswordAgeDays,
						LastLogonAgeDays
				) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
				";
				$stmt = $db->prepare($sql);
				$sql_data = array($agency, $FileDate, $filename, $Host, $UserName, $FullName, $AccountType, $Comment, $HomeDrive, $HomeDir, $Profile, $LogonScript, $Workstations, $PswdCanBeChanged, $PswdLastSetTime, $PswdRequired, $PswdExpires, $PswdExpiresTime, $AcctDisabled, $AcctLockedOut, $AcctExpiresTime, $LastLogonTime, $LastLogonServer, $LogonHours, $PasswordAgeDays, $LastLogonAgeDays);
				$stmt->execute($sql_data);
			}// end if
		}// end while
		fclose($handle);
	}// end if
} // end if

if($filetype == "group"){
	$row = 1;
	if (($handle = fopen($uploadfile, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
			if($row == 1){
				$Row1Array = explode(" ", $data[0]);
				$FileDate = $Row1Array[0] . " " . $Row1Array[1] . " " .  $Row1Array[2];
				$Host = trim($Row1Array[9], "\\");
			}
			$row++;
			$GroupName = addslashes($data[0]); 
			$Comment = addslashes($data[1]);
			$GroupType = $data[2];
			$GroupMember = addslashes($data[3]);
			$MemberType = $data[4];
			if ($row > 4) {
				$sql = "INSERT INTO dumpsec_group_table (
					Agency,
					FileDate,
					FileName,
					Host,
					GroupName,
					Comment,
					GroupType,
					GroupMember,
					MemberType
				) VALUES (?,?,?,?,?,?,?,?,?)
				";
				$stmt = $db->prepare($sql);
				$sql_data = array($agency, $FileDate, $filename, $Host, $GroupName, $Comment, $GroupType, $GroupMember, $MemberType);
				$stmt->execute($sql_data);
			}
		} //end while
		fclose($handle);
	}
}

?>
</td></tr></table>
</body></html>



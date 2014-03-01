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


include('../main/config.php'); 
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" ); 

if($filetype == "user"){
	$row = 1;
	$agency = $_POST["agency"];
	$Host = $FileDate = ""; 
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
				$LastLogonTime = $data[17];
				$PswdLastSetTime = $data[10];
				$LastLogonTimeUTC = strtotime($LastLogonTime);
				$PswdLastSetTimeUTC = strtotime($PswdLastSetTime);
				
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
				$username = addslashes($data[0]);
				$fullname = addslashes($data[1]);
				$comment = addslashes($data[3]);
				$homedir = addslashes($data[5]);
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
						RasDialin,
						RasCallback,
						RasCallbackNumber,
						PasswordAgeDays,
						LastLogonAgeDays
				) VALUES (
						'$agency',
						'$FileDate', 
						'$filename',
						'$Host',
						'$username','$fullname','$data[2]','$comment','$data[4]','$homedir',
						'$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]',
						'$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]',
						'$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$PasswordAgeDays','$LastLogonAgeDays'
				)";
				$results = $db->query($sql);ifDBError($results);
			}// end if
		}// end while
		fclose($handle);
	}// end if
} // end if

if($filetype == "group"){
	$row = 1;
	$agency = $_POST["agency"];
	$Host = $FileDate = ""; 
	if (($handle = fopen($uploadfile, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
			if($row == 1){
				$Row1Array = explode(" ", $data[0]);
				$FileDate = $Row1Array[0] . " " . $Row1Array[1] . " " .  $Row1Array[2];
				$Host = trim($Row1Array[9], "\\");
			}
			$row++; 
			$comment = addslashes($data[1]);
			$groupmember = addslashes($data[3]);
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
				) VALUES (
					'$agency',
					'$FileDate', 
					'$filename',
					'$Host',
					'$data[0]','$comment','$data[2]','$groupmember','$data[4]'
				)";
				$results = $db->query($sql);ifDBError($results);
			}
		} //end while
		fclose($handle);
	}
}

?>
</td></tr></table>
</body></html>

<?php

function ifDBError($error)
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

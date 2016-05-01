<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$option = explode("%%", $_POST["option"]);
$v = new Valitron\Validator($option);
$v->rule('slug', '0');
$v->rule('regex','1','/^([\w.-])+$/'); 
$v->rule('regex','2','/^([\w \/:])+$/'); 
$v->rule('regex','3','/^([\w _.-])+$/'); 
if(!$v->validate()) {
	print_r($v->errors());
	exit;
} 
$agency = $option[0];
$Host = $option[1];
$FileDate = $option[2];
$FileName = $option[3];

$a = "[@aA4]";
$b = "[8bB]";
$c = "[cC]";
$d = "[dD]";
$e = "[3eE]";
$f = "[fF]";
$g = "[6gG]";
$h = "[4hH]";
$i = "[\!1iI]";
$j = "[jJ]";
$k = "[kK]";
$l = "[\!1lL]";
$m = "[mM]";
$n = "[nN]";
$o = "[0oO]";
$p = "[pP]";
$q = "[qQ]";
$r = "[rR]";
$s = "[5\$sS]";
$t = "[7tT]";
$u = "[uU]";
$v = "[vV]";
$w = "[wW]";
$x = "[xX]";
$y = "[yY]";
$z = "[zZ]";


$weak_password_stats = array(
	"season" => array("winter"=>0, "spring"=>0, "summer"=>0, "fall_autumn"=>0, "total"=>0, "thePasswords"=>array()),
	"weekday" => array("0"=>0, "1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "total"=>0, "thePasswords"=>array()),
	"month" => array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0, "10"=>0, "11"=>0, "12"=>0, "total"=>0, "thePasswords"=>array()),
	"default" => array("test"=>0,"password"=>0, "welcome"=>0, "username_used"=>0,"total"=>0, "thePasswords"=>array()),
	"company" => array("company_name"=>0, "other"=>0, "total"=>0, "thePasswords"=>array()),
	"other" => array("team"=>0, "other"=>0, "total"=>0, "thePasswords"=>array()),
	"total"=>0
);

$password_complexity_stats = array("length" => 0, "complexity" => 0, "length_complexity" => 0, "total_non" => 0, "total" => 0, "thePasswords"=>array());

$password_age_stats = array("90"=>0,"365"=>0,"730"=>0,"1095"=>0,"1460"=>0,"1825"=>0,"2190"=>0,"2555"=>0,"2920"=>0,"3285"=>0,"3650"=>0);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title><?php echo "$agency - Password Statistics Report";?></title>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
select {font-family: courier new}
</style>
</head>
<body>

<?php
// Query the UserName, FullName, and password
$main_sql = "SELECT
				dumpsec_user_table.UserName,
				dumpsec_user_table.FullName,
				dumpsec_user_table.`password`
			FROM
				dumpsec_user_table
			WHERE
				dumpsec_user_table.Agency = ? AND
				dumpsec_user_table.`Host` = ? AND
				dumpsec_user_table.FileDate = ? AND
				dumpsec_user_table.FileName = ? AND
				dumpsec_user_table.AcctDisabled = 'No'
			";
$data = array($agency, $Host, $FileDate, $FileName);
$main_stmt = $db->prepare($main_sql);
$main_stmt->execute($data);



?>

<table width="100%"><tr>
	<td width="20%" valign="top">
	<?php include '../main/menu.php'; ?>
	</td>
	<td valign=top>
<?php



while($row = $main_stmt->fetch(PDO::FETCH_ASSOC)){

	$UserName = $row["UserName"];
	$FullName = $row["FullName"];
	$password = $row["password"];
	

/********************************************************************
  generate statistics on whether the password met company password complexity requirements.
***********************************************************************/	
	
	$check=0;
	//check for capital letter
	if(preg_match("/[A-Z]/", $password)) {
		$check++;
	}
	//check for lowercase letter
	if(preg_match("/[a-z]/", $password)) {
		$check++;
	}
	//check for digit
	if(preg_match("/[0-9]/", $password)) {
		$check++;
	}	
	//check for special character (anything that is not alphanumeric)
	if(preg_match("/[^a-zA-Z0-9]/", $password)) {
		$check++;
	}
	
	//minumum length 8 with Windows GPO password complexity enabled
	//in the future the variables will be dynamic
	if($password != ""){
		if (strlen($password) < 8 || $check < 3) {
			if (strlen($password) < 8 && $check < 3) { $password_complexity_stats["length_complexity"]++; }
			if(strlen($password) < 8){ 	$password_complexity_stats["length"]++;	}	
			if($check < 2){	$password_complexity_stats["complexity"]++;	}	
			$password_complexity_stats["total_non"]++;
			$password_complexity_stats["total"]++;
			array_push($password_complexity_stats["thePasswords"],$password);
		} else {
			$password_complexity_stats["total"]++;
		}
	}
	
/********************************************************************
  Identify whether the username was used in the password.
***********************************************************************/	
	
	// take the username and lowercase, remove all non-alpha, and 
	// split into array to create variable referencing the alpha_variables ${"{$t1}
	$temp1 = str_split(preg_replace('/[^a-zA-Z]/s','',strtolower($UserName)));

	$temp_pattern = "/";
	foreach($temp1 as $t1){
		$temp_pattern = $temp_pattern.${"{$t1}"};
	}
	$temp_pattern = $temp_pattern . "/";
	if(preg_match($temp_pattern, $password)){
		$weak_password_stats["default"]["username_used"]++;
		$weak_password_stats["default"]["total"]++;
		array_push($weak_password_stats["default"]["thePasswords"], $password);
	} 


}
/********************************************************************
  total users
***********************************************************************/
$sql_u = "SELECT DISTINCT
			dumpsec_user_table.UserName
		FROM
			dumpsec_user_table
		WHERE
			dumpsec_user_table.Agency = ? AND
			dumpsec_user_table.`Host` = ? AND
			dumpsec_user_table.FileDate = ? AND
			dumpsec_user_table.FileName = ? AND
			dumpsec_user_table.AcctDisabled = 'No'
		";
$data = array($agency, $Host, $FileDate, $FileName);
$stmt_u = $db->prepare($sql_u);
$stmt_u->execute($data);
$row_u = $stmt_u->fetch();
$total_users = $stmt_u->rowCount();

/********************************************************************
  total hashes
***********************************************************************/

$sql_h = "SELECT DISTINCT
			dumpsec_user_table.NTLMHash
		FROM
			dumpsec_user_table
		WHERE
			dumpsec_user_table.Agency = ? AND
			dumpsec_user_table.`Host` = ? AND
			dumpsec_user_table.FileDate = ? AND
			dumpsec_user_table.FileName = ? AND
			dumpsec_user_table.AcctDisabled = 'No'
		";
$data = array($agency, $Host, $FileDate, $FileName);
$stmt_h = $db->prepare($sql_h);
$stmt_h->execute($data);
$row_h = $stmt_h->fetch();
$total_hashes = $stmt_h->rowCount();

/********************************************************************
  accounts with a password that does not expire
***********************************************************************/
$sql = "SELECT DISTINCT
			dumpsec_user_table.UserName
		FROM
			dumpsec_user_table
		WHERE
			dumpsec_user_table.Agency = ? AND
			dumpsec_user_table.`Host` = ? AND
			dumpsec_user_table.FileDate = ? AND
			dumpsec_user_table.FileName = ? AND
			dumpsec_user_table.AcctDisabled = 'No' AND
			dumpsec_user_table.PswdExpires = 'No'
		";
$data = array($agency, $Host, $FileDate, $FileName);
$stmt = $db->prepare($sql);
$stmt->execute($data);
$row = $stmt->fetch();
$no_expire_count = $stmt->rowCount();

/********************************************************************
  generate statistics on password age.
***********************************************************************/
$sql = "SELECT DISTINCT
			dumpsec_user_table.UserName,
			dumpsec_user_table.FullName,
			dumpsec_user_table.Comment,
			dumpsec_user_table.PswdCanBeChanged,
			dumpsec_user_table.PswdRequired,
			dumpsec_user_table.PswdExpires,
			dumpsec_user_table.PswdExpiresTime,
			dumpsec_user_table.PasswordAgeDays,
			dumpsec_user_table.password
		FROM
			dumpsec_user_table
		WHERE
			dumpsec_user_table.Agency = ? AND
			dumpsec_user_table.`Host` = ? AND
			dumpsec_user_table.FileDate = ? AND
			dumpsec_user_table.FileName = ? AND
			dumpsec_user_table.AcctDisabled = 'No' AND
			dumpsec_user_table.PasswordAgeDays <> 'none' 
		ORDER BY
			dumpsec_user_table.PasswordAgeDays DESC
		";
$data = array($agency, $Host, $FileDate, $FileName);
$stmt = $db->prepare($sql);
$stmt->execute($data);
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	/*
	If PASSWD_NOTREQD user-Account-Control Attribute Value bit is set, the user is not subject to a possibly existing policy regarding the length of password.
This implies the user could have shorter password than it is required or it may even have no password at all, even if empty passwords are not allowed. This property is not visible in the normal GUI tools (Active Directory Users and Computers)

For this to make sense, please understand that updating a user account password can be accomplished through two seemingly similar, but very distinct operations:

By setting the password
This is done by an administrative user
Typically a permission delegated to helpdesk personnel
Must adhere to userAccountControl settings
Not bound by Password History policy settings
By changing the password
This is done by the user
Typically a part of first authentication attempt after password expiration
Must comply with Password Policy and userAccountControl settings
This means, that an Administrator can set your password to null if your account object permits it (ie. PASSWD_NOTREQD is set), regardless of the Password Policy that applies and how long it requires passwords to be.

You cannot change your password to null however, as the "Password Change" implies that you are replacing a password, not unsetting it. Upon changing the password, the new password will be validated against the effective Password Policy.

I think the easiest way to remember, is that Password Policies apply to Passwords - PASSWD_NOTREQD on the other hand, is a measure of whether you are allowed to not have a password - in which case the policy is no longer relevant.

Is it dangerous?
PASSWORD_NOTREQD might seem like a dangerous flag, but it's not in itself harmful as a lot of mechanisms prevents the usage of null-passwords (or "blank passwords"). As noted above, users cannot unset their own AD user passwords by default, and Windows (consumer and Server editions alike) will reject password authentication over the network, for users with blank passwords, by default - that means you can only log on from the console.
	*/
	$UserName = $row["UserName"];
	$FullName = $row["FullName"];
	$UserComment = $row["Comment"];
	$PswdCanBeChanged = $row["PswdCanBeChanged"];
	$PswdRequired = $row["PswdRequired"];
	$PswdExpires = $row["PswdExpires"];
	$PswdExpiresTime = $row["PswdExpiresTime"];
	$PasswordAgeDays = $row["PasswordAgeDays"];
	$password = $row["password"];
	
	//i've been drinking so this will not be elegant at all
	if($PasswordAgeDays > "90"){$password_age_stats["90"]++;}
	if($PasswordAgeDays > "365"){$password_age_stats["365"]++;} //one year
	if($PasswordAgeDays > "730"){$password_age_stats["730"]++;} //two years
	if($PasswordAgeDays > "1095"){$password_age_stats["1095"]++;} //three years
	if($PasswordAgeDays > "1460"){$password_age_stats["1460"]++;} //four years
	if($PasswordAgeDays > "1825"){$password_age_stats["1825"]++;} //five years
	if($PasswordAgeDays > "2190"){$password_age_stats["2190"]++;} //six years
	if($PasswordAgeDays > "2555"){$password_age_stats["2555"]++;} //seven years
	if($PasswordAgeDays > "2920"){$password_age_stats["2920"]++;} //eight years
	if($PasswordAgeDays > "3285"){$password_age_stats["3285"]++;} //nine years
	if($PasswordAgeDays > "3650"){$password_age_stats["3650"]++;} //ten years

	//Some accounts are set so the password cannot be changed
	if($PswdRequired == "No "){
		//push UserName, FullName, Comment, PasswordAgeDays, and password to Array
		echo $UserName . " " . $FullName . " " . $PasswordAgeDays . "<br>";
	}
	
	//Passwords set to expire but have a password age older than the policy limit (currently set to 90 days) may need to be disabled
	if($PasswordAgeDays > "90" && $PswdExpires == "Yes" && $PswdExpiresTime != "?Uknown"){
		//echo $UserName . " " . $FullName . " " . $PasswordAgeDays . "<br>";
	}

}


/********************************************************************
  generate statistics on whether the chosen passwords are weak.
***********************************************************************/
$sql = "SELECT
			dumpsec_user_table.`password`
		FROM
			dumpsec_user_table
		WHERE
			dumpsec_user_table.Agency = ? AND
			dumpsec_user_table.`Host` = ? AND
			dumpsec_user_table.FileDate = ? AND
			dumpsec_user_table.FileName = ? AND
			dumpsec_user_table.AcctDisabled = 'No' AND
			dumpsec_user_table.`password` IS NOT NULL
		";
$data = array($agency, $Host, $FileDate, $FileName);
$stmt = $db->prepare($sql);
$stmt->execute($data);
$num_passwords = $stmt->rowCount();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

	$password = $row["password"];
	$season = "/".$w.$i.$n.$t.$e.$r."|".$s.$p.$r.$i.$n.$g."|".$s.$u.$m.$m.$e.$r."|".$f.$a.$l.$l."|".$a.$u.$t.$u.$m.$n."/";
		$winter = "/".$w.$i.$n.$t.$e.$r."/";
		$spring = "/".$s.$p.$r.$i.$n.$g."/";
		$summer = "/".$s.$u.$m.$m.$e.$r."/";
		$fall_autumn = "/".$f.$a.$l.$l."|".$a.$u.$t.$u.$m.$n."/";
	$weekday = "/".$s.$u.$n.$d.$a.$y."|".$m.$o.$n.$d.$a.$y."|".$t.$u.$e.$s.$d.$a.$y."|".$w.$e.$d.$n.$e.$s.$d.$a.$y."|".$t.$h.$u.$r.$s.$d.$a.$y."|".$f.$r.$i.$d.$a.$y."|".$s.$a.$t.$u.$r.$d.$a.$y."/";
		$sunday = "/".$s.$u.$n.$d.$a.$y."/";
		$monday = "/".$m.$o.$n.$d.$a.$y."/";
		$tuesday = "/".$t.$u.$e.$s.$d.$a.$y."/";
		$wednesday = "/".$w.$e.$d.$n.$e.$s.$d.$a.$y."/";
		$thursday = "/".$t.$h.$u.$r.$s.$d.$a.$y."/";
		$friday = "/".$f.$r.$i.$d.$a.$y."/";
		$saturday = "/".$s.$a.$t.$u.$r.$d.$a.$y."/";	
	$month = "/".$j.$a.$n.$u.$a.$r.$y."|".$f.$e.$b.$r.$u.$a.$r.$y."|".$m.$a.$r.$c.$h."|".$a.$p.$r.$i.$l."|".$m.$a.$y."|".$j.$u.$n.$e."|".$j.$u.$l.$y."|".$a.$u.$g.$u.$s.$t."|".$s.$e.$p.$t.$e.$m.$b.$e.$r."|".$o.$c.$t.$o.$b.$e.$r."|".$n.$o.$v.$e.$m.$b.$e.$r."|".$d.$e.$c.$e.$m.$b.$e.$r."/";
		$january = "/".$j.$a.$n.$u.$a.$r.$y."/";
		$february = "/".$f.$e.$b.$r.$u.$a.$r.$y."/";
		$march = "/".$m.$a.$r.$c.$h."/";
		$april = "/".$a.$p.$r.$i.$l."/";
		$may = "/".$m.$a.$y."/";
		$june = "/".$j.$u.$n.$e."/";
		$july = "/".$j.$u.$l.$y."/";
		$august = "/".$a.$u.$g.$u.$s.$t."/";
		$september = "/".$s.$e.$p.$t.$e.$m.$b.$e.$r."/";
		$october = "/".$o.$c.$t.$o.$b.$e.$r."/";
		$november = "/".$n.$o.$v.$e.$m.$b.$e.$r."/";
		$december = "/".$d.$e.$c.$e.$m.$b.$e.$r."/";
	$default_password = "/".$p.$a.$s.$s.$w.$o.$r.$d."|".$t.$e.$s.$t."|".$w.$e.$l.$c.$o.$m.$e."|".$a.$c.$t.$i.$v.$e."/";
	//$company_name = "/".$w.$a.$b.$b.$e.$r."|".$c.$a.$t.$a.$l.$i.$n.$a."/";
	//$company_name = "/".$c.$r.$o.$c.$s."/";
	//$company_name = "/".$c.$h.$e.$e.$s.$e.$c.$a.$k.$e."|".$c.$h.$e.$e.$s.$e."/";
	$company_name = "/".$b.$r.$i.$d.$g.$e.$p.$o.$i.$n.$t."|".$a.$s.$h.$f.$o.$r.$d."/";
	
	// LA - $team = "/".$a.$n.$g.$e.$l."|".$l.$a.$k.$e.$r."|".$c.$l.$i.$p.$p.$e.$r."|".$d.$o.$d.$g.$e.$r."/";
	// TORONTO - $team = "/".$m.$a.$p.$l.$e."|".$l.$e.$a.$f."|".$t.$o.$r.$o.$n.$t.$o."|".$r.$a.$p.$t.$o.$r."|".$b.$l.$u.$e.$j.$a.$y."/";
	$team = "/".$b.$r.$o.$n.$c.$o."|".$r.$o.$c.$k.$i.$e.$s."|".$n.$u.$g.$g.$e.$t."|".$a.$v.$a.$l.$a.$n.$c.$h.$e."|".$c.$o.$l.$o.$r.$a.$d.$o."|".$d.$e.$n.$v.$e.$r."/";
	

	switch ($password) {
		case preg_match($sunday, $password) ? $password : !$password:
			$weak_password_stats["weekday"][0]++;
			$weak_password_stats["weekday"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["weekday"]["thePasswords"], $password);
			break;
		case preg_match($monday, $password) ? $password : !$password:
			$weak_password_stats["weekday"][1]++;
			$weak_password_stats["weekday"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["weekday"]["thePasswords"], $password);
			break;
		case preg_match($tuesday, $password) ? $password : !$password:
			$weak_password_stats["weekday"][2]++;
			$weak_password_stats["weekday"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["weekday"]["thePasswords"], $password);
			break;
		case preg_match($wednesday, $password) ? $password : !$password:
			$weak_password_stats["weekday"][3]++;
			$weak_password_stats["weekday"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["weekday"]["thePasswords"], $password);
			break;
		case preg_match($thursday, $password) ? $password : !$password:
			$weak_password_stats["weekday"][4]++;
			$weak_password_stats["weekday"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["weekday"]["thePasswords"], $password);
			break;
		case preg_match($friday, $password) ? $password : !$password:
			$weak_password_stats["weekday"][5]++;
			$weak_password_stats["weekday"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["weekday"]["thePasswords"], $password);
			break;
		case preg_match($saturday, $password) ? $password : !$password:
			$weak_password_stats["weekday"][6]++;
			$weak_password_stats["weekday"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["weekday"]["thePasswords"], $password);
			break;
		case preg_match($winter, $password) ? $password : !$password:
			$weak_password_stats["season"]["winter"]++;
			$weak_password_stats["season"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["season"]["thePasswords"], $password);
			break;
		case preg_match($spring, $password) ? $password : !$password:
			$weak_password_stats["season"]["spring"]++;
			$weak_password_stats["season"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["season"]["thePasswords"], $password);
			break;
		case preg_match($summer, $password) ? $password : !$password:
			$weak_password_stats["season"]["summer"]++;
			$weak_password_stats["season"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["season"]["thePasswords"], $password);
			break;
		case preg_match($fall_autumn, $password) ? $password : !$password:
			$weak_password_stats["season"]["fall_autumn"]++;
			$weak_password_stats["season"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["season"]["thePasswords"], $password);
			break;
		case preg_match($january, $password) ? $password : !$password:
			$weak_password_stats["month"][1]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($february, $password) ? $password : !$password:
			$weak_password_stats["month"][2]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($march, $password) ? $password : !$password:
			$weak_password_stats["month"][3]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($april, $password) ? $password : !$password:
			$weak_password_stats["month"][4]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($may, $password) ? $password : !$password:
			$weak_password_stats["month"][5]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($june, $password) ? $password : !$password:
			$weak_password_stats["month"][6]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($july, $password) ? $password : !$password:
			$weak_password_stats["month"][7]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($august, $password) ? $password : !$password:
			$weak_password_stats["month"][8]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($september, $password) ? $password : !$password:
			$weak_password_stats["month"][9]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($october, $password) ? $password : !$password:
			$weak_password_stats["month"][10]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($november, $password) ? $password : !$password:
			$weak_password_stats["month"][11]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($december, $password) ? $password : !$password:
			$weak_password_stats["month"][12]++;
			$weak_password_stats["month"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["month"]["thePasswords"], $password);
			break;
		case preg_match($default_password, $password) ? $password : !$password:
			$weak_password_stats["default"]["password"]++;
			$weak_password_stats["default"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["default"]["thePasswords"], $password);
			break;
		//case preg_match($welcome, $password) ? $password : !$password:
		//	$weak_password_stats["weak"]["welcome"]++;
		//	$weak_password_stats["weak"]["total"]++;
		//	$weak_password_stats["total"]++;
		//	array_push($weak_password_stats["weak"]["thePasswords"], $password);
		//	break;
		case preg_match($company_name, $password) ? $password : !$password:
			$weak_password_stats["company"]["company_name"]++;
			$weak_password_stats["company"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["company"]["thePasswords"], $password);
			break;
		case preg_match($team, $password) ? $password : !$password:
			$weak_password_stats["other"]["team"]++;
			$weak_password_stats["other"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["other"]["thePasswords"], $password);
			break;
		case preg_match($other, $password) ? $password : !$password:
			$weak_password_stats["other"]["other"]++;
			$weak_password_stats["other"]["total"]++;
			$weak_password_stats["total"]++;
			array_push($weak_password_stats["other"]["thePasswords"], $password);
			break;
	}
}






$percentage_no_password_expire = round((float)$no_expire_count/$total_users * 100 ) . '%';
$percentage_weak_password_stats_1 = round((float)$weak_password_stats["total"]/$num_passwords * 100 ) . '%';
$percentage_weak_password_stats_2 = round((float)$weak_password_stats["total"]/$total_users * 100 ) . '%';
$percentage_password_obtained = round((float)$num_passwords/$total_users * 100 ) . '%';
$percentage_not_compliant_1 = round((float)$password_complexity_stats["total_non"]/$num_passwords * 100 ) . '%';
$percentage_not_compliant_2 = round((float)$password_complexity_stats["total_non"]/$total_users * 100 ) . '%';
$percentage_90_days = round((float)$password_age_stats["90"]/$total_users * 100 ) . '%';
$percentage_365_days = round((float)$password_age_stats["365"]/$total_users * 100 ) . '%';
$percentage_730_days = round((float)$password_age_stats["730"]/$total_users * 100 ) . '%';
$percentage_1095_days = round((float)$password_age_stats["1095"]/$total_users * 100 ) . '%';
$percentage_1460_days = round((float)$password_age_stats["1460"]/$total_users * 100 ) . '%';
$percentage_1825_days = round((float)$password_age_stats["1825"]/$total_users * 100 ) . '%';
$percentage_2190_days = round((float)$password_age_stats["2190"]/$total_users * 100 ) . '%';
$percentage_2555_days = round((float)$password_age_stats["2555"]/$total_users * 100 ) . '%';
$percentage_2920_days = round((float)$password_age_stats["2920"]/$total_users * 100 ) . '%';
$percentage_3285_days = round((float)$password_age_stats["3285"]/$total_users * 100 ) . '%';
$percentage_3650_days = round((float)$password_age_stats["3650"]/$total_users * 100 ) . '%';


echo "Total Passwords:  " .$num_passwords. "<br>";
echo "Total Hashes:  " .$total_hashes. "<br>";
echo "Total Users:  " .$total_users. "<br>";
echo "<hr>";
echo $no_expire_count."/".$total_users." (".$percentage_no_password_expire.") accounts that have no password expiration.<br>";
echo $password_age_stats["90"]."/".$total_users." (".$percentage_90_days.") accounts have a password older than 90 days.<br>";
echo $password_age_stats["365"]."/".$total_users." (".$percentage_365_days.") accounts have a password older than one (1) year.<br>";
echo $password_age_stats["730"]."/".$total_users." (".$percentage_730_days.") accounts have a password older than two (2) years.<br>";
echo $password_age_stats["1095"]."/".$total_users." (".$percentage_1095_days.") accounts have a password older than three (3) years.<br>";
echo $password_age_stats["1460"]."/".$total_users." (".$percentage_1460_days.") accounts have a password older than four (4) year.<br>";
echo $password_age_stats["1825"]."/".$total_users." (".$percentage_1825_days.") accounts have a password older than five (5) years.<br>";
echo $password_age_stats["2190"]."/".$total_users." (".$percentage_2190_days.") accounts have a password older than six (6) years.<br>";
echo $password_age_stats["2555"]."/".$total_users." (".$percentage_2555_days.") accounts have a password older than seven (7) year.<br>";
echo $password_age_stats["2920"]."/".$total_users." (".$percentage_2920_days.") accounts have a password older than eight (8) years.<br>";
echo $password_age_stats["3285"]."/".$total_users." (".$percentage_3285_days.") accounts have a password older than nine (9) years.<br>";
echo $password_age_stats["3650"]."/".$total_users." (".$percentage_3650_days.") accounts have a password older than ten (10) years.<br>";

echo "<hr>";
echo $password_complexity_stats["total_non"]."/".$num_passwords." (".$percentage_not_compliant_1.") of the obtained passwords did not meet complexity requirements.<br>";
echo $weak_password_stats["total"]."/".$num_passwords." (".$percentage_weak_password_stats_1.") of the obtained passwords use the season, weekday, month, \"password\", \"welcome\", local team, username in the password, or company name.<br>";

echo $password_complexity_stats["total_non"]."/".$total_users." (".$percentage_not_compliant_2.") of the accounts have passwords that do not meet complexity requirements.<br>";
echo $weak_password_stats["total"]."/".$total_users." (".$percentage_weak_password_stats_2.") of the accounts use the season, weekday, month, \"password\", \"welcome\", company name, or the username as a basis for the password<br>";
echo $num_passwords."/".$total_users." (".$percentage_password_obtained.") passwords were obtained.<br>";
echo "<hr>";
echo "<h1>Weak Password List</h1>";
echo "<table border=1 cellspacing=0 cellpadding=0>";
	echo "<tr><td valign=bottom><p>Seasons</p></td><td valign=bottom><p>Weekdays</p></td><td valign=bottom><p>Months</p></td><td valign=bottom><p>Weak Passwords<br>\"password\"<br>\"welcome\"<br>\"username\"</p></td><td valign=bottom><p>\"Other\"<br>\"company\"<br>\"local team\"<br>\"wabber\"</p></td><td valign=bottom><p>Company Name</p></td><td valign=bottom><p>Failed Complexity Check</p></td></tr>";
	echo "<tr>";
	echo "<td valign=top><p>";
		sort($weak_password_stats["season"]["thePasswords"]);
		foreach($weak_password_stats["season"]["thePasswords"] as $seasonTEMP){
			echo "$seasonTEMP<br>";
		}	
	echo "</p></td>";
	echo "<td valign=top><p>";
		sort($weak_password_stats["weekday"]["thePasswords"]);
		foreach($weak_password_stats["weekday"]["thePasswords"] as $weekdayTEMP){
			echo "$weekdayTEMP<br>";
		}		
	echo "</p></td>";
	echo "<td valign=top><p>";
		sort($weak_password_stats["month"]["thePasswords"]);
		foreach($weak_password_stats["month"]["thePasswords"] as $monthTEMP){
			echo "$monthTEMP<br>";
		}		
	echo "</p></td>";
	echo "<td valign=top><p>";
		sort($weak_password_stats["default"]["thePasswords"]);
		foreach($weak_password_stats["default"]["thePasswords"] as $defaultTEMP){
			echo "$defaultTEMP<br>";
		}		
	echo "</p></td>";
	echo "<td valign=top><p>";
		sort($weak_password_stats["other"]["thePasswords"]);
		foreach($weak_password_stats["other"]["thePasswords"] as $specialTEMP){
			echo "$specialTEMP<br>";
		}		
	echo "</p></td>";
	echo "<td valign=top><p>";
		sort($weak_password_stats["company"]["thePasswords"]);
		foreach($weak_password_stats["company"]["thePasswords"] as $specialTEMP){
			echo "$specialTEMP<br>";
		}		
	echo "</p></td>";
	echo "<td valign=top><p>";
		sort($password_complexity_stats["thePasswords"]);
		foreach($password_complexity_stats["thePasswords"] as $complexTEMP){
			echo "$complexTEMP<br>";
		}		
	echo "</p></td>";
echo "</tr>";
echo "<tr><td><p>". $weak_password_stats["season"]["total"] ."</p></td>";
echo "<td><p>". $weak_password_stats["weekday"]["total"] ."</p></td>";
echo "<td><p>". $weak_password_stats["month"]["total"] ."</p></td>";
echo "<td><p>". $weak_password_stats["default"]["total"] ."</p></td>";
echo "<td><p>". $weak_password_stats["other"]["total"] ."</p></td>";
echo "<td><p>". $weak_password_stats["company"]["total"] ."</p></td>";
echo "<td><p>". $password_complexity_stats["total_non"] ."</p></td></tr>";
echo "</table>";



/********************************************************************
  Domain Administrator Statistics
***********************************************************************/
date_default_timezone_set('UTC');
$myDir = getcwd() . "/csvfiles/";
$myFileName_domainadmin = $agency . "_DomainAdmin_" . date('mdYHis') . ".csv";
$myFile_domainadmin = $myDir . $myFileName_domainadmin;
$fh_domainadmin = fopen($myFile_domainadmin, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");

$myFileName_duplicatepass = $agency . "_DuplicatePass_" . date('mdYHis') . ".csv";
$myFile_duplicatepass = $myDir . $myFileName_duplicatepass;
$fh_duplicatepass = fopen($myFile_duplicatepass, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");

$da_sql = "SELECT
				dumpsec_user_table.UserName,
				dumpsec_user_table.FullName,
				dumpsec_user_table.`Comment` AS UserComment,
				dumpsec_group_table.GroupName,
				dumpsec_group_table.`Comment` AS GroupComment,
				dumpsec_user_table.PswdExpires,
				dumpsec_user_table.PasswordAgeDays,
				dumpsec_user_table.LastLogonAgeDays,
				dumpsec_user_table.NTLMHash,
				dumpsec_user_table.`password`
			FROM
				dumpsec_group_table
			INNER JOIN dumpsec_user_table ON dumpsec_group_table.GroupMember = dumpsec_user_table.UserName
			WHERE
				dumpsec_user_table.Agency = ? AND
				dumpsec_user_table.`Host` = ? AND
				dumpsec_user_table.FileDate = ? AND
				dumpsec_user_table.FileName = ? AND
				dumpsec_group_table.GroupName LIKE '%Domain Admins%' AND
				dumpsec_user_table.AcctDisabled = 'No'
";
$data = array($agency, $Host, $FileDate, $FileName);
$da_stmt = $db->prepare($da_sql);
$da_stmt->execute($data);

echo "<hr>";
echo "<h1>Domain Administrators</h1>";
echo "<table border=1 cellspacing=0 cellpadding=0>";
	echo "<tr><td valign=bottom><p>UserName</p></td><td valign=bottom><p>FullName</p></td><td valign=bottom><p>UserComment</p></td><td valign=bottom><p>GroupName</p></td><td valign=bottom><p>GroupComment</p></td><td valign=bottom><p>Password Expires</p></td><td valign=bottom><p>Password Age (days)</p></td><td valign=bottom><p>NTLM</p></td><td valign=bottom><p>Password</p></td></tr>";

$header = array("Username", "FullName", "UserComment", "GroupName", "GroupComment", "Password Expires", "Password Age (Days)", "NTLM", "Password");
fputcsv($fh_domainadmin, $header);

while($da_row = $da_stmt->fetch(PDO::FETCH_ASSOC)){

	$UserName = $da_row["UserName"];
	$FullName = $da_row["FullName"];
	$UserComment = $da_row["UserComment"];
	$GroupName = $da_row["GroupName"];
	$GroupComment = $da_row["GroupComment"];
	$PswdExpires = $da_row["PswdExpires"];
	$PasswordAgeDays = $da_row["PasswordAgeDays"];
	$LastLogonAgeDays = $da_row["LastLogonAgeDays"];
	$NTLMHash = $da_row["NTLMHash"];
	$password = $da_row["password"];

	echo "<tr><td><p>".$UserName."</p></td><td><p>".$FullName."</p></td><td><p>".$UserComment."</p></td><td><p>".$GroupName."</p></td><td><p>".$GroupComment."</p></td><td><p>".$PswdExpires."</p></td><td><p>".$PasswordAgeDays."</p></td><td><p>".$NTLMHash."</p></td><td><p>".$password."</p></td></tr>";
	$csv_data_row = array($UserName, $FullName, $UserComment, $GroupName, $GroupComment, $PswdExpires, $PasswordAgeDays, $NTLMHash, $password);
	fputcsv($fh_domainadmin, $csv_data_row);
	$match_hash_sql = "SELECT DISTINCT
							dumpsec_user_table.UserName,
							dumpsec_user_table.FullName,
							dumpsec_user_table.`Comment` AS UserComment,
							dumpsec_user_table.PswdExpires,
							dumpsec_user_table.PasswordAgeDays,
							dumpsec_user_table.LastLogonAgeDays,
							dumpsec_user_table.NTLMHash,
							dumpsec_user_table.`password`
						FROM
							dumpsec_user_table
						WHERE
							dumpsec_user_table.Agency = ? AND
							dumpsec_user_table.`Host` = ? AND
							dumpsec_user_table.FileDate = ? AND
							dumpsec_user_table.FileName = ? AND
							dumpsec_user_table.AcctDisabled = 'No' AND
							dumpsec_user_table.NTLMHash = ? AND
							dumpsec_user_table.UserName != ?
	";
	$match_hash_data = array($agency, $Host, $FileDate, $FileName, $NTLMHash, $UserName);
	$match_hash_stmt = $db->prepare($match_hash_sql);
	$match_hash_stmt->execute($match_hash_data);
	while($match_hash_row = $match_hash_stmt->fetch(PDO::FETCH_ASSOC)){
	
		$UserName = $match_hash_row["UserName"];
		$FullName = $match_hash_row["FullName"];
		$UserComment = $match_hash_row["UserComment"];
		$PswdExpires = $match_hash_row["PswdExpires"];
		$PasswordAgeDays = $match_hash_row["PasswordAgeDays"];
		$LastLogonAgeDays = $match_hash_row["LastLogonAgeDays"];
		$NTLMHash = $match_hash_row["NTLMHash"];
		$password = $match_hash_row["password"];	
		
		echo "<tr><td><p>".$UserName."</p></td><td><p>".$FullName."</p></td><td><p>".$UserComment."</p></td><td><p></p></td><td><p></p></td><td><p>".$PswdExpires."</p></td><td><p>".$PasswordAgeDays."</p></td><td><p>".$NTLMHash."</p></td><td><p>".$password."</p></td></tr>";
		$csv_data_row = array($UserName, $FullName, $UserComment, "", "", $PswdExpires, $PasswordAgeDays, $NTLMHash, $password);
		fputcsv($fh_domainadmin, $csv_data_row);
	}



}
echo "</table>";
echo "	<hr>";
echo "		<p align=\"center\"><a href=\"csvfiles/$myFileName_domainadmin\">Click Here</a> to download the CSV file.</p>";






echo "<hr>";
echo "<h1>Dumplicate Passwords</h1>";
echo "<table border=1 cellspacing=0 cellpadding=0>";
	echo "<tr><td valign=bottom><p>UserName</p></td><td valign=bottom><p>FullName</p></td><td valign=bottom><p>UserComment</p></td><td valign=bottom><p>NTLM</p></td><td valign=bottom><p>Password</p></td></tr>";

$header = array("Username", "FullName", "UserComment", "NTLM", "Password");
fputcsv($fh_duplicatepass, $header);

$duplicate_pass_sql = "SELECT DISTINCT
						dumpsec_user_table.`password`,
						dumpsec_user_table.NTLMHash,
						COUNT(*) c
					FROM
						dumpsec_user_table
					WHERE
						dumpsec_user_table.Agency = ? AND
						dumpsec_user_table.`Host` = ? AND
						dumpsec_user_table.FileDate = ? AND
						dumpsec_user_table.FileName = ? AND
						dumpsec_user_table.AcctDisabled = 'No'
					GROUP BY NTLMHash HAVING c > 1
					ORDER BY c DESC
				";
$data = array($agency, $Host, $FileDate, $FileName);
$duplicate_pass_stmt = $db->prepare($duplicate_pass_sql);
$duplicate_pass_stmt->execute($data);
while($duplicate_pass_row = $duplicate_pass_stmt->fetch(PDO::FETCH_ASSOC)){

	$password = $duplicate_pass_row["password"];
	$NTLMHash = $duplicate_pass_row["NTLMHash"];
	$get_user_names_sql = "SELECT
							dumpsec_user_table.UserName,
							dumpsec_user_table.FullName,
							dumpsec_user_table.`Comment` AS UserComment
						FROM
							dumpsec_user_table
						WHERE
							dumpsec_user_table.Agency = ? AND
							dumpsec_user_table.`Host` = ? AND
							dumpsec_user_table.FileDate = ? AND
							dumpsec_user_table.FileName = ? AND
							dumpsec_user_table.AcctDisabled = 'No' AND
							dumpsec_user_table.NTLMHash = ?
						";
						
						
	$data = array($agency, $Host, $FileDate, $FileName, $NTLMHash);
	$get_user_names_stmt = $db->prepare($get_user_names_sql);
	$get_user_names_stmt->execute($data);
	while($get_user_names_row = $get_user_names_stmt->fetch(PDO::FETCH_ASSOC)){
	
		$UserName = $get_user_names_row["UserName"];
		$FullName = $get_user_names_row["FullName"];
		$UserComment = $get_user_names_row["UserComment"];
		
		echo "<tr><td><p>".$UserName."</p></td><td><p>".$FullName."</p></td><td><p>".$UserComment."</p></td><td><p>".$NTLMHash."</p></td><td><p>".$password."</p></td></tr>";
		$csv_data_row = array($UserName, $FullName, $UserComment, $NTLMHash, $password);
		fputcsv($fh_duplicatepass, $csv_data_row);
		
	}
	
}

echo "</table>";
echo "	<hr>";
echo "		<p align=\"center\"><a href=\"csvfiles/$myFileName_duplicatepass\">Click Here</a> to download the CSV file.</p>";

?>
	</td>
</tr></table>
</body>
</html>



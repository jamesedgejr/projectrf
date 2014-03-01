<?php
$agency = $_POST["agency"];
$host_application = $_POST["host_application"];

$isSeverity = $_POST["isSeverity"];
$isSummary = $_POST["isSummary"];
$isImplication = $_POST["isImplication"];
$isExecution = $_POST["isExecution"];
$isFix = $_POST["isFix"];
$isReferenceinfo = $_POST["isReferenceinfo"];
$isUrl = $_POST["isUrl"];
$isNotes = $_POST["isNotes"];

$critical = $_POST["critical"];
$high = $_POST["high"];
$medium = $_POST["medium"];
$low  = $_POST["low"];

$isTest = $_POST["isTest"];
$isStyle = $_POST["isStyle"];
$sw = $critical.$high.$medium.$low;
switch ($sw) {
	case "4":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '4')";
		break;
	case "43":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '4' OR `webinspect_xml`.`severity` = '3')";
		break;
	case "432":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '4' OR `webinspect_xml`.`severity` = '3' OR `webinspect_xml`.`severity` = '2')";
		break;
	case "4321":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '4' OR `webinspect_xml`.`severity` = '3' OR `webinspect_xml`.`severity` = '2' OR `webinspect_xml`.`severity` = '1')";
		break;
	case "431":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '4' OR `webinspect_xml`.`severity` = '3' OR `webinspect_xml`.`severity` = '1')";
		break;
	case "421":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '4' OR `webinspect_xml`.`severity` = '2' OR `webinspect_xml`.`severity` = '1')";
		break;
	case "42":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '4' OR `webinspect_xml`.`severity` = '2')";
		break;
	case "41":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '4' OR `webinspect_xml`.`severity` = '1')";
		break;
	case "321":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '3' OR `webinspect_xml`.`severity` = '2' OR `webinspect_xml`.`severity` = '1')";
		break;
	case "32":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '3' OR `webinspect_xml`.`severity` = '2')";
		break;
	case "31":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '3' OR `webinspect_xml`.`severity` = '1')";
		break;
	case "3":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '3')";
		break;
	case "21":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '2' OR `webinspect_xml`.`severity` = '1')";
		break;
	case "2":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '2')";
		break;
	case "1":
		$sqlSeverity = "(`webinspect_xml`.`severity` = '1')";
		break;
	default: /* We shall default to listing all Critical and High Vulnerabilities */
		$sqlSeverity = "(`webinspect_xml`.`severity` = '4' OR `webinspect_xml`.`severity` = '3')";
}



include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );

foreach ($host_application as $h_a){
  $temp_array = explode(":", $h_a);
  $host = $temp_array[0];
  $application = $temp_array[1];

  $port_sql = 	"SELECT DISTINCT `webinspect_xml`.`port` FROM `webinspect_xml` WHERE `webinspect_xml`.`agency` = '$agency' AND `webinspect_xml`.`host` ='$host' AND `webinspect_xml`.`application` = '$application'";
  $port_result = $db->query($port_sql);
  $port_row = $port_result->fetchRow();
  $port = $port_row[0];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title><?php echo "$agency - Risk Report";?></title>
<link rel="stylesheet" type="text/css" href="../main/<?php echo "$isStyle";?>" />
</head>
<body>
<table class="main">
<tr>
  <td class="top"><p>HOST:  <?php echo "$host";?></p></td>
  <td class="top"><p>PORT:  <?php echo "$port";?></p></td>
  <td class="top"><p>APPLICATION:  <?php echo "$application";?></p></td>
</tr>
</table>

<?php
	$vuln_sql = 	"SELECT DISTINCT 
			  `webinspect_xml`.`name`,
			  `webinspect_xml`.`vulnID`,
			  `webinspect_xml`.`severity`,
			  `webinspect_xml`.`summary`,
			  `webinspect_xml`.`implication`,
			  `webinspect_xml`.`execution`,
			  `webinspect_xml`.`fix`,
			  `webinspect_xml`.`referenceinfo`
			FROM
			  `webinspect_xml`
			WHERE
			  $sqlSeverity AND
			  (`webinspect_xml`.`agency` = '$agency') AND
			  (`webinspect_xml`.`host` = '$host') AND 
			  (`webinspect_xml`.`port` = '$port') AND 
			  (`webinspect_xml`.`application` = '$application') AND (`webinspect_xml`.`vulnID` = '5672')
			ORDER BY
			  `webinspect_xml`.`severity` DESC
					";
	$vuln_result = $db->query($vuln_sql);
	while($vuln_row = $vuln_result->fetchRow()){
		$name = $vuln_row[0];
		$vulnID = $vuln_row[1];
		switch ($vuln_row[2]){
		case "4":
			$severity = "Critical";
			break;
		case "3":
			$severity = "High";
			break;
		case "2":
			$severity = "Medium";
			break;
		case "1":
			$severity = "Low";
			break;
		case "0":
			$severity = "Information";
			break;
		}//end switch
		$summary = stripslashes($vuln_row[3]);
		$implication = stripslashes($vuln_row[4]);
		$execution = stripslashes($vuln_row[5]);
		$fix = stripslashes($vuln_row[6]);
		$referenceinfo = stripslashes($vuln_row[7]);
		
		$count = 0;
?>
<table class="main">
  <tr>
	<td class="left"><p>Vulnerability:</p></td>
	<td class="right"><p><?php echo "$name" ?></p></td>
  </tr>
  <tr>
	<td class="left"><p>Vulnerability ID:</p></td>
	<td class="right"><p><?php echo "$vulnID" ?></p></td>
  </tr>

<?php
	if($isSeverity == "y"){
?>
	<tr>
	<td class="left"><p>Severity:</p></td>
	<td class="right"><p><?php echo "$severity" ?></p></td>
 	</tr>
<?php
	}//end if isSeverity
?>
<?php
	if($isSummary == "y"){
?>
	<tr>
	<td class="left"><p>Summary:</p></td>
	<td class="right"><p><?php echo "$summary" ?></p></td>
 	</tr>
<?php
	}//end if isSummary
?>
<?php
	if($isImplication == "y"){
?>
	<tr>
	<td class="left"><p>Implication:</p></td>
	<td class="right"><p><?php echo "$implication" ?></p></td>
 	</tr>
<?php
	}//end if isImplication
?>
<?php
	if($isExecution == "y"){
?>
	<tr>
	<td class="left"><p>Execution:</p></td>
	<td class="right"><p><?php echo "$execution" ?></p></td>
 	</tr>
<?php
	}//end if isExecution
?>
<?php
	if($isFix == "y"){
?>
	<tr>
	<td class="left"><p>Fix:</p></td>
	<td class="right"><p><?php echo "$fix" ?></p></td>
 	</tr>
<?php
	}//end if isFix
?>
<?php
	if($isReferenceinfo == "y"){
?>
	<tr>
	<td class="left"><p>Reference:</p></td>
	<td class="right"><p><?php echo "$referenceinfo" ?></p></td>
 	</tr>
<?php
	}//end if isReferenceinfo
?>

</table>

<?php
if($isUrl == "y"){
?>

<table class="main">
  <tr>
	<td class="top">#</td>
	<td class="top">URL</td>
	<?php if($isTest == "y"){ ?>
		<td class="top">Is Valid</td>
	<?php }?>
  </tr>
<?php
	$url_sql = "SELECT 
		     `webinspect_xml`.`url`, 
		     `webinspect_xml`.`request_fullquery`, 
		     `webinspect_xml`.`request_fullpostdata`, 
		     `webinspect_xml`.`request_cookie`,
		     `webinspect_xml`.`request_method`
		    FROM
		     `webinspect_xml`
		    WHERE
			(`webinspect_xml`.`agency` = '$agency') AND
			(`webinspect_xml`.`application` = '$application') AND
			(`webinspect_xml`.`host` = '$host') AND 
			(`webinspect_xml`.`port` = '$port') AND 
			(`webinspect_xml`.`vulnID` = '$vulnID')
		    ORDER BY `webinspect_xml`.`url_file`, `webinspect_xml`.`url_folder`
		    ";
	$url_result = $db->query($url_sql);
	while($url_row = $url_result->fetchRow()){
		$url = stripslashes($url_row[0]);
		$url_wrap = wordwrap($url,125,"<br />",1);
		$fullquery = stripslashes($url_row[1]);
		$fullquery_wrap = wordwrap($fullquery,125,"<br />",1);
		$fullpostdata = stripslashes($url_row[2]);
		$fullpostdata_wrap = wordwrap($fullpostdata,125,"<br />",1);
		$cookie = stripslashes($url_row[3]);
		$cookie_wrap = wordwrap($cookie,50,"<br />",1);
		
		/*
		$request2 = nl2br($url_row[1]);
		$request_array = explode("\n", $url_row[1]);
		$arraySize = count($request_array);
		$postRequest = $request_array[$arraySize - 1];		
		$postRequest_edit = wordwrap($postRequest,100,"<br />",1);
		*/
		$count++;
?>
  <tr>
	<td class="right"><p><?echo "$count";?></p></td>
	<td class="right">
		<p><?php echo "$url_wrap";?></p>
		<p><?php echo "$fullquery_wrap";?></p>
		<p><?php echo "$fullpostdata_wrap";?></p>
		<p><?php echo "$cookie_wrap";?></p>
	</td>
	<?php if($isTest == "y"){ ?>
	<td class="right">
	<?php
	
		if($url_row[4] == "POST"){
			echo "<form method=post action=\"$url\">";
			$fullpostdata_array = explode("&", $fullpostdata);
			foreach($fullpostdata_array as $fpdA){
				$variables = explode("=", $fpdA);
				echo "<input type=\"hidden\" name=\"$variables[0]\" value=\"$variables[1]\">\n";
			}
			echo "<input type=submit value=submit>";
			echo "</form>";
		}
	?>
	</td>
	<?php }?>
  </tr>
<?php
	}//end url while
?>
</table>

<?php
}//end if isUrl
?>


<?php
if($isNotes == "y"){
?>

	<table class="main">
	  <tr><td class="top"><p>Notes:</p></td></tr>
	  <tr><td class="right"><p><br></p></td></tr>
	</table>

<?php 
}//end if isNotes 
?>

<p><br><br></p>	
<?php
	}//end vuln while
?>
<p><br><br></p>
<?php

}//end foreach
?>


<?php
$agency = $_POST["agency"];
$host_application = $_POST["host_application"];

include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
foreach ($host_application as $h_a){

$temp_array = explode(":", $h_a);
$host = $temp_array[0];
$application = $temp_array[1];
$cat_sql = 	"SELECT DISTINCT 
		  `webinspect_xml`.`severity`, 
		  `webinspect_xml`.`name`, 
		  `webinspect_xml`.`vulnID` 
		FROM 
		  `webinspect_xml` 
		WHERE
		  `webinspect_xml`.`agency` = '$agency' AND
		  `webinspect_xml`.`host` = '$host' AND
		  `webinspect_xml`.`application` = '$application'
		ORDER BY `webinspect_xml`.`severity` DESC
		"; 
$cat_result = $db->query($cat_sql);

?>
<br>
<table border="2" cellpadding="" cellspacing="" align="center" width="600">
<tr>
  <td>
	<p><strong><?php echo "$host";?></strong></p>
  </td>
  <td>
	<p><strong><?php echo "$application";?></strong></p>
  </td>
</tr>
</table>
<br>
<table border="2" cellpadding="" cellspacing="" align="center" width="600">
<tr>
  <td>
	<p><strong>SEVERITY</strong></p>
  </td>
  <td>
	<p><strong>NAME</strong></p>
  </td>
  <td>
	<p><strong>Occurances</strong></p>
  </td>
  <td>
	<p><strong>Validation</strong></p>
  </td>
</tr>
<?php

while($cat_row = $cat_result->fetchRow()){
	switch ($cat_row[0]){
	case "4":
		$severity = "Critical";
		$color = "#FF0000";
		break;
	case "3":
		$severity = "High";
		$color = "#FFA500";
		break;
	case "2":
		$severity = "Medium";
		$color = "#32CD32";
		break;
	case "1":
		$severity = "Low";
		$color = "#00BFFF";
		break;
	case "0":
		$severity = "Information";
		break;
	}//end switch
	$name = $cat_row[1];
	$vulnID = $cat_row[2];

$nist_sql = 	"SELECT DISTINCT `webinspect_80053`.`nist_category` FROM `webinspect_80053` WHERE `webinspect_80053`.`vulnID` = '$vulnID'";
$nist_result = $db->query($nist_sql);

$count_sql = 	"SELECT DISTINCT  
		  `webinspect_xml`.`rawrequest`
		FROM 
		  `webinspect_xml`
		WHERE 
		  `webinspect_xml`.`agency` = '$agency' AND 
		  `webinspect_xml`.`vulnID` = '$vulnID' AND 
		  `webinspect_xml`.`host` = '$host' AND 
		  `webinspect_xml`.`application` = '$application' 
		";
$count_result = $db->query($count_sql);
$total = $count_result->numRows();
?>

<tr>
  <td bgcolor="<?php echo"$color";?>">
	<p><?php echo "$severity";?></p>
  </td>
  <td bgcolor="<?php echo"$color";?>">
	<p>
		<?php 	echo "$name<br>";
			while($nist_row = $nist_result->fetchRow()){ 
				echo "$nist_row[0]<br>";
			}
		?></p>
  </td>
  <td>
	<p><?php echo "$total";?></p>
  </td>
  <td>
	<p></p>
  </td>
</tr>

<?php }?>
</table>
<?php }?>


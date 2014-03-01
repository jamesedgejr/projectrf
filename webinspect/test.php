<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );

$host = "thelmalou.pap.state.ga.us";
$port = "80";
$vulnID = "5649";

$url_sql = "SELECT `webinspect_xml`.`url`, `webinspect_xml`.`request` FROM `webinspect_xml` WHERE (`webinspect_xml`.`host` = '$host') AND (`webinspect_xml`.`port` = '$port') AND (`webinspect_xml`.`vulnID` = '$vulnID')";

$url_result = $db->query($url_sql);
while($url_row = $url_result->fetchRow()){
	$url = $url_row[0];
	//$test = nl2br($url_row[1]);
	//echo "<hr>$test<hr>";
	$request_array = explode("\n", $url_row[1]);
	$arraySize = count($request_array);
	$postRequest = $request_array[$arraySize - 1];
	
	if(preg_match("/POST/", $request_array[0])){
		echo "<form method=post action=\"$url\">";
		$postRequest_array = explode("&", $postRequest);
		foreach($postRequest_array as $pR){
			$variables = explode("=", $pR);
			echo "<input type=\"hidden\" name=\"$variables[0]\" value=\"$variables[1]\">\n";
		}
		echo "<input type=submit value=submit>";
		echo "</form>";
	}
?>

<?php
}//end url while
?>

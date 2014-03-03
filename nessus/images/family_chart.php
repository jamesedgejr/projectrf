<?php
include('../../main/config.php');
include("../../pChart/class/pData.class.php");
include("../../pChart/class/pDraw.class.php");
include("../../pChart/class/pImage.class.php");
require_once( 'DB.php' );

$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
ifError($db);
$agency = $_GET["agency"];
$report_name = $_GET["report_name"];
$scan_start = $_GET["scan_start"];
$scan_end = $_GET["scan_end"];
$byVuln = $_GET["byVuln"];

$fam_sql = "SELECT
	nessus_results.pluginFamily,
	Count(nessus_results.severity) AS sevCount
FROM
	nessus_results
INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
WHERE
	nessus_results.agency = '$agency' AND 
	nessus_results.report_name = '$report_name' AND
	nessus_results.scan_start = '$scan_start' AND
	nessus_results.scan_end = '$scan_end' AND
	nessus_results.pluginID != '0' AND 	
	nessus_results.severity != '0'
GROUP BY
	nessus_results.pluginFamily
ORDER BY
	sevCount DESC
LIMIT 0, 3
";
$fam_result = $db->query($fam_sql);ifError($fam_result);
while ($fam_row = $fam_result->fetchRow(DB_FETCHMODE_ASSOC)){
	$pluginFamily = $fam_row["pluginFamily"];
	$exec_fam[$pluginFamily] = array(critical => "0", high => "0", medium => "0", low => "0");
	
	$sql = "SELECT
		nessus_results.pluginFamily,
		nessus_results.severity,
		nessus_results.cveList
	FROM
		nessus_results
	INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
	INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_tags.host_name
	INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
	WHERE
		nessus_results.agency = '$agency' AND 
		nessus_results.report_name = '$report_name' AND
		nessus_results.scan_start = '$scan_start' AND
		nessus_results.scan_end = '$scan_end' AND
		nessus_results.pluginFamily = '$pluginFamily'
	";
	$result = $db->query($sql);ifError($result);
	while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$severity = $row["severity"];
		$pluginFamily = $row["pluginFamily"];
		//$famArray[]  = preg_replace($patterns, $replacements, $fam_row["pluginFamily"]);
		$cveList = explode(",", $row["cveList"]);
		$cveCount = count($cveList) - 1;
		if($byVuln == "plugin"){
			switch ($severity) {
				case "4":
					$exec_fam[$pluginFamily]["critical"]++;
					break;
				case "3":
					$exec_fam[$pluginFamily]["high"]++;
					break;
				case "2":
					$exec_fam[$pluginFamily]["medium"]++;
					break;
				case "1":
					$exec_fam[$pluginFamily]["low"]++;
					break;
			}
		}
		if($byVuln == "cve"){
			switch ($severity) {
				case "4":
					$exec_fam[$pluginFamily]["critical"]=$exec_fam[$pluginFamily]["critical"]+$cveCount;
					break;
				case "3":
					$exec_fam[$pluginFamily]["high"]=$exec_fam[$pluginFamily]["high"]+$cveCount;
					break;
				case "2":
					$exec_fam[$pluginFamily]["medium"]=$exec_fam[$pluginFamily]["medium"]+$cveCount;
					break;
				case "1":
					$exec_fam[$pluginFamily]["low"]=$exec_fam[$pluginFamily]["low"]+$cveCount;
					break;
			}
		}
	}
}
/*
$fam_sql = "SELECT
	nessus_results.pluginFamily,
	Count(nessus_results.severity) AS sevCount
FROM
	nessus_results
INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_results.host_name
INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
WHERE
	nessus_results.agency = '$agency' AND 
	nessus_results.report_name = '$report_name' AND
	nessus_results.scan_start = '$scan_start' AND
	nessus_results.scan_end = '$scan_end' AND
	nessus_results.pluginID != '0' AND
	nessus_results.severity != '0'
GROUP BY
	nessus_results.pluginFamily
ORDER BY
	sevCount DESC
LIMIT 0, 3
";
$fam_result = $db->query($fam_sql);
ifError($fam_result);
while ($fam_row = $fam_result->fetchRow(DB_FETCHMODE_ASSOC)){
	$pluginFamily = $fam_row["pluginFamily"];
	$famArray[]  = preg_replace($patterns, $replacements, $fam_row["pluginFamily"]);
	$sql = "SELECT
		nessus_results.severity,
		COUNT(*) AS sevCount
	FROM
		nessus_results
	INNER JOIN nessus_tmp_hosts ON nessus_tmp_hosts.host_name = nessus_results.host_name
	INNER JOIN nessus_tmp_family ON nessus_tmp_family.pluginFamily = nessus_results.pluginFamily
	WHERE
		nessus_results.agency = '$agency' AND 
		nessus_results.report_name = '$report_name' AND
		nessus_results.scan_start = '$scan_start' AND
		nessus_results.scan_end = '$scan_end' AND
		nessus_results.pluginFamily = '$pluginFamily'
	GROUP BY nessus_results.severity
	";
	$result = $db->query($sql);
	ifError($result);
	while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		switch ($row["severity"]) {
			case "4":
				$criticalArray[] = $row["sevCount"];
				break;
			case "3":
				$highArray[] = $row["sevCount"];
				break;
			case "2":
				$mediumArray[] = $row["sevCount"];
				break;
			case "1":
				$lowArray[] = $row["sevCount"];
				break;
		}
	}

}
*/

uasort($exec_fam, 'sortByHigh');

$highArray = array();
$mediumArray = array();
$lowArray = array();
$osArray = array();
$patterns = array();
$patterns[0] = '/Microsoft/i';
$patterns[1] = '/Service Pack/i';
$patterns[2] = '/Windows/i';
$patterns[3] = '/Linux/i';
$patterns[4] = '/Enterprise/i';
$patterns[5] = '/Standard/i';
$patterns[6] = '/2003/i';
$patterns[7] = '/2008/i';
$patterns[7] = '/\(English\)/i';
$patterns[9] = '/\([a-zA-Z]+\)/';
$replacements = array();
$replacements[0] = 'MS';
$replacements[1] = 'SP';
$replacements[2] = 'Win';
$replacements[3] = 'Lnx';
$replacements[4] = 'Ent';
$replacements[5] = 'Std';
$replacements[6] = '03';
$replacements[7] = '08';
$replacements[8] = '';
$replacements[9] = '';
foreach ($exec_fam as $key1 => $value1){
	$criticalArray[] = $value1["critical"];
	$highArray[] = $value1["high"];
	$mediumArray[] = $value1["medium"];
	$lowArray[] = $value1["low"];
	$key1 = preg_replace($patterns, $replacements, $key1);
	$famArray[] = $key1;
}


$myData = new pData();
$myData->loadPalette("../../pChart/palettes/nessus.color",TRUE);
$myData->addPoints($criticalArray,"Serie1");
$myData->setSerieDescription("Serie1","Critical");
$myData->setSerieOnAxis("Serie1",0);

$myData->addPoints($highArray,"Serie2");
$myData->setSerieDescription("Serie2","High");
$myData->setSerieOnAxis("Serie2",0);

$myData->addPoints($mediumArray,"Serie3");
$myData->setSerieDescription("Serie3","Medium");
$myData->setSerieOnAxis("Serie3",0);

$myData->addPoints($lowArray,"Serie4");
$myData->setSerieDescription("Serie4","Low");
$myData->setSerieOnAxis("Serie4",0);

$myData->addPoints($famArray,"Absissa");
$myData->setAbscissa("Absissa");

$myData->setAxisPosition(0,AXIS_POSITION_LEFT);
$myData->setAxisName(0,"");
$myData->setAxisUnit(0,"");

$myPicture = new pImage(700,230,$myData);
$Settings = array("R"=>220, "G"=>220, "B"=>220);
$myPicture->drawFilledRectangle(0,0,700,230,$Settings);

$myPicture->drawRectangle(0,0,799,249,array("R"=>162,"G"=>181,"B"=>205));

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20));

$myPicture->setFontProperties(array("FontName"=>"../../pChart/fonts/Forgotte.ttf","FontSize"=>14));
$TextSettings = array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE
, "R"=>0, "G"=>0, "B"=>0);
$myPicture->drawText(350,25,"Vulnerability Distribution (by Family)",$TextSettings);

$myPicture->setShadow(FALSE);
$myPicture->setGraphArea(100,70,675,210);
$myPicture->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>"../../pChart/fonts/pf_arma_five.ttf","FontSize"=>6));

$Settings = array("Pos"=>SCALE_POS_TOPBOTTOM
, "Mode"=>SCALE_MODE_ADDALL_START0
, "LabelingMethod"=>LABELING_ALL
, "GridR"=>255, "GridG"=>255, "GridB"=>255, "GridAlpha"=>50, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>50, "LabelRotation"=>0, "CycleBackground"=>1, "DrawXLines"=>1, "DrawSubTicks"=>1, "SubTickR"=>255, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>50, "DrawYLines"=>ALL);
$myPicture->drawScale($Settings);

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10));

$Config = array("DisplayValues"=>1, "Rounded"=>1, "AroundZero"=>1);
$myPicture->drawStackedBarChart($Config);

$Config = array("FontR"=>0, "FontG"=>0, "FontB"=>0, "FontName"=>"../../pChart/fonts/pf_arma_five.ttf", "FontSize"=>6, "Margin"=>6, "Alpha"=>30, "BoxSize"=>5, "Style"=>LEGEND_NOBORDER
, "Mode"=>LEGEND_HORIZONTAL
);
$myPicture->drawLegend(557,16,$Config);

$myPicture->stroke();

function sortByHigh($a, $b) { 
	return strnatcmp($b['critical'], $a['critical']); 
} // sort alphabetically by name 

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
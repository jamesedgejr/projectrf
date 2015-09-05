<?php
include('../../main/config.php');
include("../../pChart/class/pData.class.php");
include("../../pChart/class/pDraw.class.php");
include("../../pChart/class/pImage.class.php");
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

$v = new Valitron\Validator($_GET);
$v->rule('numeric', ['scan_start', 'scan_end']);
$v->rule('slug','agency');
$v->rule('regex','report_name','/[A-Za-z0-9 _ .-]+/');// validate report name
$v->rule('alpha','byVuln');
if(!$v->validate()) {
    print_r($v->errors());
	exit;
}

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
WHERE
	nessus_results.agency = ? AND 
	nessus_results.report_name = ? AND
	nessus_results.scan_start = ? AND
	nessus_results.scan_end = ? AND
	nessus_results.pluginID != '0' AND 	
	nessus_results.severity != '0'
GROUP BY
	nessus_results.pluginFamily
ORDER BY
	sevCount DESC
LIMIT 0, 3
";
$fam_stmt = $db->prepare($fam_sql);
$fam_stmt->execute(array($agency, $report_name, $scan_start, $scan_end));
while ($fam_row = $fam_stmt->fetch(PDO::FETCH_ASSOC)){
	$pluginFamily = $fam_row["pluginFamily"];
	$exec_fam[$pluginFamily] = array(critical => "0", high => "0", medium => "0", low => "0");
	
	$sql = "SELECT
		nessus_results.pluginFamily,
		nessus_results.severity,
		nessus_results.cveList
	FROM
		nessus_results
	INNER JOIN nessus_tags ON nessus_results.tagID = nessus_tags.tagID
	WHERE
		nessus_results.agency = ? AND 
		nessus_results.report_name = ? AND
		nessus_results.scan_start = ? AND
		nessus_results.scan_end = ? AND
		nessus_results.pluginFamily = ?
	";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($agency, $report_name, $scan_start, $scan_end, $pluginFamily));
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
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

uasort($exec_fam, 'sortByHigh');

$highArray = array();
$mediumArray = array();
$lowArray = array();
$osArray = array();
$patterns = array();
$patterns[0] = '/Accounts/i';
$patterns[1] = '/Checks/i';
$patterns[2] = '/Linux/i';
$patterns[3] = '/Local/i';
$patterns[4] = '/management/i';
$patterns[5] = '/Microsoft/i';
$patterns[6] = '/Security/i';
$patterns[7] = '/Servers/i';
$patterns[8] = '/Service/i';
$patterns[9] = '/Windows/i';
$replacements = array();
$replacements[0] = 'Acct';
$replacements[1] = 'Ch';
$replacements[2] = 'Lnx';
$replacements[3] = 'Lcl';
$replacements[4] = 'Mgmt';
$replacements[5] = 'MS';
$replacements[6] = 'Sec';
$replacements[7] = 'Srv';
$replacements[8] = 'Svc';
$replacements[9] = 'Win';

$replacements[12] = '';
$replacements[13] = '';
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
$myPicture->drawLegend(535,16,$Config);

$myPicture->stroke();

function sortByHigh($a, $b) { 
	return strnatcmp($b['critical'], $a['critical']); 
} // sort alphabetically by name 

?>
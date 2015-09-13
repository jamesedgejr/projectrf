<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v1 = new Valitron\Validator($_POST);
$v1->rule('slug','newAgencyName');
$v1->rule('regex', 'newfileName','/^([\w _.-])+$/'); //regex includes alpha/numeric, space, underscore, dash, and period
$v1->rule('regex',['w1','w2'], '/^([\w\s_.\[\]():;-])+$/'); //regex includes alpha/numeric, space, underscore, dash, period, white space, brackets, parentheses, colon, and semi-colon
$v1->rule('length',1,['critical','high','medium','low','info']);
$v1->rule('integer',['critical','high','medium','low','info']);
if(!$v1->validate()) {
    print_r($v1->errors());
	exit;
} 


$filename = $_POST["filename"];
$newAgencyName = $_POST["newAgencyName"];
$newfileName = $_POST["newfileName"];
$agencyArray = $fileNameArray = $nmaprunStartArray = $finishedTimeArray = $sortedNmaprunStart = $sortedFinished = array();
if(isset($filename) && isset($newAgencyName) && isset($newfileName)){
	foreach($filename as $key => $value){
		if ($value == "none") unset($filename[$key]);
	}
	foreach($filename as $f){
		$temp = explode(":", $f);
		$v2 = new Valitron\Validator($temp);
		$v2->rule('slug', '0');//validate agency
		$v2->rule('regex','1','/^([\w _.-])+$/');// validate filename
		$v2->rule('numeric',['2','3']);//validate nmaprun_start and finished_time
		if(!$v2->validate()) {
			print_r($v2->errors());
			exit;
		} 
		$agencyArray[] = $temp[0];
		$fileNameArray[] = $temp[1];
		$nmaprunStartArray[] = $temp[2];
		$finishedTimeArray[] = $temp[3];
	}
	$sortedNmaprunStart = $nmaprunStartArray;
	$sortedFinished = $finishedTimeArray;
	sort($sortedNmaprunStart);
	rsort($sortedFinished);
	for($x=0;$x<count($agencyArray);$x++){
		$sql = "UPDATE nmap_runstats_xml
				SET
					nmap_runstats_xml.agency = ?,
					nmap_runstats_xml.filename = ?,
					nmap_runstats_xml.nmaprun_start = ?,
					nmap_runstats_xml.finished_time = ?
				WHERE
					nmap_runstats_xml.agency = ? AND
					nmap_runstats_xml.filename = ? AND
					nmap_runstats_xml.nmaprun_start = ? AND
					nmap_runstats_xml.finished_time = ?
				";
		$stmt = $db->prepare($sql);
		$stmt->execute(array($newAgencyName, $newfileName, $sortedNmaprunStart[0], $sortedFinished, $agencyArray[$x], $fileNameArray[$x], $nmaprunStartArray[$x], $finishedTimeArray[$x]));
	}
}

$merge_sql = 	"SELECT DISTINCT 
					nmap_runstats_xml.agency, 
					nmap_runstats_xml.filename, 
					nmap_runstats_xml.nmaprun_start, 
					nmap_runstats_xml.finished_time 
				FROM 
					nmap_runstats_xml
				";
$merge_stmt = $db->prepare($merge_sql);
$merge_stmt->execute();
?>

<HTML>
<head>
<title>MERGE NMAP SCANS</title>
<script>
function selectAll(selectBox,selectAll) {
    // have we been passed an ID
    if (typeof selectBox == "string") {
        selectBox = document.getElementById(selectBox);
    }

    // is the select box a multiple select box?
    if (selectBox.type == "select-multiple") {
        for (var i = 0; i < selectBox.options.length; i++) {
            selectBox.options[i].selected = selectAll;
        }
    }
}
</script>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
select {font-family: courier new}
</style>
</head>
<BODY>
<table width="100%"><tr><td width="200px" valign="top"><?php include '../main/menu.php'; ?></td>
<td>
<table style="text-align: left; width: 850px;" border="0" cellpadding="0" cellspacing="0" valign="top">
    <tr>
      <td style="width: 600px;text-align: center;">
	  <form action="merge.php" method="post">
	  <p align="center">[ Merge Nmap Scans ]</p>
	  <p align="center">From the list select Agency/filename name that you wish to merge/combine.  
	  <br>The combined results will take the earliest start time and the latest end time.
	  <br>Enter the Agency/Company/filename Title you want to call the merged data.</p>
	  <p><input type="button" name="Button" value="Select All" onclick="selectAll('filenameselectall',true)" /></p>
  	  <select MULTIPLE NAME="filename[]" SIZE="10"  style="width:800px;margin:5px 0 5px 0;" id="filenameselectall">
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Filename]",40)).str_replace(' ','&nbsp;',str_pad("[Date]",20))."</option>";
			while($merge_row = $merge_stmt->fetch(PDO::FETCH_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($merge_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($merge_row["filename"], 40));
				$formatedDate = date("D M d H:i:s Y", $merge_row["finished_time"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $merge_row["agency"] . ":" . $merge_row["filename"] . ":" . $merge_row["nmaprun_start"] . ":" . $merge_row["finished_time"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select><br>
	  <p>Enter New Name for Agengy/Company:<br><input name="newAgencyName" type="text" /></p>
	  <p>Enter New Name for the filename:<br><input name="newfileName" type="text" /></p>
	  <br>
	  <INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
	  </form>
	  </td>
	</tr>
    <tr>
      <td align="center">
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<?php  include '../main/footer.php'; ?>
      </td>
    </tr>
</table>
</td></tr></table>
</body>
</html>	  
	  
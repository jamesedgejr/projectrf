<?php
$agency = $_POST["agency"];


include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );

$sql = 	"SELECT DISTINCT webinspect_xml.vulnID, webinspect_xml.name, webinspect_xml.severity, webinspect_xml.summary FROM webinspect_xml ORDER BY webinspect_xml.severity DESC, webinspect_xml.vulnID ASC";
$result = $db->query($sql);

echo <<<END
<table cellspacing="5" cellpadding="" border="0">
  <tr>
	<td width="65%" colspan="2"><p></p></td>
	<td><p></p></td>
  </tr>
END;

while($row = $result->fetchRow()){
$vulnID = $row[0];
$name = $row[1];
switch ($row[2]){
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
$summary = $row[3];
$fix = $row[4];

$host_sql = "SELECT DISTINCT webinspect_xml.host, webinspect_xml.application FROM webinspect_xml WHERE webinspect_xml.vulnID = '$vulnID'";
$host_result = $db->query($host_sql);
$count = $host_result->numRows();
$count += 4;
echo <<<END
	<tr><td bgcolor="#d0d0d0"><p>Vulnerability</p></td><td bgcolor="#f0f0f0"><p>$name</p></td><td><p align="center"> Management Response</p></td></tr>
	<tr><td bgcolor="#d0d0d0"><p>Severity</p></td><td bgcolor="#f0f0f0"><p>$severity</p></td><td rowspan=$count valign="top"><p></p></td></tr>
	<tr><td bgcolor="#d0d0d0"><p>Host</p></td><td bgcolor="#d0d0d0"><p>Application</p></td></tr>
END;
while ($host_row = $host_result->fetchRow()){
	echo "<tr><td bgcolor=\"#f0f0f0\"><p>$host_row[0]</p></td><td bgcolor=\"#f0f0f0\"><p>$host_row[1]</p></td></tr>";
}
echo <<<END
	<tr><td bgcolor="#d0d0d0" colspan="2"><p>Summary</p></td></tr>
	<tr><td bgcolor="#f0f0f0" colspan="2"><p>$summary</p></td></tr>
	<tr><td colspan="2"><br><br></td></tr>
	

END;

}
?>


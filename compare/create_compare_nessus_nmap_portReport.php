<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$nessus_agency_sql = 	"SELECT DISTINCT 
							nessus_results.agency, 
							nessus_results.report_name, 
							nessus_results.scan_start, 
							nessus_results.scan_end 
						FROM 
							nessus_results
						";
$nmap_agency_sql = 	"SELECT DISTINCT
					nmap_runstats_xml.agency,
					nmap_runstats_xml.filename,
					nmap_runstats_xml.nmaprun_start,
					nmap_runstats_xml.finished_time
				FROM
					nmap_runstats_xml
				";

?>
<HTML>
<head>
<title>COMPARE NESSUS VULNERABILITY REPORTS</title>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
select {font-family: courier new}
</style>
</head>
<BODY>
<table width="100%"><tr><td width="200px" valign="top"><?php include '../main/menu.php'; ?></td>
<td valign="top">
<table style="text-align: left; width: 950px;" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2">
	  <form name="f1"  action="compare_nessus_nmap_portReport.php" method="post">
	  <p align="center">[ Compare Nessus and Nmap Reports ]</p>
  	  <select NAME="nessus_report" SIZE="1"  style="width:950px;margin:5px 0 5px 0;">
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Report Name]",70)).str_replace(' ','&nbsp;',str_pad("[Date]",20))."</option>";
			$nessus_agency_stmt = $db->prepare($nessus_agency_sql);
			$nessus_agency_stmt->execute();
			while($nessus_agency_row = $nessus_agency_stmt->fetch(PDO::FETCH_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($nessus_agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($nessus_agency_row["report_name"], 70));
				$formatedDate = date("D M d H:i:s Y", $nessus_agency_row["scan_end"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $nessus_agency_row["agency"] . ":@:@:" . $nessus_agency_row["report_name"] . ":@:@:" . $nessus_agency_row["scan_start"] . ":@:@:" . $nessus_agency_row["scan_end"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select>
	  <select NAME="nmap_report" SIZE="1"  style="width:950px;margin:5px 0 5px 0;">
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Report Name]",70)).str_replace(' ','&nbsp;',str_pad("[Date]",20))."</option>";
			$nmap_agency_stmt = $db->prepare($nmap_agency_sql);
			$nmap_agency_stmt->execute();
			while($nmap_agency_row = $nmap_agency_stmt->fetch(PDO::FETCH_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($nmap_agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($nmap_agency_row["filename"], 70));
				$formatedDate = date("D M d H:i:s Y", $nmap_agency_row["finished_time"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $nmap_agency_row["agency"] . ":@:@:" . $nmap_agency_row["filename"] . ":@:@:" . $nmap_agency_row["nmaprun_start"] . ":@:@:" . $nmap_agency_row["finished_time"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select>
	  </td></tr>
<tr><td colspan="2">



</td></tr>

<tr><td><br><INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT"></form></td></tr>  
<tr><td colspan="2"><?php  include '../main/footer.php'; ?></td></tr>
</table>
</td></tr></table>
</body>
</html>
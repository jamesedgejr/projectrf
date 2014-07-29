<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$agency_sql = 	"SELECT DISTINCT 
					nessus_results.agency, 
					nessus_results.report_name, 
					nessus_results.scan_start, 
					nessus_results.scan_end 
				FROM 
					nessus_results
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
	  <form name="f1"  action="compareReport.php" method="post">
	  <p align="center">[ Compare Nessus Reports ]</p>
  	  <select NAME="report1" SIZE="1"  style="width:950px;margin:5px 0 5px 0;">
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Report Name]",70)).str_replace(' ','&nbsp;',str_pad("[Date]",20))."</option>";
			$agency_stmt = $db->prepare($agency_sql);
			$agency_stmt->execute();
			while($agency_row = $agency_stmt->fetch(PDO::FETCH_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["report_name"], 70));
				$formatedDate = date("D M d H:i:s Y", $agency_row["scan_end"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $agency_row["agency"] . ":" . $agency_row["report_name"] . ":" . $agency_row["scan_start"] . ":" . $agency_row["scan_end"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select>
	  <select NAME="report2" SIZE="1"  style="width:950px;margin:5px 0 5px 0;">
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Report Name]",70)).str_replace(' ','&nbsp;',str_pad("[Date]",20))."</option>";
			$agency_stmt = $db->prepare($agency_sql);
			$agency_stmt->execute();			
			while($agency_row = $agency_stmt->fetch(PDO::FETCH_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["report_name"], 70));
				$formatedDate = date("D M d H:i:s Y", $agency_row["scan_end"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $agency_row["agency"] . ":" . $agency_row["report_name"] . ":" . $agency_row["scan_start"] . ":" . $agency_row["scan_end"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select>
	  </td></tr>
<tr><td colspan="2">

<table style="border: 1px solid black;width: 950px">
<tr><td colspan="8" style="border-bottom: 1px solid black;">Plugin Information</td></tr>
<tr>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isPlugName" checked></td><td style="width: 174px;">Plugin Name</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isPlugFam" checked></td><td style="width: 174px;">Plugin Family</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isPlugInfo" checked></td><td style="width: 174px;">Additional Information</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isSynopsis" checked></td><td style="width: 174px;">Synopsis</td>
</tr>
<tr>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isDescription" checked></td><td style="width: 174px;">Description</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isSolution" checked></td><td style="width: 174px;">Solution</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isSeeAlso" checked></td><td style="width: 174px;">See Also</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isPlugOut" checked></td><td style="width: 174px;">Plugin Output</td>
</tr>
</table>
</br>
<table style="border: 1px solid black;width: 950px">
<tr><td colspan="4" style="border-bottom: 1px solid black;">Risk Information</td></tr>
<tr><td style="width: 30px;"><input type="checkbox" value="yes" name="isCvss" checked></td><td style="width: 500px;">Common Vulnerability Scoring System (CVSS) Score</td><td></td></tr>
<tr>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isVulnPub"></td><td style="width: 250px;">Vulnerability Publication Date</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isExploit"></td><td style="width: 250px;">Exploit Information</td>
</tr>
</table>
</br>
<table style="border: 1px solid black;width: 950px">
<tr><td colspan="6" style="border-bottom: 1px solid black;">Additional Research Links</td></tr>
<tr>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isCve" checked></td><td style="width: 250px;">Common Vuln Exposure (CVE)</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isBid" checked></td><td style="width: 250px;">Bugtraq ID (BID)</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isOsvdb" checked></td><td style="width: 250px;">Open Source Vuln DB (OSVBD)</td>
</tr>
<tr>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isCert" checked></td><td style="width: 250px;">Cert</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isIava" checked></td><td style="width: 250px;">IAVA</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isCWE" checked></td><td style="width: 250px;">Common Weakness Enum (CWE)</td>
</tr>
<tr>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isMS" checked></td><td style="width: 250px;">Microsoft Bulletin</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isSec" checked></td><td style="width: 250px;">Secunia</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isEdb" checked></td><td style="width: 250px;">Exploit DB (EDB-ID)</td>
</tr>
</table>
</br>
<table style="border: 1px solid black;width: 950px">
<tr><td colspan="10" style="border-bottom: 1px solid black;">Severity</td></tr>
<tr>
<td style="width: 30px;"><input type="checkbox" value="4" name="critical" checked></td><td style="width: 174px;">Critical Risk</td>
<td style="width: 30px;"><input type="checkbox" value="3" name="high" checked></td><td style="width: 174px;">High Risk</td>
<td style="width: 30px;"><input type="checkbox" value="2" name="medium"></td><td style="width: 174px;">Medium Risk</td>
<td style="width: 30px;"><input type="checkbox" value="1" name="low"></td><td style="width: 174px;">Low Risk</td>
<td style="width: 30px;"><input type="checkbox" value="0" name="info"></td><td style="width: 174px;">Information Only</td>
</tr>
</table>
</br>
<table style="border: 1px solid black;width: 950px">
<tr><td colspan="8" style="border-bottom: 1px solid black;">Miscellaneous</td></tr>
<tr>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isNotes"></td><td style="width: 174px;">Include Notes</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isAffected" checked></td><td style="width: 174px;">Include Host List</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="isService" checked></td><td style="width: 174px;">Include Service/Protocol</td>
<td style="width: 30px;"><input type="checkbox" value="yes" name="cover" checked></td><td style="width: 174px;">Include Cover Page</td>
</tr>
</table>

</td></tr>

<tr><td><br><INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT"></form></td></tr>  
<tr><td colspan="2"><?php  include '../main/footer.php'; ?></td></tr>
</table>
</td></tr></table>
</body>
</html>
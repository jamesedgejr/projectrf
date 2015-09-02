<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$agency_temp = explode(":@:", $_POST["agency"]);
$v = new Valitron\Validator($agency_temp);
$v->rule('slug', ['0','3','4']);//validate $agency
$v->rule('numeric','5');//validate scan_start and scan_end
//$v->rule('numeric','1','/[\d]+/');
if($v->validate()) {

} else {
    print_r($v->errors());
	exit;
} 

$agency = $agency_temp[0];
$filename = $agency_temp[1];
$scan_name = $agency_temp[2];
$scan_startTime = $agency_temp[3];
$scan_endTime = $agency_temp[4];
$scan_id = $agency_temp[5];
$scan_sql = 	"SELECT DISTINCT
					nexpose_scans.agency,
					nexpose_scans.filename,
					nexpose_scans.scan_name,
					nexpose_scans.scan_startTime,
					nexpose_scans.scan_endTime,
					nexpose_scans.scan_id
				FROM
					nexpose_scans
				";
$scan_stmt = $db->prepare($scan_sql);
$scan_stmt->execute();
if($agency != ""){
	$host_sql = "SELECT DISTINCT
					nexpose_nodes.node_address,
					nexpose_nodes.node_name,
					nexpose_nodes.node_device_id
					
				FROM
					nexpose_tests
					Inner Join nexpose_nodes ON nexpose_tests.device_id = nexpose_nodes.node_device_id
					Inner Join nexpose_scans ON nexpose_tests.scan_id = nexpose_scans.scan_id
				WHERE
					nexpose_nodes.agency =  ? AND
					nexpose_nodes.filename =  ? AND
					nexpose_scans.scan_name =  ? AND
					nexpose_scans.scan_startTime =  ? AND
					nexpose_scans.scan_endTime =  ? AND
					nexpose_scans.scan_id =  ? 
				ORDER BY
					INET_ATON(nexpose_nodes.node_address)
				";
	$host_data = array($agency, $filename, $scan_name, $scan_startTime, $scan_endTime, $scan_id);
	$host_stmt = $db->prepare($host_sql);
	$host_stmt->execute($host_data);
	$tags_sql = 	"SELECT DISTINCT
						nexpose_tags.tag
					FROM
						nexpose_tags
					Inner Join nexpose_vulnerabilities ON nexpose_vulnerabilities.vuln_id = nexpose_tags.vuln_id
					Inner Join nexpose_tests ON nexpose_tests.test_id = nexpose_vulnerabilities.vuln_id
					Inner Join nexpose_scans ON nexpose_scans.scan_id = nexpose_tests.scan_id
					WHERE
						nexpose_scans.agency =  ? AND
						nexpose_scans.filename =  ? AND
						nexpose_scans.scan_name =  ? AND
						nexpose_scans.scan_startTime =  ? AND
						nexpose_scans.scan_endTime =  ? AND
						nexpose_scans.scan_id =  ? 
					";
	$tags_data = array($agency, $filename, $scan_name, $scan_startTime, $scan_endTime, $scan_id);
	$tags_stmt = $db->prepare($tags_sql);
	$tags_stmt->execute($tags_data);
}//end if

?>

<HTML>
<head>
<title>CREATE NEXPOSE REPORT</title>
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
<td valign="top">
<table style="text-align: left; width: 950px;" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2">
	  <form name="f1"  action="" method="post">
	  <p align="center">[ Nexpose Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.  <br>Then select the hosts and the Nexpose Tag Categories you want to include.</p>
  	  <select NAME="agency" SIZE="10"  style="width:950px;margin:5px 0 5px 0;" ONCHANGE="f1.submit()" >
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Scan Name]",70)).str_replace(' ','&nbsp;',str_pad("[Date]",20))."</option>";
			while($scan_row = $scan_stmt->fetch(PDO::FETCH_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($scan_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($scan_row["scan_name"], 70));
				//$formatedDate = date("D M d H:i:s Y", $scan_row["endTime"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($scan_row["scan_endTime"], 20));
				echo "<option value='" . $scan_row["agency"] . ":@:" . $scan_row["filename"] . ":@:" . $scan_row["scan_name"] . ":@:" . $scan_row["scan_startTime"] . ":@:" . $scan_row["scan_endTime"] . ":@:" . $scan_row["scan_id"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
			
	  </select>
	  </form>
	  </td>
	</tr>  
	<tr>
	  <td style="width: 700px;" valign="top"> 
	  <form name="f2" action="report.php" method="post">
		<?php
		//host list
		if($agency == ""){
		?>
			<p align="center">[ Nodes ]</p>
			<SELECT MULTIPLE NAME="host" SIZE="25" style="width:700px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Nodes ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('hostselectall',true)" />
			<SELECT MULTIPLE NAME="node[]" SIZE="20" style="width:700px;margin:5px 0 5px 0;" id="hostselectall">
		<?php
			echo "<option value=\"REMOVE\">".str_replace(' ','&nbsp;',str_pad("[IP Address]", 26)).str_replace(' ','&nbsp;',str_pad("[FQDN]", 45))."</option>";
			while($host_row = $host_stmt->fetch(PDO::FETCH_ASSOC)){
			  $value1 = str_replace(' ','&nbsp;',str_pad($host_row["node_address"], 26));
			  $value2 = str_replace(' ','&nbsp;',str_pad($host_row["node_name"], 45));
			  echo "<OPTION value='" . $host_row["node_address"] . ":" . $host_row["node_device_id"] ."'>" . $value1 . $value2 . "</OPTION>";
			}//end while
		?>

			</SELECT>				
		<?php
		}//end else
		?>
		<?php
		//nessus plugin families
		if($agency == ""){
		?>
			<p align="center">[ Tag Groups ]</p>
			<SELECT MULTIPLE NAME="tags" SIZE="15" style="width:700px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Tag Groups ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('familyselectall',true)" />
			<SELECT MULTIPLE NAME="tags[]" SIZE="15" style="width:700px;margin:5px 0 5px 0;" id="familyselectall">
		<?php
			while($tags_row = $tags_stmt->fetch(PDO::FETCH_ASSOC)){
					echo "<OPTION value='" . $tags_row["tag"] . "'>" . $tags_row["tag"] . "</OPTION>";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>	  
	<br>
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	  <tr>
	  <td>
		<p>Who created this report?</p>
		<p><textarea style="width:300px;margin:5px 0 5px 0;" rows="5" name="w1"></textarea></p>
	  </td>
	  <td>
		<p>Who is this report for?</p>
		<p><textarea style="width:300px;margin:5px 0 5px 0;" rows="5" name="w2"></textarea></p>
	  </td>
	  </tr>
	</table>  
	<br><table>
	  <TR>
		<TD>
		<input type="hidden" name="agency" value="<?php echo "$agency";?>">
		<input type="hidden" name="filename" value="<?php echo "$filename";?>">
		<input type="hidden" name="scan_name" value="<?php echo "$scan_name";?>">
		<input type="hidden" name="scan_startTime" value="<?php echo "$scan_startTime";?>">
		<input type="hidden" name="scan_endTime" value="<?php echo "$scan_endTime";?>">
		<input type="hidden" name="scan_id" value="<?php echo "$scan_id";?>">
		<INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
		</TD>
	  </TR>
	</table>

	  </td>
      <td style="width: 250px;" valign="top" align="right">
      <table style="text-align: left; width: 225px;" border="0" cellpadding="2" cellspacing="2">
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Vulnerability Information</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isVulnTitle" checked>
			</td>
            <td style="width: 174px;">Vulnerability Title</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isTag" checked>
			</td>
            <td style="width: 174px;">Vulnerability Tags</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isVulnInfo" checked>
			</td>
            <td style="width: 174px;">Additional Information</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isDescription" checked>
			</td>
            <td style="width: 174px;">Description</td>
          </tr>
          <tr>
            <td style="width: 30px;">
                                <input type="checkbox" value="yes" name="isSolution">
                        </td>
            <td style="width: 174px;">Solution</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isVulnOut" checked>
			</td>
            <td style="width: 174px;">Vulnerability Output</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Risk Information</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isCvss" checked>
			</td>
            <td style="width: 174px;">Cvss Score</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isVulnPub">
			</td>
            <td style="width: 174px;">Vuln Pub Date</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isExploit">
			</td>
            <td style="width: 174px;">Exploit Information</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Additional Research Links</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isApple">
            </td>
            <td style="width: 174px;">Apple Vuln</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isBid" checked>
            </td>
            <td style="width: 174px;">Bugtraq ID (BID)</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isCert" checked>
			</td>
            <td style="width: 174px;">Cert</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isCve" checked>
			</td>
            <td style="width: 174px;">Common Vuln Exposer (CVE)</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isMS" checked>
			</td>
            <td style="width: 174px;">Microsoft Bulletin</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isOsvdb" checked>
			</td>
            <td style="width: 174px;">Open Source Vuln DB (OSVBD)</td>
          </tr>

          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isRedHat">
			</td>
            <td style="width: 174px;">RedHat Vuln</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isURL" checked>
			</td>
            <td style="width: 174px;">URLs</td>
          </tr>

          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isXF">
			</td>
            <td style="width: 174px;">XF</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Severity</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="4" name="critical" checked>
			</td>
            <td style="width: 174px;">Critical Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="3" name="high" checked>
			</td>
            <td style="width: 174px;">High Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="2" name="medium">
			</td>
            <td style="width: 174px;">Medium Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="1" name="low">
			</td>
            <td style="width: 174px;">Low Risk</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="0" name="info">
			</td>
            <td style="width: 174px;">Information Only</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Miscellaneous</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isNotes">
			</td>
            <td style="width: 174px;">Include Notes</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isAffected" checked>
			</td>
            <td style="width: 174px;">Include Host List</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isService" checked>
			</td>
            <td style="width: 174px;">Include Service/Protocol</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="cover" checked>
			</td>
            <td style="width: 174px;">Include Cover Page</td>
          </tr>
      </table>
	  </form>
      </td>
    </tr>
    <tr>
      <td colspan="2">
			<?php include '../main/footer.php'; ?>
      </td>
    </tr>
</table>
</td></tr></table>
</body>
</html>


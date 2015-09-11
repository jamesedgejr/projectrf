<?php
include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

$agency_temp = explode(":", $_POST["agency"]);
$v = new Valitron\Validator($agency_temp);
$v->rule('slug', '0');//validate agency
$v->rule('regex','1','/^([\w _.-])+$/');// validate filename
$v->rule('numeric',['2','3']);//validate nmaprun_start and finished_time
if($v->validate()) {

} else {
    print_r($v->errors());
	exit;
} 
$agency = $agency_temp[0];
$filename = $agency_temp[1];
$nmaprun_start = $agency_temp[2];
$finished_time = $agency_temp[3];
$agency_sql = 	"SELECT DISTINCT
					nmap_runstats_xml.agency,
					nmap_runstats_xml.filename,
					nmap_runstats_xml.nmaprun_start,
					nmap_runstats_xml.finished_time
				FROM
					nmap_runstats_xml
				";
$agency_stmt = $db->prepare($agency_sql);
$agency_stmt->execute();

if($agency != ""){
	$host_sql = "SELECT DISTINCT
					nmap_hosts_xml.hostname_name,
					nmap_hosts_xml.address_addr
				FROM
					nmap_runstats_xml
				INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
				WHERE
					nmap_hosts_xml.status_state = 'up' AND
					nmap_runstats_xml.agency = ? AND
					nmap_runstats_xml.filename = ? AND
					nmap_runstats_xml.nmaprun_start = ? AND
					nmap_runstats_xml.finished_time = ?
				ORDER BY
					nmap_hosts_xml.address_addr ASC
				";
	$data = array($agency, $filename, $nmaprun_start, $finished_time);
	$host_stmt = $db->prepare($host_sql);
	$host_stmt->execute($data);

	$port_sql = "SELECT DISTINCT
					nmap_ports_xml.port_portid,
					nmap_ports_xml.port_service_name
				FROM
					nmap_runstats_xml
				INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
				INNER JOIN nmap_ports_xml ON nmap_ports_xml.host_id = nmap_hosts_xml.id
				WHERE
					nmap_hosts_xml.status_state = 'up' AND
					nmap_runstats_xml.agency = ? AND
					nmap_runstats_xml.filename = ? AND
					nmap_runstats_xml.nmaprun_start = ? AND
					nmap_runstats_xml.finished_time = ?
				ORDER BY
					nmap_ports_xml.port_service_name ASC
				";
	$port_stmt = $db->prepare($port_sql);
	$port_stmt->execute($data);
	$nse_port_sql = "SELECT DISTINCT
						nmap_port_nse_xml.script_id
					FROM
						nmap_runstats_xml
					Inner Join nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
					Inner Join nmap_port_nse_xml ON nmap_port_nse_xml.host_id = nmap_hosts_xml.id
					WHERE
						nmap_hosts_xml.status_state = 'up' AND
						nmap_runstats_xml.agency = ? AND
						nmap_runstats_xml.filename = ? AND
						nmap_runstats_xml.nmaprun_start = ? AND
						nmap_runstats_xml.finished_time = ?
					ORDER BY
						nmap_port_nse_xml.script_id ASC
					";
	$nse_port_stmt = $db->prepare($nse_port_sql);
	$nse_port_stmt->execute($data);

	$nse_host_sql = "SELECT DISTINCT
						nmap_host_nse_xml.script_id
					FROM
						nmap_runstats_xml
					Inner Join nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
					Inner Join nmap_host_nse_xml ON nmap_host_nse_xml.host_id = nmap_hosts_xml.id
					WHERE
						nmap_hosts_xml.status_state = 'up' AND
						nmap_runstats_xml.agency = ? AND
						nmap_runstats_xml.filename = ? AND
						nmap_runstats_xml.nmaprun_start = ? AND
						nmap_runstats_xml.finished_time = ?
					ORDER BY
						nmap_host_nse_xml.script_id ASC
					";
	$nse_host_stmt = $db->prepare($nse_host_sql);
	$nse_host_stmt->execute($data);


}//end if
?>

<HTML>
<head>
<title>CREATE NMAP SCAN MATRIX</title>
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
<table style="text-align: left; width: 850px;" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td style="width: 600px;">
	  <form name="f1"  action="" method="post">
	  <p align="center">[ Nmap Reports ]</p>
	  <p align="center">Select Agency/Report name that you uploaded to the database.  <br>Then select the hosts and the Nmap NSE Scripts you want to include.</p>
  	  <select NAME="agency" SIZE="10"  style="width:700px;margin:5px 0 5px 0;" ONCHANGE="f1.submit()" >
			<?php
			echo "<option value=\"none\" selected>".str_replace(' ','&nbsp;',str_pad("[Agency/Company]",20)).str_replace(' ','&nbsp;',str_pad("[Report Name]",40)).str_replace(' ','&nbsp;',str_pad("[Date]",20))."</option>";
			while($agency_row = $agency_stmt->fetch(PDO::FETCH_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["filename"], 40));
				$formatedDate = date("D M d H:i:s Y", $agency_row["finished_time"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $agency_row["agency"] . ":" . $agency_row["filename"] . ":" . $agency_row["nmaprun_start"] . ":" . $agency_row["finished_time"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select>
	  </form>
	<form name="f2" action="matrix.php" method="post">
		<?php
		//host list
		if($agency == ""){
		?>
			<p align="center">[ Hosts ]</p>
			<SELECT MULTIPLE NAME="host" SIZE="25" style="width:700px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Hosts ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('hostselectall',true)" /><br>
			<SELECT MULTIPLE NAME="host[]" SIZE="20" style="width:700px;margin:5px 0 5px 0;" id="hostselectall">
		<?php
			echo "<option value=\"dlskeaAKEJFDAKE\">".str_replace(' ','&nbsp;',str_pad("[Host Name]", 40)).str_replace(' ','&nbsp;',str_pad("[IP Address]", 40))."</option>";
			while($host_row = $host_stmt->fetch(PDO::FETCH_ASSOC)){
			  $value1 = str_replace(' ','&nbsp;',str_pad($host_row["hostname_name"], 40));
			  $value2 = str_replace(' ','&nbsp;',str_pad($host_row["address_addr"], 40));
			  echo "<OPTION value='" . $host_row["address_addr"] . "'>" . $value1 . $value2 . "</OPTION>";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>
		<table width="600px">
		  <tr><td width="50%" align="left">
		<?php
		if($agency == ""){
		?>
			<p align="center">[ NSE Scripts ]</p>
			<SELECT MULTIPLE NAME="nse" SIZE="15" style="width:290px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ NSE Port Scripts ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('nseportselectall',true)" />
			<SELECT MULTIPLE NAME="nsePort[]" SIZE="10" style="width:290px;margin:5px 0 5px 0;" id="nseportselectall">
		<?php
			echo "<option value=\"dlskeaAKEJFDAKE\">".str_replace(' ','&nbsp;',str_pad("[Script ID]", 4))."</option>";
			while($nse_port_row = $nse_port_stmt->fetch(PDO::FETCH_ASSOC)){
				$script_id = str_replace(' ','&nbsp;',str_pad($nse_port_row["script_id"], 30));
				echo "<OPTION value='" . $nse_port_row["script_id"] . "'>" . $script_id . "</OPTION>";
			}//end while
		?>
			</SELECT>
			<p align="center">[ NSE Host Scripts ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('nsehostselectall',true)" />
			<SELECT MULTIPLE NAME="nseHost[]" SIZE="10" style="width:290px;margin:5px 0 5px 0;" id="nsehostselectall">
		<?php
			echo "<option value=\"dlskeaAKEJFDAKE\">".str_replace(' ','&nbsp;',str_pad("[Script ID]", 4))."</option>";
			while($nse_host_row = $nse_host_stmt->fetch(PDO::FETCH_ASSOC)){
				$script_id = str_replace(' ','&nbsp;',str_pad($nse_host_row["script_id"], 30));
				echo "<OPTION value='" . $nse_host_row["script_id"] . "'>" . $script_id . "</OPTION>";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>
		</td>
		<td width="50%" align="right" valign="top">
		<?php
		if($agency == ""){
		?>
			<p align="center">[ Ports ]</p>
			<SELECT MULTIPLE NAME="nse" SIZE="25" style="width:290px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Ports ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('portselectall',true)" />
			<SELECT MULTIPLE NAME="ports[]" SIZE="25" style="width:290px;margin:5px 0 5px 0;" id="portselectall">
		<?php
			echo "<option value=\"dlskeaAKEJFDAKE\">".str_replace(' ','&nbsp;',str_pad("[Num]", 5)).str_replace(' ','&nbsp;',str_pad("[Name]", 4))."</option>";
			while($port_row = $port_stmt->fetch(PDO::FETCH_ASSOC)){
				$value1 = str_replace(' ','&nbsp;',str_pad($port_row["port_portid"], 10));
				$value2 = str_replace(' ','&nbsp;',str_pad($port_row["port_service_name"], 30));
				echo "<OPTION value='" . $port_row["port_portid"] . ":" . $port_row["port_service_name"] . "'>" . $value1 . $value2 . "</OPTION>";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>		
		</td>
		</tr>
		</table>
	<br><table align="center">
	  <TR>
		<TD>
		<input type="hidden" name="agency" value="<?php echo "$agency";?>">
		<input type="hidden" name="filename" value="<?php echo "$filename";?>">
		<input type="hidden" name="nmaprun_start" value="<?php echo "$nmaprun_start";?>">
		<input type="hidden" name="finished_time" value="<?php echo "$finished_time";?>">
		<INPUT TYPE="SUBMIT" NAME="submithost" VALUE="SUBMIT">
		</TD>
	  </TR>
	</table>
	  </td>
      <td style="width: 250px;" valign="top" align="right">
      <table style="text-align: left; width: 225px;" border="0" cellpadding="2" cellspacing="2">
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Host State</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isUp" checked>
			</td>
            <td style="width: 174px;">Up</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isDown">
			</td>
            <td style="width: 174px;">Down</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Port State</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isOpen" checked>
			</td>
            <td style="width: 174px;">Open</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isClosed">
			</td>
            <td style="width: 174px;">Closed</td>
          </tr>
         <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isFiltered">
			</td>
            <td style="width: 174px;">Filtered</td>
          </tr>
         <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="yes" name="isOpenFiltered">
			</td>
            <td style="width: 174px;">Open|Filtered</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">NSE Script</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="port" name="nsescript" checked>
			</td>
            <td style="width: 174px;">NSE Port Scripts</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="host" name="nsescript">
			</td>
            <td style="width: 174px;">NSE Host Scripts</td>
          </tr>
          <tr>
            <td colspan=2><p>I can do baby (NSE Port), or I can do geezer murder mystery (NSE Host), but I can't do both!</p></td>
          </tr>
      </table>
      </td>
    </tr>
    <tr>
      <td colspan="2">
			<?php  include '../main/footer.php'; ?>
      </td>
    </tr>
</table>
</FORM>

</td></tr></table>
</body>
</html>
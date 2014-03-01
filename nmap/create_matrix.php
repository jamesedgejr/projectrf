<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );

$agency_temp = explode(":", $_POST["agency"]);
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
$agency_result = $db->query($agency_sql);ifError($agency_result);

if($agency != ""){
	$host_sql = "SELECT DISTINCT
					nmap_hosts_xml.hostname_name,
					nmap_hosts_xml.address_addr
				FROM
					nmap_runstats_xml
				INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
				WHERE
					nmap_hosts_xml.status_state = 'up' AND
					nmap_runstats_xml.agency = '$agency' AND
					nmap_runstats_xml.filename = '$filename' AND
					nmap_runstats_xml.nmaprun_start = '$nmaprun_start' AND
					nmap_runstats_xml.finished_time = '$finished_time'
				ORDER BY
					nmap_hosts_xml.address_addr ASC
				";

	$host_result = $db->query($host_sql);ifError($host_result);
	$port_sql = "SELECT DISTINCT
					nmap_ports_xml.port_portid,
					nmap_ports_xml.port_service_name
				FROM
					nmap_runstats_xml
				INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
				INNER JOIN nmap_ports_xml ON nmap_ports_xml.host_id = nmap_hosts_xml.id
				WHERE
					nmap_hosts_xml.status_state = 'up' AND
					nmap_runstats_xml.agency = '$agency' AND
					nmap_runstats_xml.filename = '$filename' AND
					nmap_runstats_xml.nmaprun_start = '$nmaprun_start' AND
					nmap_runstats_xml.finished_time = '$finished_time'
				ORDER BY
					nmap_ports_xml.port_service_name ASC
				";

	$port_result = $db->query($port_sql);ifError($port_result);
	$nse_port_sql = 	"SELECT DISTINCT
					nmap_nse_xml.script_type,
					nmap_nse_xml.script_id
				FROM
					nmap_runstats_xml
				INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
				INNER JOIN nmap_ports_xml ON nmap_ports_xml.host_id = nmap_hosts_xml.id
				INNER JOIN nmap_nse_xml ON nmap_nse_xml.host_or_port_id = nmap_ports_xml.id
				WHERE
					nmap_hosts_xml.status_state = 'up' AND
					nmap_runstats_xml.agency = '$agency' AND
					nmap_runstats_xml.filename = '$filename' AND
					nmap_runstats_xml.nmaprun_start = '$nmaprun_start' AND
					nmap_runstats_xml.finished_time = '$finished_time'
				ORDER BY
					nmap_nse_xml.script_id ASC
				";
	$nse_port_result = $db->query($nse_port_sql);ifError($nse_port_result);
	$nse_host_sql = "SELECT DISTINCT
						nmap_nse_xml.script_type,
						nmap_nse_xml.script_id
					FROM
						nmap_runstats_xml
					INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
					INNER JOIN nmap_nse_xml ON nmap_nse_xml.host_or_port_id = nmap_hosts_xml.id
					WHERE
						nmap_hosts_xml.status_state = 'up' AND
						nmap_runstats_xml.agency = '$agency' AND
						nmap_runstats_xml.filename = '$filename' AND
						nmap_runstats_xml.nmaprun_start = '$nmaprun_start' AND
						nmap_runstats_xml.finished_time = '$finished_time'
					ORDER BY
						nmap_nse_xml.script_id ASC
					";
	$nse_host_result = $db->query($nse_host_sql);ifError($nse_host_result);
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
  	  <select NAME="agency" SIZE="10"  style="width:600px;margin:5px 0 5px 0;" ONCHANGE="f1.submit()" >
		<option value="dlskeaAKEJFDAKE" selected>[Agency]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Report Name]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Date/Time]]</option>
			<?php
			while($agency_row = $agency_result->fetchRow(DB_FETCHMODE_ASSOC)){
			    $value1 = str_replace(' ','&nbsp;',str_pad($agency_row["agency"], 20));
			    $value2 = str_replace(' ','&nbsp;',str_pad($agency_row["filename"], 20));
				$formatedDate = date("D M d H:i:s Y", $agency_row["finished_time"]);
				$value3 = str_replace(' ','&nbsp;',str_pad($formatedDate, 20));
				echo "<option value='" . $agency_row["agency"] . ":" . $agency_row["filename"] . ":" . $agency_row["nmaprun_start"] . ":" . $agency_row["finished_time"] . "'>" . $value1 . $value2 . $value3 . "</option>";
			}
			?>
	  </select>
	  </form>
	<form name="f2" action="test_matrix.php" method="post">
		<?php
		//host list
		if($agency == ""){
		?>
			<p align="center">[ Hosts ]</p>
			<SELECT MULTIPLE NAME="host" SIZE="25" style="width:600px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Hosts ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('hostselectall',true)" /><br>
			<SELECT MULTIPLE NAME="host[]" SIZE="20" style="width:600px;margin:5px 0 5px 0;" id="hostselectall">
			<option value='dlskeaAKEJFDAKE'>[Host Name]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[IP Address]&nbsp;&nbsp;&nbsp;&nbsp;</option>
		<?php
			while($host_row = $host_result->fetchRow(DB_FETCHMODE_ASSOC)){
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
			<p align="center">[ NSE Scripts ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('nseselectall',true)" />
			<SELECT MULTIPLE NAME="nse[]" SIZE="15" style="width:290px;margin:5px 0 5px 0;" id="nseselectall">
			<option value='dlskeaAKEJFDAKE'>[Type]&nbsp;&nbsp;&nbsp;&nbsp;[Script ID]&nbsp;&nbsp;&nbsp;&nbsp;</option>
		<?php
			while($nse_port_row = $nse_port_result->fetchRow(DB_FETCHMODE_ASSOC)){
				$value1 = str_replace(' ','&nbsp;',str_pad($nse_port_row["script_type"], 10));
				$value2 = str_replace(' ','&nbsp;',str_pad($nse_port_row["script_id"], 30));
				echo "<OPTION value='" . $nse_port_row["script_type"] . ":" . $nse_port_row["script_id"] . "'>" . $value1 . $value2 . "</OPTION>";
			}//end while
			while($nse_host_row = $nse_host_result->fetchRow(DB_FETCHMODE_ASSOC)){
				$value1 = str_replace(' ','&nbsp;',str_pad($nse_host_row["script_type"], 10));
				$value2 = str_replace(' ','&nbsp;',str_pad($nse_host_row["script_id"], 30));
				echo "<OPTION value='" . $nse_host_row["script_type"] . ":" . $nse_host_row["script_id"] . "'>" . $value1 . $value2 . "</OPTION>";
			}//end while
		?>
			</SELECT>				
		<?php
		}//end else
		?>
		</td>
		<td width="50%" align="right">
		<?php
		if($agency == ""){
		?>
			<p align="center">[ Ports ]</p>
			<SELECT MULTIPLE NAME="nse" SIZE="15" style="width:290px;margin:5px 0 5px 0;">
			  <OPTION>[no agency selected]</OPTION>
			</SELECT>
		<?php
		}//end if
		else {
		?>
			<p align="center">[ Ports ]</p><input type="button" name="Button" value="Select All" onclick="selectAll('portselectall',true)" />
			<SELECT MULTIPLE NAME="ports[]" SIZE="15" style="width:290px;margin:5px 0 5px 0;" id="portselectall">
			<option value='dlskeaAKEJFDAKE'>[Num]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Name]&nbsp;&nbsp;&nbsp;&nbsp;</option>
		<?php
			while($port_row = $port_result->fetchRow(DB_FETCHMODE_ASSOC)){
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
            <td colspan="2" rowspan="1" style="width: 30px;">Port State</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="open" name="isOpen" checked>
			</td>
            <td style="width: 174px;">Open</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="closed" name="isClosed">
			</td>
            <td style="width: 174px;">Closed</td>
          </tr>
         <tr>
            <td style="width: 30px;">
				<input type="checkbox" value="filtered" name="isFiltered">
			</td>
            <td style="width: 174px;">Filtered</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="1" style="width: 30px;">Table Pivot</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="left" name="pivot" checked>
			</td>
            <td style="width: 174px;">Hosts Along the Left</td>
          </tr>
          <tr>
            <td style="width: 30px;">
				<input type="radio" value="top" name="pivot">
			</td>
            <td style="width: 174px;">Hosts Along the Top</td>
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
<?php
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
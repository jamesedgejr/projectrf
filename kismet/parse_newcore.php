<html>
<head><title>Completed upload of Kismet NETXML file.</title>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
</style>
</head>
<body>
<table width="100%"><tr>
	<td width="20%" valign="top">
	<?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">

<?php
$agency = $_POST["agency"];
$location = $_POST["location"];
$floor = $_POST["floor"];
$uploaddir = sys_get_temp_dir();
$uploadfile = tempnam(sys_get_temp_dir(), basename($_FILES['userfile']['name']));
$file_name = basename($_FILES['userfile']['name']);
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
   echo "<hr><p align=\"center\"><b>File is valid, and was successfully uploaded.</b></p><hr>";
} else { 
   echo "<h1>Upload Error!</h1>";
   echo "An error occurred while executing this script. The file may be too large or the upload folder may have insufficient permissions.";
   echo "<p />";
   echo "Please examine the following items to see if there is an issue";
   echo "<hr><pre>";
   echo "1.  /tmp (Temp) directory exists and has the correct permissions.<br />";
   echo "2.  The php.ini file needs to be modified to allow for larger file uploads.<br />";
   echo "</pre><hr>";
   exit; 
}


if(file_exists($uploadfile)) { 
	$xml = simplexml_load_file($uploadfile);
} 
else { 
	exit('Failed to open the xml file');
} 

include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );
$kismet_version = $xml['kismet-version'];
$start_time = $xml['start-time'];
$end_time = $xml['end-time'];
foreach($xml->{'card-source'} as $cs){
	$cs_uuid = $cs['uuid'];
	$cs_card_name = $cs->{'card-name'};
	$cs_card_interface = $cs->{'card-interface'};	
	$cs_card_type = $cs->{'card-type'};
	$cs_card_packets = $cs->{'card-packets'};
	$cs_card_hop = $cs->{'card-hop'};
	$cs_card_channels = $cs->{'card-channels'};
}
foreach($xml->{'wireless-network'} as $wn){
	$wn_number = $wn[number];
	$wn_type = $wn[type];
	$wn_first_time = $wn["first-time"];
	$wn_last_time = $wn["last-time"];
	$wn_SSID_first_time = $wn->SSID["first-time"];
	$wn_SSID_last_time = $wn->SSID["last-time"];
	$wn_SSID_type = $wn->SSID->type;
	$wn_SSID_max_rate = $wn->SSID->{'max-rate'};
	$wn_SSID_packets = $wn->SSID->packets;
	$wn_SSID_beaconrate = $wn->SSID->beaconrate;
	$wn_SSID_encryption = "";
	if($wn->SSID->encryption){
		foreach($wn->SSID->encryption as $enc){
			$wn_SSID_encryption .= $enc . ",";
		}
	}
	$wn_SSID__dot11d_country = $wn->SSID->dot11d[country];
	$wn_SSID_dot11d_range_start = $wn->SSID->dot11d->{'dot11d-range'}[start];
	$wn_SSID_dot11d_range_end = $wn->SSID->dot11d->{'dot11d-range'}[end];
	$wn_SSID_dot11d_range_max_power = $wn->SSID->dot11d->{'dot11d-range'}["max-power"];
	$wn_SSID_essid_cloaked =$wn->SSID->essid[cloaked];
	$wn_SSID_essid_name = addslashes($wn->SSID->essid);
	$wn_SSID_info = addslashes($wn->SSID->info);
	$wn_BSSID = $wn->BSSID;
	$wn_manuf = $wn->manuf;
	$wn_channel = $wn->channel;
	$wn_freqmhz = $wn->freqmhz;
	$wn_maxrate = $wn->maxrate;
	$wn_maxseenrate = $wn->maxseenrate;
	$wn_carrier = $wn->carrier;
	$wn_encoding = $wn->encoding;
	$wn_packets_LLC = $wn->packets->LLC;
	$wn_packets_data = $wn->packets->data;
	$wn_packets_crypt = $wn->packets->crypt;
	$wn_packets_total = $wn->packets->total;
	$wn_packets_fragments = $wn->packets->fragments;
	$wn_packets_retries = $wn->packets->retries;
	$wn_gps_info_min_lat = $wn->{'gps-info'}->{'min-lat'};
	$wn_gps_info_min_lon = $wn->{'gps-info'}->{'min-lon'};
	$wn_gps_info_min_alt = $wn->{'gps-info'}->{'min-alt'};
	$wn_gps_info_min_spd = $wn->{'gps-info'}->{'min-spd'};
	$wn_gps_info_max_lat = $wn->{'gps-info'}->{'max-lat'};
	$wn_gps_info_max_lon = $wn->{'gps-info'}->{'max-lon'};
	$wn_gps_info_max_alt = $wn->{'gps-info'}->{'max-alt'};
	$wn_gps_info_max_spd = $wn->{'gps-info'}->{'max-spd'};
	$wn_gps_info_peak_lat = $wn->{'gps-info'}->{'peak-lat'};
	$wn_gps_info_peak_lon = $wn->{'gps-info'}->{'peak-lon'};
	$wn_gps_info_peak_alt = $wn->{'gps-info'}->{'peak-alt'};
	$wn_gps_info_avg_lat = $wn->{'gps-info'}->{'avg-lat'};
	$wn_gps_info_avg_lon = $wn->{'gps-info'}->{'avg-lon'};
	$wn_gps_info_avg_alt = $wn->{'gps-info'}->{'avg-alt'};
	$wn_ip_address = $wn->{'ip-address'};
	$wn_ip_range = $wn->{'ip-range'};
	$wn_datasize = $wn->datasize;
	$wn_snr_info_last_signal_dbm = $wn->{'snr-info'}->last_signal_dbm;
	$wn_snr_info_last_noise_dbm = $wn->{'snr-info'}->last_noise_dbm;
	$wn_snr_info_last_signal_rssi = $wn->{'snr-info'}->last_signal_rssi;
	$wn_snr_info_last_noise_rssi = $wn->{'snr-info'}->last_noise_rssi;
	$wn_snr_info_min_signal_dbm = $wn->{'snr-info'}->min_signal_dbm;
	$wn_snr_info_min_noise_dbm = $wn->{'snr-info'}->min_noise_dbm;
	$wn_snr_info_min_signal_rssi = $wn->{'snr-info'}->min_signal_rssi;
	$wn_snr_info_min_noise_rssi = $wn->{'snr-info'}->min_noise_rssi;
	$wn_snr_info_max_signal_dbm = $wn->{'snr-info'}->max_signal_dbm;
	$wn_snr_info_max_noise_dbm = $wn->{'snr-info'}->max_noise_dbm;
	$wn_snr_info_max_signal_rssi = $wn->{'snr-info'}->max_signal_rssi;
	$wn_snr_info_max_noise_rssi = $wn->{'snr-info'}->max_noise_rssi;
	$wn_bsstimestamp = $wn->bsstimestamp;
	$wn_cdp_device = $wn->{'cdp-device'};
	$wn_cdp_portid = $wn->{'cdp-portid'};
	$wn_seen_card_seen_uuid = $wn->{'seen-card'}->{'seen-uuid'};
	$wn_seen_card_seen_time = $wn->{'seen-card'}->{'seen-time'};
	$wn_seen_card_seen_packets = $wn->{'seen-card'}->{'seen-packets'};
	
	foreach($wn->{'wireless-client'} as $wc){
		$wc_number = $wc[number];
		$wc_type = $wc[type];
		$wc_first_time = $wc["first-time"];
		$wc_last_time = $wc["last-time"];
		$wc_client_mac = $wc->{'client-mac'};
		$wc_client_manuf = $wc->{'client-manuf'};
		$wc_SSID_first_time = $wc->SSID["first-time"];
		$wc_SSID_last_time = $wc->SSID["last-time"];
		$wc_SSID_type = $wc->SSID->type;
		$wc_SSID_max_rate = $wc->SSID->{'max-rate'};
		$wc_SSID_packets = $wc->SSID->packets;
		$wc_SSID_beaconrate = $wc->SSID->beaconrate;
		$wc_SSID_encryption = "";
		if($wc->SSID->encryption){
			foreach($wc->SSID->encryption as $enc){
				$wc_SSID_encryption .= $enc . ",";
			}
		}
		$wc_SSID_ssid = addslashes($wc->SSID->ssid);
		$wc_channel = $wc->channel;
		$wc_freqmhz = $wc->freqmhz;
		$wc_maxrate = $wc->maxrate;
		$wc_maxseenrate = $wc->maxseenrate;
		$wc_encoding = $wc->encoding;
		$wc_packets_LLC = $wc->packets->LLC;
		$wc_packets_data = $wc->packets->data;
		$wc_packets_crypt = $wc->packets->crypt;
		$wc_packets_total = $wc->packets->total;
		$wc_packets_fragments = $wc->packets->fragments;
		$wc_packets_retries = $wc->packets->retries;
		$wc_gps_info_min_lat = $wc->{'gps-info'}->{'min-lat'};
		$wc_gps_info_min_lon = $wc->{'gps-info'}->{'min-lon'};
		$wc_gps_info_min_alt = $wc->{'gps-info'}->{'min-alt'};
		$wc_gps_info_min_spd = $wc->{'gps-info'}->{'min-spd'};
		$wc_gps_info_max_lat = $wc->{'gps-info'}->{'max-lat'};
		$wc_gps_info_max_lon = $wc->{'gps-info'}->{'max-lon'};
		$wc_gps_info_max_alt = $wc->{'gps-info'}->{'max-alt'};
		$wc_gps_info_max_spd = $wc->{'gps-info'}->{'max-spd'};
		$wc_gps_info_peak_lat = $wc->{'gps-info'}->{'peak-lat'};
		$wc_gps_info_peak_lon = $wc->{'gps-info'}->{'peak-lon'};
		$wc_gps_info_peak_alt = $wc->{'gps-info'}->{'peak-alt'};
		$wc_gps_info_avg_lat = $wc->{'gps-info'}->{'avg-lat'};
		$wc_gps_info_avg_lon = $wc->{'gps-info'}->{'avg-lon'};
		$wc_gps_info_avg_alt = $wc->{'gps-info'}->{'avg-alt'};
		$wc_datasize = $wc->datasize;
		$wc_snr_info_last_signal_dbm = $wc->{'snr-info'}->last_signal_dbm;
		$wc_snr_info_last_noise_dbm = $wc->{'snr-info'}->last_noise_dbm;
		$wc_snr_info_last_signal_rssi = $wc->{'snr-info'}->last_signal_rssi;
		$wc_snr_info_last_noise_rssi = $wc->{'snr-info'}->last_noise_rssi;
		$wc_snr_info_min_signal_dbm = $wc->{'snr-info'}->min_signal_dbm;
		$wc_snr_info_min_noise_dbm = $wc->{'snr-info'}->min_noise_dbm;
		$wc_snr_info_min_signal_rssi = $wc->{'snr-info'}->min_signal_rssi;
		$wc_snr_info_min_noise_rssi = $wc->{'snr-info'}->min_noise_rssi;
		$wc_snr_info_max_signal_dbm = $wc->{'snr-info'}->max_signal_dbm;
		$wc_snr_info_max_noise_dbm = $wc->{'snr-info'}->max_noise_dbm;
		$wc_snr_info_max_signal_rssi = $wc->{'snr-info'}->max_signal_rssi;
		$wc_snr_info_max_noise_rssi = $wc->{'snr-info'}->max_noise_rssi;
		$wc_cdp_device = $wc->{'cdp-device'};
		$wc_cdp_portid = $wc->{'cdp-portid'};
		$wc_seen_card_seen_uuid = $wc->{'seen-card'}->{'seen-uuid'};
		$wc_seen_card_seen_time = $wc->{'seen-card'}->{'seen-time'};
		$wc_seen_card_seen_packets = $wc->{'seen-card'}->{'seen-packets'};	
		
		$sql = "INSERT INTO kismet_results_newcore 
				(
				agency,
				location,
				floor,
				file_name,
				kismet_version,
				start_time,
				end_time,
				cs_uuid,
				cs_card_name,
				cs_card_interface,
				cs_card_type,
				cs_card_packets,
				cs_card_hop,
				cs_card_channels,
				wn_number,
				wn_type,
				wn_first_time,
				wn_last_time,
				wn_SSID_first_time,
				wn_SSID_last_time,
				wn_SSID_type,
				wn_SSID_max_rate,
				wn_SSID_packets,
				wn_SSID_beaconrate,
				wn_SSID_encryption,
				wn_SSID__dot11d_country,
				wn_SSID_dot11d_range_start,
				wn_SSID_dot11d_range_end,
				wn_SSID_dot11d_range_max_power,
				wn_SSID_essid_cloaked,
				wn_SSID_essid_name,
				wn_SSID_info,
				wn_BSSID,
				wn_manuf,
				wn_channel,
				wn_freqmhz,
				wn_maxrate,
				wn_maxseenrate,
				wn_carrier,
				wn_encoding,
				wn_packets_LLC,
				wn_packets_data,
				wn_packets_crypt,
				wn_packets_total,
				wn_packets_fragments,
				wn_packets_retries,
				wn_gps_info_min_lat,
				wn_gps_info_min_lon,
				wn_gps_info_min_alt,
				wn_gps_info_min_spd,
				wn_gps_info_max_lat,
				wn_gps_info_max_lon,
				wn_gps_info_max_alt,
				wn_gps_info_max_spd,
				wn_gps_info_peak_lat,
				wn_gps_info_peak_lon,
				wn_gps_info_peak_alt,
				wn_gps_info_avg_lat,
				wn_gps_info_avg_lon,
				wn_gps_info_avg_alt,
				wn_ip_address,
				wn_ip_range,
				wn_datasize,
				wn_snr_info_last_signal_dbm,
				wn_snr_info_last_noise_dbm,
				wn_snr_info_last_signal_rssi,
				wn_snr_info_last_noise_rssi,
				wn_snr_info_min_signal_dbm,
				wn_snr_info_min_noise_dbm,
				wn_snr_info_min_signal_rssi,
				wn_snr_info_min_noise_rssi,
				wn_snr_info_max_signal_dbm,
				wn_snr_info_max_noise_dbm,
				wn_snr_info_max_signal_rssi,
				wn_snr_info_max_noise_rssi,
				wn_bsstimestamp,
				wn_cdp_device,
				wn_cdp_portid,
				wn_seen_card_seen_uuid,
				wn_seen_card_seen_time,
				wn_seen_card_seen_packets,
				wc_number,
				wc_type,
				wc_first_time,
				wc_last_time,
				wc_client_mac,
				wc_client_manuf,
				wc_SSID_first_time,
				wc_SSID_last_time,
				wc_SSID_type,
				wc_SSID_max_rate,
				wc_SSID_packets,
				wc_SSID_beaconrate,
				wc_SSID_encryption,
				wc_SSID_ssid,
				wc_channel,
				wc_freqmhz,
				wc_maxseenrate,
				wc_packets_LLC,
				wc_packets_data,
				wc_packets_crypt,
				wc_packets_total,
				wc_packets_fragments,
				wc_packets_retries,
				wc_gps_info_min_lat,
				wc_gps_info_min_lon,
				wc_gps_info_min_alt,
				wc_gps_info_min_spd,
				wc_gps_info_max_lat,
				wc_gps_info_max_lon,
				wc_gps_info_max_alt,
				wc_gps_info_max_spd,
				wc_gps_info_peak_lat,
				wc_gps_info_peak_lon,
				wc_gps_info_peak_alt,
				wc_gps_info_avg_lat,
				wc_gps_info_avg_lon,
				wc_gps_info_avg_alt,
				wc_datasize,
				wc_snr_info_last_signal_dbm,
				wc_snr_info_last_noise_dbm,
				wc_snr_info_last_signal_rssi,
				wc_snr_info_last_noise_rssi,
				wc_snr_info_min_signal_dbm,
				wc_snr_info_min_noise_dbm,
				wc_snr_info_min_signal_rssi,
				wc_snr_info_min_noise_rssi,
				wc_snr_info_max_signal_dbm,
				wc_snr_info_max_noise_dbm,
				wc_snr_info_max_signal_rssi,
				wc_snr_info_max_noise_rssi,
				wc_cdp_device,
				wc_cdp_portid,
				wc_seen_card_seen_uuid,
				wc_seen_card_seen_time,
				wc_seen_card_seen_packets
				) 
			VALUES 
				(
				'$agency',
				'$location',
				'$floor',
				'$file_name',
				'$kismet_version', 
				'$start_time',
				'$end_time',
				'$cs_uuid', 
				'$cs_card_name', 
				'$cs_card_interface', 
				'$cs_card_type', 
				'$cs_card_packets', 
				'$cs_card_hop', 
				'$cs_card_channels', 
				'$wn_number', 
				'$wn_type', 
				'$wn_first_time', 
				'$wn_last_time', 
				'$wn_SSID_first_time', 
				'$wn_SSID_last_time', 
				'$wn_SSID_type', 
				'$wn_SSID_max_rate', 
				'$wn_SSID_packets', 
				'$wn_SSID_beaconrate', 
				'$wn_SSID_encryption', 
				'$wn_SSID__dot11d_country',
				'$wn_SSID_dot11d_range_start',
				'$wn_SSID_dot11d_range_end',
				'$wn_SSID_dot11d_range_max_power',
				'$wn_SSID_essid_cloaked', 
				'$wn_SSID_essid_name',
				'$wn_SSID_info',
				'$wn_BSSID', 
				'$wn_manuf', 
				'$wn_channel', 
				'$wn_freqmhz', 
				'$wn_maxrate', 
				'$wn_maxseenrate', 
				'$wn_carrier', 
				'$wn_encoding', 
				'$wn_packets_LLC', 
				'$wn_packets_data', 
				'$wn_packets_crypt', 
				'$wn_packets_total', 
				'$wn_packets_fragments', 
				'$wn_packets_retries', 
				'$wn_gps_info_min_lat',
				'$wn_gps_info_min_lon',
				'$wn_gps_info_min_alt',
				'$wn_gps_info_min_spd',
				'$wn_gps_info_max_lat',
				'$wn_gps_info_max_lon',
				'$wn_gps_info_max_alt',
				'$wn_gps_info_max_spd',
				'$wn_gps_info_peak_lat',
				'$wn_gps_info_peak_lon',
				'$wn_gps_info_peak_alt',
				'$wn_gps_info_avg_lat',
				'$wn_gps_info_avg_lon',
				'$wn_gps_info_avg_alt',
				'$wn_ip_address',
				'$wn_ip_range',
				'$wn_datasize', 
				'$wn_snr_info_last_signal_dbm', 
				'$wn_snr_info_last_noise_dbm', 
				'$wn_snr_info_last_signal_rssi', 
				'$wn_snr_info_last_noise_rssi', 
				'$wn_snr_info_min_signal_dbm', 
				'$wn_snr_info_min_noise_dbm', 
				'$wn_snr_info_min_signal_rssi', 
				'$wn_snr_info_min_noise_rssi', 
				'$wn_snr_info_max_signal_dbm', 
				'$wn_snr_info_max_noise_dbm', 
				'$wn_snr_info_max_signal_rssi', 
				'$wn_snr_info_max_noise_rssi', 
				'$wn_bsstimestamp', 
				'$wn_cdp_device', 
				'$wn_cdp_portid', 
				'$wn_seen_card_seen_uuid', 
				'$wn_seen_card_seen_time', 
				'$wn_seen_card_seen_packets', 
				'$wc_number', 
				'$wc_type', 
				'$wc_first_time', 
				'$wc_last_time', 
				'$wc_client_mac', 
				'$wc_client_manuf', 
				'$wc_SSID_first_time',
				'$wc_SSID_last_time',
				'$wc_SSID_type',
				'$wc_SSID_max_rate',
				'$wc_SSID_packets',
				'$wc_SSID_beaconrate',
				'$wc_SSID_encryption',
				'$wc_SSID_ssid',
				'$wc_channel', 
				'$wc_freqmhz', 
				'$wc_maxseenrate', 
				'$wc_packets_LLC', 
				'$wc_packets_data', 
				'$wc_packets_crypt', 
				'$wc_packets_total', 
				'$wc_packets_fragments', 
				'$wc_packets_retries', 
				'$wc_gps_info_min_lat',
				'$wc_gps_info_min_lon',
				'$wc_gps_info_min_alt',
				'$wc_gps_info_min_spd',
				'$wc_gps_info_max_lat',
				'$wc_gps_info_max_lon',
				'$wc_gps_info_max_alt',
				'$wc_gps_info_max_spd',
				'$wc_gps_info_peak_lat',
				'$wc_gps_info_peak_lon',
				'$wc_gps_info_peak_alt',
				'$wc_gps_info_avg_lat',
				'$wc_gps_info_avg_lon',
				'$wc_gps_info_avg_alt',
				'$wc_datasize', 
				'$wc_snr_info_last_signal_dbm', 
				'$wc_snr_info_last_noise_dbm', 
				'$wc_snr_info_last_signal_rssi', 
				'$wc_snr_info_last_noise_rssi', 
				'$wc_snr_info_min_signal_dbm', 
				'$wc_snr_info_min_noise_dbm', 
				'$wc_snr_info_min_signal_rssi', 
				'$wc_snr_info_min_noise_rssi', 
				'$wc_snr_info_max_signal_dbm', 
				'$wc_snr_info_max_noise_dbm', 
				'$wc_snr_info_max_signal_rssi', 
				'$wc_snr_info_max_noise_rssi', 
				'$wc_cdp_device', 
				'$wc_cdp_portid', 
				'$wc_seen_card_seen_uuid', 
				'$wc_seen_card_seen_time', 
				'$wc_seen_card_seen_packets' 
				)";
		$result = $db->query($sql);ifDBError($result);
	}
}


?>
</td></tr></table>
</body>
</html>
<?php 
function ifDBError($error)
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
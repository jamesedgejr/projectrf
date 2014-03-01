<?php

$netxml_file = $argv[1];
$switch = $argv[2];
if(file_exists($netxml_file)) {
         $xml = simplexml_load_file($netxml_file);
}
else {
        exit('Failed to open the netxml file');
}

$values = array();
foreach($xml->{'wireless-network'} as $wn){

	$wn_type = $wn[type];
	$wn_BSSID = $wn->BSSID;
	$wn_SSID_essid_name = addslashes($wn->SSID->essid);
	$wn_channel = $wn->channel;	
	foreach($wn->{'wireless-client'} as $wc){
		$wc_type = $wc[type];
		$wc_client_mac = $wc->{'client-mac'};
		$wc_channel = $wc->channel;
		if($wn_type == "infrastructure" && $wn_SSID_essid_name != "" && $wn_channel != ""){
			if($switch == "1"){
				$values[] = array("$wn_SSID_essid_name", "$wn_channel");
			} else {
				if($wn_BSSID != $wc_client_mac){
					$values[] = array("$wn_BSSID", "$wc_client_mac");
				}
			}
		}
	}
}
$tmp = array ();
foreach ($values as $v) { 
    if (!in_array($v,$tmp)) {
		$tmp[] = $v;
	}
}

foreach ($tmp as $t){
	print $t[0] . "," . $t[1] . "\n";
}


//start kismet_server silently with only netxml produced
//sleep for X number of seconds
//killall kismet_server
//parse netxml to create CSV file
//run bash script aireplay and setting the wireless channel with CSV file in deauthentication

?>
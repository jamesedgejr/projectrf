<?php

function writePoint($y, $type, $name, $manufacturer, $total_clients, $packets, $bssid, $channel, $maxrate, $hidden, $gps_lon, $gps_lat, $encryption, $clients, $iconColor, $clients_true) {
  
  fwrite($y, "
        <Placemark>
          <visibility>0</visibility>
          <name>$name</name>
          <description><![CDATA[
           <ul>
            <li> Manufacturer : $manufacturer </li>
  ");
  if($clients_true){
    fwrite($y, "          <li> Clients:  $total_clients</li>");
  }
  fwrite($y, "			
            <li> Packets : $packets </li>
            <li> BSSID : $bssid </li>
            <li> Channel : $channel </li>
            <li> Max Rate : $maxrate </li>
            <li> Hidden : $hidden </li>
            <li> GPS : ($gps_lon , $gps_lat) </li>
	        <li> Encryption : ( $encryption) </li>
  ");
  if($clients_true){
	fwrite($y, "    	<li> Client list:  $clients </li>");
  }
  fwrite($y, "
           </ul>
           ]]>
          </description>
  ");
  if(preg_match("/netgear/i", $manufacturer) && $type == "infrastructure"){
          $manuf = "netgear";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/2wire/i", $manufacturer) && $type == "infrastructure"){
          $manuf = "2wire";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/apple/i", $manufacturer) && $type == "infrastructure"){
          $manuf = "apple";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/cisco/i", $manufacturer) && !preg_match("/linksys/i", $manufacturer) && $type == "infrastructure"){
          $manuf = "cisco";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/motorola/i", $manufacturer) || preg_match("/netopia/i", $manufacturer) && $type == "infrastructure"){
          $manuf = "motorola";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/linksys/i", $manufacturer) && $type == "infrastructure"){
          $manuf = "linksys";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/d.link/i", $manufacturer) && $type == "infrastructure"){
          $manuf = "dlink";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/3com/i", $manufacturer) && $type == "infrastructure"){
          $manuf = "3com";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/belkin/i", $manufacturer) && $type == "infrastructure"){
          $manuf = "belkin";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/hpsetup/i", $name) && $type == "ad-hoc"){
          $manuf = "hpsetup";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/nintendo/i", $manufacturer) && $type == "probe"){
          $manuf = "nintendo";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/apple/i", $manufacturer) && $type == "probe"){
          $manuf = "powerbook";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/hon/i", $manufacturer) && $type == "probe"){
          $manuf = "iphone";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  elseif(preg_match("/rim/i", $manufacturer) || preg_match("/research/i", $manufacturer) && $type == "probe"){
          $manuf = "blackberry";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  }
  else {
		  $manuf = "default";
		  fwrite($y, "          <styleUrl>#$manuf:$iconColor</styleUrl>");
  } 
  fwrite($y, "
          <Point>
            <extrude>1</extrude>
            <altitudeMode>relativeToGround</altitudeMode>
            <coordinates>$gps_lon,$gps_lat,0</coordinates>
          </Point>
        </Placemark>
  ");//end fwrite
  $manufCOLOR = "$manuf:$iconColor";
  return $manufCOLOR;
  
}

function writeStyle($y,$uniqueSA) {
$ipaddress = $_POST["ipaddress"];
$portnumber = $_POST["portnumber"];
echo $ipaddress . ":" . $portnumber . "<br>";
  foreach($uniqueSA as $sA){
	list($manuf, $color) = explode(":", $sA);
    fwrite($y, "
      <Style id=\"$sA\">
         <IconStyle>
          <Icon>
            <href>http://" . $ipaddress . ":" . $portnumber . "/kismet/images/icons/$color.png</href>
          </Icon>
         </IconStyle>
        <BalloonStyle>
          <text><![CDATA[
            <img src=\"http://" . $ipaddress . ":" . $portnumber . "/kismet/images/icons/" . $manuf . "_128.png\" height=\"80\">
            <br/>
            <b><font color=\"#CC0000\" size=\"+2\">$[name]</font></b>
            <br/>
            <font face=\"Courier\" size=\"-2\">$[description]</font>
            <br/>
           ]]></text>
        </BalloonStyle>
      </Style>
    ");
  }
}

function writeClient($wc_mac, $wc_manuf, $wc_clients){
$ipaddress = $_POST["ipaddress"];
$portnumber = $_POST["portnumber"];

    if(preg_match("/nintendo/i", $wc_manuf)){
		$wc_clients .= "<img src=\"http://" . $ipaddress . ":" . $portnumber . "/kismet/images/icons/nintendo_128.png\" height=\"25\" align=left vspace=1 hspace=1>" . $wc_mac . "<br>" . $wc_manuf . "<br><br>";
	}
	elseif(preg_match("/apple/i", $wc_manuf)){
          $wc_clients .= "<img src=\"http://" . $ipaddress . ":" . $portnumber . "/kismet/images/icons/powerbook_128.png\" height=\"25\" align=left vspace=1 hspace=1>" . $wc_mac . "<br>" . $wc_manuf . "<br><br>";
	}
	elseif(preg_match("/hon/i", $wc_manuf)){
          $wc_clients .= "<img src=\"http://" . $ipaddress . ":" . $portnumber . "/kismet/images/icons/iphone_128.png\" height=\"25\" align=left vspace=1 hspace=1>" . $wc_mac . "<br>" . $wc_manuf . "<br><br>";
	}
	elseif(preg_match("/rim/i", $wc_manuf) || preg_match("/research/i", $wc_manuf)){
          $wc_clients .= "<img src=\"http://" . $ipaddress . ":" . $portnumber . "/kismet/images/icons/blackberry_128.png\" height=\"25\" align=left vspace=1 hspace=1>" . $wc_mac . "<br>" . $wc_manuf . "<br><br>";
	}
	else {
		$wc_clients .= "<img src=\"http://" . $ipaddress . ":" . $portnumber . "/kismet/images/icons/laptop_128.png\" height=\"25\" align=left vspace=1 hspace=1>" . $wc_mac . "<br>" . $wc_manuf . "<br><br>";
	}
	return $wc_clients;
}
//read in oui.txt and load it into an array.  this file lists the hardware address assigned to all manufacturers
$myfile = 'oui.txt';
$lines = file($myfile);
$manuf = array();
for($i=0;$i<count($lines);$i++){
        $tmp = explode("\t", $lines[$i]);
        $hex = $tmp[0];
        $m = $tmp[1];
        $manuf[$hex] = $m;
}

$uploaddir = sys_get_temp_dir();
$uploadfile = tempnam(sys_get_temp_dir(), basename($_FILES['userfile']['name']));
if (move_uploaded_file($_FILES['xmlfile']['tmp_name'], $uploadfile)) {
} else {
          echo "<p align=\"center\"><b>Error Uploading the File</b></p>";
          exit;
}

$temp = explode(".", $_FILES['xmlfile']['name']);
$length = count($temp);

if($temp[$length-1] != "xml"){
        echo "<p align=\"center\"><b>You uploaded the wrong file.  Please upload an Kismet XML file.</b></p>";
        exit;
}

if(file_exists($uploadfile)) {
         $xml = simplexml_load_file($uploadfile);
}
else {
        exit('Failed to open the xml file');
}

$filename = $_POST["kmlfilename"];


$kmldir = "kmlfiles/";
$myFile = $kmldir . $filename . ".kml";
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");

$kismet_version = $xml['kismet-version'];
$start_time = $xml['start-time'];
$end_time = $xml['end-time'];
$infra_total = 0;
$infra_open = 0;
$infra_wep = 0;
$infra_wpa = 0;
$infra_cloaked = 0;
$adhoc_total = 0;
$probe_total = 0;
$styleArray = array();
//KML Header
fwrite($fh, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
fwrite($fh, "<kml xmlns=\"http://earth.google.com/kml/0.5\">\n");
fwrite($fh, "<Document>\n");
fwrite($fh, "   <name>Wireless Access Point Location</name>\n");
fwrite($fh, "   <description><![CDATA[
					<p><font face=\"Courier\" size=\"-2\">Wardrive Results<br>
					Kismet:  $kismet_version<br>
					Start:  $start_time<br>
					End:  $end_time</font></p>
				]]></description>\n");

//#################################################
// IDENTIFY AND MAP OPEN ACCESS POINTS  #  GREEN  #
//#################################################

fwrite($fh, "
   <Folder>
     <name> Open Access Points</name>
");

foreach($xml->{'wireless-network'} as $wn){

  $wn_number = "$wn[number]";  $wn_type = "$wn[type]";  $wn_wep = "$wn[wep]";  $wn_cloaked = "$wn[cloaked]";  $wn_ssid = htmlspecialchars($wn->SSID);  $wn_bssid = $wn->BSSID; $wn_channel = $wn->channel;  $wn_maxrate = $wn->maxrate;  $wn_pk_data = $wn->packets->data;  $wn_pk_total = $wn->packets->total;  $gps_max_lat = $wn->{'gps-info'}->{'max-lat'};  $gps_max_lon = $wn->{'gps-info'}->{'max-lon'};
  
  $bssid_tmp = explode(":", $wn_bssid);
  $wn_manuf = rtrim($manuf["$bssid_tmp[0]$bssid_tmp[1]$bssid_tmp[2]"]);
  $wn_encryption = "";
  foreach($wn->encryption as $enc){
	$wn_encryption .= $enc . " ";
  }
  $wc_client_total = 0;
  $wc_clients = "<br>";
  foreach ($wn->{'wireless-client'} as $wc){
	$wc_client_mac = $wc->{'client-mac'};
	$wc_client_mac_tmp = explode(":", $wn_bssid);
    $wc_manuf = $manuf["$wc_client_mac_tmp[0]$wc_client_mac_tmp[1]$wc_client_mac_tmp[2]"];
	$wc_clients = writeClient($wc_client_mac,$wc_manuf,$wc_clients);
    $wc_client_total++;
  }//end client count foreach
  if($wn_type == "infrastructure" && $wn_ssid != "" && $gps_max_lon != 0.000000 && $gps_max_lat != 0.000000 && $gps_max_lon != -90.000000 && $gps_max_lat != -90.000000 && $wn_wep == "false"){
	$infra_open++;
	$infra_total++;
	$manufCOLOR = writePoint($fh, $wn_type, $wn_ssid, $wn_manuf, $wc_client_total, $wn_pk_total, $wn_bssid, $wn_channel, $wn_maxrate, $wn_cloaked, $gps_max_lon, $gps_max_lat, $wn_encryption, $wc_clients, "GREEN", 1);
    $styleArray[] = $manufCOLOR;
  }//end infrasturcture if
}//end foreach
fwrite($fh, "
     <description>Total Open Networks Identified:  $infra_open</description>
   </Folder>
");


//##################################################
// IDENTIFY AND MAP WEP ACCESS POINTS   #  ORANGE  #
//##################################################


fwrite($fh, "
   <Folder>
     <name>WEP Encrypted Access Points</name>
");

foreach($xml->{'wireless-network'} as $wn){

  $wn_number = "$wn[number]";  $wn_type = "$wn[type]";  $wn_wep = "$wn[wep]";  $wn_cloaked = "$wn[cloaked]";  $wn_ssid = htmlspecialchars($wn->SSID);  $wn_bssid = $wn->BSSID; $wn_channel = $wn->channel;  $wn_maxrate = $wn->maxrate;  $wn_pk_data = $wn->packets->data;  $wn_pk_total = $wn->packets->total;  $gps_max_lat = $wn->{'gps-info'}->{'max-lat'};  $gps_max_lon = $wn->{'gps-info'}->{'max-lon'};
  
  $bssid_tmp = explode(":", $wn_bssid);
  $wn_manuf = rtrim($manuf["$bssid_tmp[0]$bssid_tmp[1]$bssid_tmp[2]"]);
  $wn_encryption = "";
  foreach($wn->encryption as $enc){
	$wn_encryption .= $enc . " ";
  }
  $wc_client_total = 0;
  $wc_clients = "<br>";
  foreach ($wn->{'wireless-client'} as $wc){
	$wc_client_mac = $wc->{'client-mac'};
	$wc_client_mac_tmp = explode(":", $wn_bssid);
    $wc_manuf = $manuf["$wc_client_mac_tmp[0]$wc_client_mac_tmp[1]$wc_client_mac_tmp[2]"];
	$wc_clients = writeClient($wc_client_mac,$wc_manuf,$wc_clients);
    $wc_client_total++;
  }//end client count foreach
  if($wn_type == "infrastructure" && $wn_ssid != "" && $gps_max_lon != 0.000000 && $gps_max_lat != 0.000000 && $gps_max_lon != -90.000000 && $gps_max_lat != -90.000000 && $wn_wep == "true" && !preg_match("/WPA/i", $wn_encryption)){
	$infra_wep++;
	$infra_total++;
	$manufCOLOR = writePoint($fh, $wn_type, $wn_ssid, $wn_manuf, $wc_client_total, $wn_pk_total, $wn_bssid, $wn_channel, $wn_maxrate, $wn_cloaked, $gps_max_lon, $gps_max_lat, $wn_encryption, $wc_clients, "ORANGE", 1);
    $styleArray[] = $manufCOLOR;
  }//end if
}//end foreach

fwrite($fh, "
     <description>Total WEP Protected Networks Identified:  $infra_wep</description>
   </Folder>
");


//###############################################
// IDENTIFY AND MAP WPA ACCESS POINTS   #  RED  # 
//###############################################


fwrite($fh, "
   <Folder>
     <name>WPA Encrypted Access Points</name>
");

foreach($xml->{'wireless-network'} as $wn){

  $wn_number = "$wn[number]";  $wn_type = "$wn[type]";  $wn_wep = "$wn[wep]";  $wn_cloaked = "$wn[cloaked]";  $wn_ssid = htmlspecialchars($wn->SSID);  $wn_bssid = $wn->BSSID; $wn_channel = $wn->channel;  $wn_maxrate = $wn->maxrate;  $wn_pk_data = $wn->packets->data;  $wn_pk_total = $wn->packets->total;  $gps_max_lat = $wn->{'gps-info'}->{'max-lat'};  $gps_max_lon = $wn->{'gps-info'}->{'max-lon'};
  
  $bssid_tmp = explode(":", $wn_bssid);
  $wn_manuf = rtrim($manuf["$bssid_tmp[0]$bssid_tmp[1]$bssid_tmp[2]"]);
  $wn_encryption = "";
  foreach($wn->encryption as $enc){
	$wn_encryption .= $enc . " ";
  }
  $wc_client_total = 0;
  $wc_clients = "<br>";
  foreach ($wn->{'wireless-client'} as $wc){
	$wc_client_mac = $wc->{'client-mac'};
	$wc_client_mac_tmp = explode(":", $wn_bssid);
    $wc_manuf = $manuf["$wc_client_mac_tmp[0]$wc_client_mac_tmp[1]$wc_client_mac_tmp[2]"];
	$wc_clients = writeClient($wc_client_mac,$wc_manuf,$wc_clients);
    $wc_client_total++;
  }//end client count foreach
  if($wn_type == "infrastructure" && $wn_ssid != "" && $gps_max_lon != 0.000000 && $gps_max_lat != 0.000000 && $gps_max_lon != -90.000000 && $gps_max_lat != -90.000000 && preg_match("/WPA/i", $wn_encryption)){
	$infra_wpa++;
	$infra_total++;
	$manufCOLOR = writePoint($fh, $wn_type, $wn_ssid, $wn_manuf, $wc_client_total, $wn_pk_total, $wn_bssid, $wn_channel, $wn_maxrate, $wn_cloaked, $gps_max_lon, $gps_max_lat, $wn_encryption, $wc_clients, "RED", 1);
    $styleArray[] = $manufCOLOR;
  }//end infrasturcture if
}//end foreach
fwrite($fh, "
     <description>Total WPA Protected Networks Identified:  $infra_wpa</description>
   </Folder>
");


//#########################################################
// IDENTIFY AND MAP CLOAKED ACCESS POINTS   #  LIGHTBLUE  #
//#########################################################


fwrite($fh, "
   <Folder>
     <name> Cloaked Access Points</name>
");

foreach($xml->{'wireless-network'} as $wn){

  $wn_number = "$wn[number]";  $wn_type = "$wn[type]";  $wn_wep = "$wn[wep]";  $wn_cloaked = "$wn[cloaked]";  $wn_ssid = htmlspecialchars($wn->SSID);  $wn_bssid = $wn->BSSID; $wn_channel = $wn->channel;  $wn_maxrate = $wn->maxrate;  $wn_pk_data = $wn->packets->data;  $wn_pk_total = $wn->packets->total;  $gps_max_lat = $wn->{'gps-info'}->{'max-lat'};  $gps_max_lon = $wn->{'gps-info'}->{'max-lon'};
  
  $bssid_tmp = explode(":", $wn_bssid);
  $wn_manuf = rtrim($manuf["$bssid_tmp[0]$bssid_tmp[1]$bssid_tmp[2]"]);
  $wn_encryption = "";
  foreach($wn->encryption as $enc){
	$wn_encryption .= $enc . " ";
  }
  $wc_client_total = 0;
  $wc_clients = "<br>";
  foreach ($wn->{'wireless-client'} as $wc){
	$wc_client_mac = $wc->{'client-mac'};
	$wc_client_mac_tmp = explode(":", $wn_bssid);
    $wc_manuf = $manuf["$wc_client_mac_tmp[0]$wc_client_mac_tmp[1]$wc_client_mac_tmp[2]"];
	$wc_clients = writeClient($wc_client_mac,$wc_manuf,$wc_clients);
    $wc_client_total++;
  }//end client count foreach
  if($wn_type == "infrastructure" && $wn_ssid != "" && $gps_max_lon != 0.000000 && $gps_max_lat != 0.000000 && $gps_max_lon != -90.000000 && $gps_max_lat != -90.000000 && $wn_cloaked == "true"){
	$infra_cloaked++;
	$infra_total++;
	$manufCOLOR = writePoint($fh, $wn_type, $wn_ssid, $wn_manuf, $wc_client_total, $wn_pk_total, $wn_bssid, $wn_channel, $wn_maxrate, $wn_cloaked, $gps_max_lon, $gps_max_lat, $wn_encryption, $wc_clients, "LIGHTBLUE", 1);
    $styleArray[] = $manufCOLOR;
  }//end if
}//end foreach
fwrite($fh, "
     <description>Total Hidden/Cloaked Networks Identified:  $infra_cloaked</description>
   </Folder>
");


//#####################################################
// IDENTIFY AND MAP AD-HOC ACCESS POINTS    #  BLACK  #
//#####################################################


fwrite($fh, "
   <Folder>
      <name> Ad-Hoc Networks</name>
");

foreach($xml->{'wireless-network'} as $wn){

  $wn_number = "$wn[number]";  $wn_type = "$wn[type]";  $wn_pk_data = $wn->packets->data;  $wn_pk_total = $wn->packets->total;  $gps_max_lat = $wn->{'gps-info'}->{'max-lat'};  $gps_max_lon = $wn->{'gps-info'}->{'max-lon'};  $wn_wep = "$wn[wep]";  $wn_cloaked = "$wn[cloaked]";  $wn_ssid = htmlspecialchars($wn->SSID);  $wn_bssid = $wn->BSSID;  $wn_channel = $wn->channel;  $wn_maxrate = $wn->maxrate;

  $bssid_tmp = explode(":", $wn_bssid);
  $wn_manuf = rtrim($manuf["$bssid_tmp[0]$bssid_tmp[1]$bssid_tmp[2]"]);
  $wn_encryption = "";
  foreach($wn->encryption as $enc){
	$wn_encryption .= $enc . " ";
  }
  if($wn_type == "ad-hoc" && $wn_ssid != "" && $gps_max_lon != 0.000000 && $gps_max_lat != 0.000000 && $gps_max_lon != -90.000000 && $gps_max_lat != -90.000000){
	$adhoc_total++;
	$manufCOLOR = writePoint($fh, $wn_type, $wn_ssid, $wn_manuf, $wc_client_total, $wn_pk_total, $wn_bssid, $wn_channel, $wn_maxrate, $wn_cloaked, $gps_max_lon, $gps_max_lat, $wn_encryption, $wc_clients, "BLACK", 0);
    $styleArray[] = $manufCOLOR;
  }//end if
}//end foreach
fwrite($fh, "
     <description>Total Ad-Hoc Networks Identified:  $adhoc_total</description>
   </Folder>
");


//####################################################
// IDENTIFY AND MAP PROBE CLIENTS           #  BLUE  #
//####################################################

fwrite($fh, "
   <Folder>
      <name>Probe Clients</name>
");

foreach($xml->{'wireless-network'} as $wn){

  $wn_number = "$wn[number]";  $wn_type = "$wn[type]";  $wn_wep = "$wn[wep]";  $wn_cloaked = "$wn[cloaked]";  $wn_ssid = htmlspecialchars($wn->SSID);  $wn_bssid = $wn->BSSID;  $wn_channel = $wn->channel;  $wn_maxrate = $wn->maxrate;  $wn_pk_data = $wn->packets->data;  $wn_pk_total = $wn->packets->total;  $gps_max_lat = $wn->{'gps-info'}->{'max-lat'};  $gps_max_lon = $wn->{'gps-info'}->{'max-lon'};
  $bssid_tmp = explode(":", $wn_bssid);
  $wn_manuf = rtrim($manuf["$bssid_tmp[0]$bssid_tmp[1]$bssid_tmp[2]"]);

  $wn_encryption = "";
  foreach($wn->encryption as $enc){
	$wn_encryption .= $enc . " ";
  }
  if($wn_type == "probe" && $wn_ssid != "" && $gps_max_lon != 0.000000 && $gps_max_lat != 0.000000 && $gps_max_lon != -90.000000 && $gps_max_lat != -90.000000){
	$probe_total++;
	$manufCOLOR = writePoint($fh, $wn_type, $wn_ssid, $wn_manuf, $wc_client_total, $wn_pk_total, $wn_bssid, $wn_channel, $wn_maxrate, $wn_cloaked, $gps_max_lon, $gps_max_lat, $wn_encryption, $wc_clients, "BLUE", 0);
    $styleArray[] = $manufCOLOR;
  }//end if
}//end foreach
fwrite($fh, "
     <description>Total Probe Clients Identified:  $probe_total</description>
   </Folder>
");

//###########################################
//  WRITE STYLES                            #
//###########################################

$uniqueStyleArray = array_unique($styleArray);
writeStyle($fh, $uniqueStyleArray);

//###########################################
fwrite($fh, "
 </Document>
</kml>
");//end fwrite

$ipaddress = $_POST["ipaddress"];
$portnumber = $_POST["portnumber"];
?>
<html>
<head>
<title>Kismet Parse</title>
</head>
<body>
<table width="100%"><tr>
    <td valign="top">
<?php
echo "<p>$filename.xml created</p>";
echo "<p>Click <a href=\"http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=http:%2F%2F" . $ipaddress . ":" . $portnumber . "%2Fkismet%2Fkmlfiles%2F$filename.kml\">here</a> to view the data in Google Maps</p>";
?>

</td></tr></table>
</body></html>


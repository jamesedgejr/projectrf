<?php
$uploaddir = sys_get_temp_dir();
$uploadfile = tempnam(sys_get_temp_dir(), basename($_FILES['userfile']['name']));
if (move_uploaded_file($_FILES['txtfile']['tmp_name'], $uploadfile)) {
} else {
          echo "<p align=\"center\"><b>Error Uploading the File</b></p>";
          exit;
}


if(file_exists($uploadfile)) {
         $lines = file($uploadfile);
}
else {
        exit('Failed to open the file');
}

$filename = $_POST["kmlfilename"];
$dir = "results/";
$myFile = $dir . $filename . ".kml";
$fh = fopen($myFile, 'w') or die("can't open $myFile for writing.  Please check folder permissions.");

function writePoint($y, $ip_address, $country, $state, $city, $gps_lon, $gps_lat) {
  fwrite($y, "
        <Placemark>
          <visibility>0</visibility>
          <name>$ip_address</name>
          <description><![CDATA[
           <ul>
	    <li> $country </li>
	    <li> $state </li>
	    <li> $city </li>
            <li> GPS : ($gps_lon , $gps_lat) </li>
           </ul>
           ]]>
          </description>
	");
  fwrite($y, "          <styleUrl>#mystyle</styleUrl>");
  fwrite($y, "
          <Point>
            <extrude>1</extrude>
            <altitudeMode>relativeToGround</altitudeMode>
            <coordinates>$gps_lon,$gps_lat,50</coordinates>
          </Point>
        </Placemark>
  ");  
}

//KML Header
fwrite($fh, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
fwrite($fh, "<kml xmlns=\"http://www.opengis.net/kml/2.2\">\n");
//fwrite($fh, "<kml xmlns=\"http://earth.google.com/kml/0.5\">\n");

fwrite($fh, "<Document>\n");
fwrite($fh, "   <name>IP Address Location</name>\n");
fwrite($fh, "   <description><![CDATA[
					<p><font face=\"Courier\" size=\"-2\"Results<br>
					</font></p>
				]]></description>\n");

fwrite($fh, "
  <Style id=\"pushpin\">
    <IconStyle id=\"mystyle\">
      <Icon>
        <href>http://maps.google.com/mapfiles/kml/pushpin/ylw-pushpin.png</href>
        <scale>1.0</scale>
      </Icon>
    </IconStyle>
  </Style>
 ");

fwrite($fh, "
   <Folder>
     <name>IP Addresses</name>
");

foreach($lines as $l){
	$items = explode("," , $l);
	$ip_address = $items[0];
	$country = $items[1];
	$state = $item[2];
	$city = $items[3];
	$gps = explode(" ", $items[4]);
	writePoint($fh, $ip_address, $country, $state, $city, $gps[0], $gps[1]);
}//end foreach

fwrite($fh, "
     <description>SOME DESCRIPTION</description>
   </Folder>
");

fwrite($fh, "
 </Document>
</kml>
");//end fwrite


?>
<html>
<head>
<title>Ed File Parse</title>
</head>
<body>
<table width="100%"><tr>
    <td valign="top">

</td></tr></table>
</body></html>

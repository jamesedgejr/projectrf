<?php
$myfile = 'oui.txt';
$lines = file($myfile);
$BSSID = "00:0C:41:F4:70:A1";
$manuf = array();
for($i=0;$i<count($lines);$i++){
	$tmp = explode("\t", $lines[$i]);
	$hex = $tmp[0];
	$m = $tmp[1];
	$manuf[$hex] = $m;
}
	$tmp = explode(":", $BSSID);
	echo "$tmp[0]$tmp[1]$tmp[2]\n";
	$t = $manuf["$tmp[0]$tmp[1]$tmp[2]"];
	echo "MANUF:  $t\n";
	if(preg_match("/linksys/i", $t)){
		echo "$t\n";
	}
?>

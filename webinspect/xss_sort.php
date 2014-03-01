<pre>
<?php
include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );

	$url_sql = "SELECT 
		     `webinspect_xml`.`url`,
		     `webinspect_xml`.`request_fullquery` 
		    FROM
		     `webinspect_xml`
		    WHERE
			(`webinspect_xml`.`vulnID` = '5649')
		    ";
	$url_result = $db->query($url_sql);
	while($url_row = $url_result->fetchRow()){		
		$url_split1 = explode("?", $url_row[0]);
		$url_split2 = explode("/", $url_split1[0]);
		
		$last_value = count($url_split2);
		$last_value = $last_value - 1;
		$final_array[] = array('value' => $url_split2[$last_value],'folder'=>$url_split2[4],'url' => $url_row[0],'post' => $url_row[1]);
	
	}

function subval_sort($a,$subkey) {
	foreach ($a as $k=>$v) {
		$b[$k] = strtolower($v[$subkey]);
	}
	asort($b);
	foreach($b as $key=>$val) {
		$c[] = $a[$key];
	}
	return $c;
}
	$sorted = subval_sort($final_array,'value');
	print_r($sorted);

?>
</pre>
<table width="100%">
  <tr><TD>
	<textarea cols="100" rows="30">VALUE,FOLDER,URL,POST<?php echo "\n";
		
		foreach($sorted as $key1=>$val1){
			print "$val1[value],$val1[folder],$val1[url],$val[post]\n";

		}//end foreach
	?></textarea>		
  </TD></TR>
</table>

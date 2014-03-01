<?php
$uploaddir = '/tmp/upload_';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

$uploaddir = sys_get_temp_dir();
$uploadfile = tempnam(sys_get_temp_dir(), basename($_FILES['userfile']['name']));
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
  $xp = new XSLTProcessor();
  $xsl = new DomDocument;
  $xsl->load('nmap.xsl');
  $xp->importStylesheet($xsl);
  $xml_doc = new DomDocument;
  $xml_doc->load($uploadfile);

  if ($html = $xp->transformToXML($xml_doc)) {
      echo $html;
  } else {
      trigger_error('XSL transformation failed.', E_USER_ERROR);
  }
} else { 
		echo "<h1>Upload Error!</h1>";
		echo "An error occurred while executing this script. The file may be too large or the upload folder may have insufficient permissions.";
		echo "<p />";
		echo "Please examine the following items to see if there is an issue";
		echo "<hr><pre>";
		echo "1.  ".$uploaddir." (Temp) directory exists and has the correct permissions.<br />";
		echo "2.  The php.ini file needs to be modified to allow for larger file uploads.<br />";
		echo "</pre><hr>";
		exit; 
}



?> 
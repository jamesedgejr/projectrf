<html>
<head>
<title>UPLOAD KISMET SCAN</title>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
</style>
</head>
<body>
	<table cellspacing="5" cellpadding="5" width="600">
		<tr>
			<td colspan="2">
				<form enctype="multipart/form-data" action="kismet2kml_v2.php" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000000" />
				<img src="images/kismet_logo.png"></img>
				<p>The Kismet XML file will be parsed and converted to a Google Maps / Earth KML file.  Wireless AP and Client devices are mapped according to location, encryption type, and manufacturer.</p>
				<p>The KML file created with the icons will need to be hosted externally for Google Maps to find it.  Enter your ip address and it will be included in the KML.</p>
			</td>
		</tr>
		<tr>
			<td><p>Select Kismet XML file: </p></td><td><input name="xmlfile" type="file" /></td>
		</tr>
		<tr>
			<td><p>Enter KML Name: </p></td><td><input name="kmlfilename" type="text"></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Process File" />
				</form>
			</td>
		</tr>
	</table>
</body>
</html>

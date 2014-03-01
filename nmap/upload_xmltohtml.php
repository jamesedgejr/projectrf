<html>
<head>
<title>UPLOAD NMAP XML FILE</title>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
</style>
</head>
<body>
<table width="100%"><tr>
	<td width="200px" valign="top">
		<?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">
	<br><br>
	<table cellspacing="5" cellpadding="5" width="600">
		<tr>
			<td colspan="2">
				<form enctype="multipart/form-data" action="xmltohtml.php" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000000" />
				<img src="images/nmap_logo.png"></img>
				<p>The NMAP XML file will be parsed with the NMAP XSL file to create an HTML report.</p>
				<p>This HTML report created used the default XSL file provided with NMAP.</p>
			</td>
		</tr>
		<tr>
			<td><p>Select NMAP XML file: </p></td><td><input name="userfile" type="file" /></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Process File" />
				</form>
			</td>
		</tr>
	</table>
	</td>
</tr></table>

</body>
</html>

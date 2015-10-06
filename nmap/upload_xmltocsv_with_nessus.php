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
				<form enctype="multipart/form-data" action="xmltocsv_with_nessus.php" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000000" />
				<img src="images/nmap_logo.png"></img>
				<p>This is a custom parser that integrates results from Nessus with Nmap to get a more detailed and acurate results.  It is also custom to my own reporting.</p>
				<p>The NMAP XML file will be parsed and converted to a comma delimited format for further analysis in your favorite spreadsheet application.</p>
				<p>The output only shows hosts found to be up with open ports.</p>
			</td>
		</tr>
		<tr>
			<td><p>Select NMAP XML file: </p></td><td><input name="userfile" type="file" /></td>
		</tr>
		<tr><td><p>Enter Agency/Company Name: </p></td><td><input name="agency" type="text"></p></td></tr>
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

<html>
<head>
<title>UPLOAD KISMET SCAN</title>
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
				<form enctype="multipart/form-data" action="txt2kml.php" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000000" />
			</td>
		</tr>
		<tr>
			<td><p>Select text file: </p></td><td><input name="txtfile" type="file" /></td>
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
	</td>
</tr></table>

</body>
</html>

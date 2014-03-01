<html>
<head>
<title>IMPORT DUMPSEC TAB DELIMITED CSV FILE</title>
<style type="text/css">
p {font-size: 90%}
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
				<form enctype="multipart/form-data" action="parse.php" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000000" />
				<img src="images/dump_truck_icon.jpg"></img>
				<p>The DumpSec Tab Delimited CSV file will be uploaded, parsed, and added to the backend MySQL database.  Reports can then be generated from the information in the database.</p>
			</td>
		</tr>
		<tr>
			<td><p>Select csv file: </p></td><td><input name="userfile" type="file" /></td>
		</tr>
		<tr>
			<td><p>Enter Agency/Company Name: </p></td><td><input name="agency" type="text"></td>
		</tr>
		<tr>
			<td><p>File Type: </p></td><td>User:<input type="radio" value="user" name="filetype" checked>Group:<input type="radio" value="group" name="filetype"></td><tr>
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

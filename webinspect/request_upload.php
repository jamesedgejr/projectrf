<html>
<head>
<title>Upload Web Inspect - import into database</title>
<style type="text/css">
p {font-size: 70%}
a {text-decoration: none}
a:hover {text-decoration: underline}
</style>
</head>
<body>
<body>
<table width="100%"><tr>
	<td width="20%" valign="top">
	<?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">
<br><br><br>
<table cellspacing="5" cellpadding="5" width="500" align="center">
	<tr>
		<td valign="top"><img src="webinspect_logo.png"></td>
	<td>
		<form enctype="multipart/form-data" action="request_parse.php" method="POST">
		<!-- MAX_FILE_SIZE must precede the file input field -->
		<input type="hidden" name="MAX_FILE_SIZE" value="500000000" />
		<!-- Name of input element determines name in $_FILES array -->
		Upload Web Inspect xml: <input name="userfile" type="file" /><br>
		Enter Agency: <input name="agency" type="text"><br>
		Enter Application: <input name="application" type="text"><br>
		<input type="submit" value="Process File" />	
		</form>
	</td></tr>
</table>
</td></tr></table>
</body>
</html>

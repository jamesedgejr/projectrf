<html>
<head>
<title>Upload Web Inspect - import into database</title>
<style type="text/css">
p {font-size: 90%}
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
	<table cellspacing="5" cellpadding="5" width="500">
	<tr>
		<td valign="middle"><img src="webinspect_logo.png"></td>
	<td valign="top">
		<form enctype="multipart/form-data" action="parse_nist_update.php" method="POST">
		<!-- MAX_FILE_SIZE must precede the file input field -->
		<input type="hidden" name="MAX_FILE_SIZE" value="50000000" />
		<!-- Name of input element determines name in $_FILES array -->
		<p>Upload WebInspect NIST 800-53<br>Compliance HTML report<br><br><input name="userfile" type="file" /></p>
		<p><input type="submit" value="Process File" /></p>
		</form>
	</td></tr>
	<tr><td colspan="2"><p><b>Instructions:  </b>The file you need to upload is a saved HTML Nist 800-53 Compliance report from WebInspect.  This file is uploaded and parsed for the HP WebInspect vulnerability ID to NIST 800-53 control mapping.  Everytime WebInspect is updated with new vulnerability checks a compliance report should be created of the most recent scan and uploaded.  This does not have to be done after every scan.</p></td></tr>
</table>
</td></tr></table>
</body>
</html>

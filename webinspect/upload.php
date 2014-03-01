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
<form enctype="multipart/form-data" action="parse.php" method="POST">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="MAX_FILE_SIZE" value="2000000000" />
<table cellspacing="5" cellpadding="5" width="600" align="center">
	<tr>
		<td valign="top"><img src="webinspect_logo.png"></td>
		<td align="right">
			<p>Upload XML: <input type="radio" name="upload_choice" value="y" checked></p>
		</td>
		<td>
			<!-- Name of input element determines name in $_FILES array -->
			<p><input name="userfile" type="file"></p>
		</td>
	</tr>
	<tr>	
		<td></td>
		<td align="right">
			<p>Upload Location: <input type="radio" name="upload_choice" value="n"></p>
		</td>
		<td>
			<p><input name="upload_path" type="text"></p>
		</td>
	</tr>
	<tr>	
		<td></td>
		<td align="right">
			<p>Enter Agency:</p>
		</td>
		<td>
			<input name="agency" type="text">	
			
		</td>
	</tr>
	<tr>
		<td></td>
		<td align="right">	
			<p>Enter Application or Scan name:</p>
		</td>
		<td>
			<p><input name="application" type="text"></p>
		</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td>
			<p><input type="submit" value="Process File" /></p>
		</td>
	</tr>
	



</table>
</form>
</td></tr></table>
</body>
</html>

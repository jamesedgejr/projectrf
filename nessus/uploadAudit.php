<html>
<head><title>Completed upload of Nessus Compliance v2 XML file.</title>
<style type="text/css">
a {text-decoration: none}
a:hover {text-decoration: underline}
</style>
</head>
<body>
<table width="100%"><tr>
	<td width="20%" valign="top">
	<?php include '../main/menu.php'; ?>
	</td>
	<td valign="top">

	<table cellspacing="5" cellpadding="5" width="600">
		<tr>
			<td colspan="2">
				<form enctype="multipart/form-data" action="parseComplianceAudit.php" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000000" />
				<img src="images/nessus_logo.png"></img>
				<p>The Nessus .audit file is used in a compliance scan and is the only place where the Compliance Type can be found.  It will be uploaded and parsed adding this information to the database.</p>
			</td>
		</tr>
		<tr>
			<td><p>Select .audit compliance file: </p></td><td><input name="userfile" type="file" /></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Process File" />
				<input type="hidden" name="agency" value="<?php echo "$agency"; ?>">
				<input type="hidden" name="report_name" value="<?php echo "$report_name"; ?>">
				</form>
			</td>
		</tr>
	</table>
</td></tr></table>
</body>
</html>
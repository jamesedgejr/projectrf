<html>
<head><title>Completed upload of IBM Rational AppScan XML file.</title>
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

<?php
$agency = $_POST["agency"];
$uploaddir = sys_get_temp_dir();
$uploadfile = tempnam(sys_get_temp_dir(), basename($_FILES['userfile']['name']));
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
	echo "<hr><p align=\"center\"><b>File is valid, and was successfully uploaded.</b></p><hr>";
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

if(file_exists($uploadfile)) { 
	$reader = new XMLReader();
	$reader->open($uploadfile);
	echo "<p align=\"center\"><b>XML file successfully opened.</b></p>"; 
}
else { 
	echo "<p align=\"center\"><b>Failed to open the xml file.</b></p>"; 
	exit;
} 
echo "<hr>";

include('../main/config.php');
require_once( 'DB.php' );
$db = DB::connect( "mysql://$dbuser:$dbpass@$dbhost/$dbname" );

while ($reader->read()) {
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "XmlReport"){
		$reader->moveToAttribute('Name');
		$XmlReport_Name = mysql_prep($reader->value);
	}
	
/*--------------------------------------------------------------------------
		SUMMARY
--------------------------------------------------------------------------*/
	
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Summary"){
		$isSummary = "true";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "TotalIssues" && $isSummary == "true"){
		$reader->read();
		$TotalIssues = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "TotalVariants" && $isSummary == "true"){
		$reader->read();
		$TotalVariants = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "TotalRemediations" && $isSummary == "true"){
		$reader->read();
		$TotalRemediations = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "TotalScanDuration" && $isSummary == "true"){
		$reader->read();
		$TotalScanDuration = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Host" && $isSummary == "true"){
		$Host_Name = $Host_TotalInformationalIssues = $Host_TotalLowSeverityIssues = $Host_TotalMediumSeverityIssues = $Host_TotalHighSeverityIssues = $Host_Total = "";
		$reader->moveToAttribute('Name');
		$Host_Name = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "TotalInformationalIssues" && $isSummary == "true"){
		$reader->read();
		$Host_TotalInformationalIssues = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "TotalLowSeverityIssues" && $isSummary == "true"){
		$reader->read();
		$Host_TotalLowSeverityIssues = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "TotalMediumSeverityIssues" && $isSummary == "true"){
		$reader->read();
		$Host_TotalMediumSeverityIssues = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "TotalHighSeverityIssues" && $isSummary == "true"){
		$reader->read();
		$Host_TotalHighSeverityIssues = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Total" && $isSummary == "true"){
		$reader->read();
		$Host_Total = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Host" && $isSummary == "true"){
		$sql = "INSERT INTO AppScan_Summary (agency, XmlReport_Name, TotalIssues, TotalVariants, TotalRemediations, TotalScanDuration, Host_Name, Host_TotalInformationalIssues, Host_TotalLowSeverityIssues, Host_TotalMediumSeverityIssues, Host_TotalHighSeverityIssues, Host_Total) VALUES ('$agency', '$XmlReport_Name', '$TotalIssues', '$TotalVariants', '$TotalRemediations', '$TotalScanDuration', '$Host_Name', '$Host_TotalInformationalIssues', '$Host_TotalLowSeverityIssues', '$Host_TotalMediumSeverityIssues', '$Host_TotalHighSeverityIssues', '$Host_Total')";
		$result = $db->query($sql);ifDBError($result);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Summary" && $isSummary == "true"){
		$isSummary = "false";
	}
	
/*--------------------------------------------------------------------------
		REMEDIATION TYPES
--------------------------------------------------------------------------*/
	
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "RemediationTypes"){
		$isRemediationTypes = "true";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Total"  && $isRemediationTypes == "true"){
		$reader->read();
		$Total = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "RemediationType"){
		$isRemediationType = "true";
		$RemediationType_ID = $Name = $Priority = $fixRecommendation = $fixRecommendation_type = "";
		$reader->moveToAttribute('ID');
		$RemediationType_ID = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Name" && $isRemediationType == "true"){
		$reader->read();
		$Name = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Priority" && $isRemediationType == "true"){
		$reader->read();
		$Priority = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "fixRecommendation" && $isRemediationType == "true"){
		$isFixRecommendation = "true";
		$reader->moveToAttribute('type');
		$fixRecommendation_type = mysql_prep($reader->value);
		$fixRecommendation = mysql_prep("<p>");
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "text" && $isFixRecommendation == "true" && $isRemediationType == "true"){
		$reader->read();
		$fixRecommendation .= mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "code" && $isFixRecommendation == "true" && $isRemediationType == "true"){
		$fixRecommendation .= mysql_prep("<pre>");
		$reader->read();
		$fixRecommendation .= mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "indentText" && $isFixRecommendation == "true" && $isRemediationType == "true"){
		$fixRecommendation .= mysql_prep("<pre>");
		$reader->read();
		$fixRecommendation .= mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "br" && $isFixRecommendation == "true" && $isRemediationType == "true"){
		$fixRecommendation .= mysql_prep("<br>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "code" && $isFixRecommendation == "true" && $isRemediationType == "true"){
		$fixRecommendation .= mysql_prep("</pre>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "indentText" && $isFixRecommendation == "true" && $isRemediationType == "true"){
		$fixRecommendation .= mysql_prep("</pre>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "fixRecommendation" && $isFixRecommendation == "true" && $isRemediationType == "true"){
		$isFixRecommendation = "false";
		$fixRecommendation .= mysql_prep("</p>");
		$fixRecommendation = mysql_prep($fixRecommendation);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "RemediationType" && $isRemediationType == "true"){
		$isRemediationType = "false";		
		//echo "$agency, $XmlReport_Name, $Total, $RemediationType_ID, $Name, $Priority, $fixRecommendation, $fixRecommendation_type<br>";
		$sql = "INSERT INTO AppScan_RemediationTypes (agency, XmlReport_Name, Total, RemediationType_ID, Name, Priority, fixRecommendation, fixRecommendation_type) VALUES ('$agency', '$XmlReport_Name', '$Total', '$RemediationType_ID', '$Name', '$Priority', '$fixRecommendation', '$fixRecommendation_type')";
		$result = $db->query($sql);ifDBError($result);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "RemediationTypes"  && $isRemediationTypes == "true"){
		$isRemediationTypes = "false";
	}


/*--------------------------------------------------------------------------
		ISSUE TYPES
--------------------------------------------------------------------------*/
	
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "IssueTypes"){
		$isIssueTypes = "true";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Total"  && $isIssueTypes == "true"){
		$reader->read();
		$Total = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "IssueType"){
		$isIssueType = "true";
		$IssueType_ID = $IssueType_Count = $RemediationID = 
		$advisory_name = $advisory_testDescription = $threatClassification_name = 
		$threatClassification_reference = $testTechnicalDescription = $causes = 
		$securityRisks = $affectedProducts = $linkName = $linkTarget = 
		$fixRecommendation_type = $fixRecommendation = $Severity = $EntityType =
		$Invasive = "";
			$reader->moveToAttribute('ID');
			$IssueType_ID = mysql_prep($reader->value);
			$reader->moveToAttribute('Count');
			$IssueType_Count = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "RemediationID"  && $isIssueType == "true"){
		$reader->read();
		$RemediationID = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "advisory"  && $isIssueType == "true"){
		$isAdvisory = "true";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "name"  && $isAdvisory == "true"){
		$reader->read();
		$advisory_name = mysql_prep($reader->value);
	}
/*TEST DESCRIPTION*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "testDescription"  && $isAdvisory == "true"){
		$reader->read();
		$advisory_testDescription = mysql_prep($reader->value);
	}
/*THREAT CLASSIFICATION*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "threatClassification"  && $isAdvisory == "true"){
		$reader->read();$reader->read();$reader->read();
		$threatClassification_name = mysql_prep($reader->value);
		$reader->read();$reader->read();$reader->read();$reader->read();
		$threatClassification_reference = mysql_prep($reader->value);
	}
/*TEST TECHNICAL DESCRIPTION*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "testTechnicalDescription" && $isAdvisory == "true"){
		$isTestTechnicalDescription = "true";
		$reader->moveToAttribute('type');
		$testTechnicalDescription_type = mysql_prep($reader->value);
		$testTechnicalDescription = mysql_prep("<p>");
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "text" && $isTestTechnicalDescription == "true"){
		$reader->read();
		$testTechnicalDescription .= mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "indentText" && $isTestTechnicalDescription == "true"){
		$testTechnicalDescription .= mysql_prep("<pre>");
		$reader->read();
		$testTechnicalDescription .= mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "code" && $isTestTechnicalDescription == "true"){
		$testTechnicalDescription .= mysql_prep("<pre>");
		$reader->read();
		$testTechnicalDescription .= mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "br" && $isTestTechnicalDescription == "true"){
		$testTechnicalDescription .= mysql_prep("<br>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "code" && $isTestTechnicalDescription == "true"){
		$testTechnicalDescription .= mysql_prep("</pre>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "indentText" && $isTestTechnicalDescription == "true"){
		$testTechnicalDescription .= mysql_prep("</pre>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "testTechnicalDescription" && $isTestTechnicalDescription == "true"){
		$isTestTechnicalDescription = "false";
		$testTechnicalDescription .= mysql_prep("</p>");
		$testTechnicalDescription = mysql_prep($testTechnicalDescription);
	}	
/*CAUSES*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "causes"  && $isAdvisory == "true"){
		$isCauses = "true";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "cause"  && $isCauses == "true"){
		$reader->read();
		$causes .= mysql_prep($reader->value);
		$causes .= mysql_prep("<br>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "causes"  && $isAdvisory == "true"){
		$isCauses = "false";
		$causes = mysql_prep($causes);
	}
/*SECURITY RISKS*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "securityRisks"  && $isAdvisory == "true"){
		$isSecurityRisks = "true";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "securityRisk"  && $isSecurityRisks == "true"){
		$reader->read();
		$securityRisks .= mysql_prep($reader->value);
		$securityRisks .= mysql_prep("<br>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "securityRisks"  && $isAdvisory == "true"){
		$isSecurityRisks = "false";
		$securityRisks = mysql_prep($securityRisks);
	}
/*AFFECTED PRODUCTS*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "affectedProducts"  && $isAdvisory == "true"){
		$isAffectedProducts = "true";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "affectedProduct"  && $isAffectedProducts == "true"){
		$reader->read();
		$affectedProducts .= mysql_prep($reader->value);
		$affectedProducts .= mysql_prep("<br>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "affectedProducts"  && $isAdvisory == "true"){
		$isAffectedProducts = "false";
		$affectedProducts = addslashes($affectedProducts);
	}
/*REFERENCES - LINKS*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "references"  && $isAdvisory == "true"){
		$isReferences = "true";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "link"  && $isReferences == "true"){
		$reader->moveToAttribute('target');
		$linkTarget .= mysql_prep($reader->value);
		$linkTarget .= ",";
		$reader->moveToElement();
		$reader->read();
		$linkName .= mysql_prep($reader->value);
		$linkName .= ",";
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "references"  && $isReferences == "true"){
		$isReferences = "false";
		$linkName = mysql_prep($linkName);
		$linkTarget = mysql_prep($linkTarget);
	}
/*FIX RECOMMENDATION*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "fixRecommendation" && $isAdvisory == "true"){
		$isFixRecommendation = "true";
		$reader->moveToAttribute('type');
		$fixRecommendation_type = mysql_prep($reader->value);
		$fixRecommendation = mysql_prep("<p>");
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "text" && $isFixRecommendation == "true" && $isAdvisory == "true"){
		$reader->read();
		$fixRecommendation .= mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "code" && $isFixRecommendation == "true" && $isAdvisory == "true"){
		$fixRecommendation .= mysql_prep("<pre>");
		$reader->read();
		$fixRecommendation .= mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "indentText" && $isFixRecommendation == "true" && $isAdvisory == "true"){
		$fixRecommendation .= mysql_prep("<pre>");
		$reader->read();
		$fixRecommendation .= mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "br" && $isFixRecommendation == "true" && $isAdvisory == "true"){
		$fixRecommendation .= mysql_prep("<br>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "code" && $isFixRecommendation == "true" && $isAdvisory == "true"){
		$fixRecommendation .= mysql_prep("</pre>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "indentText" && $isFixRecommendation == "true" && $isAdvisory == "true"){
		$fixRecommendation .= mysql_prep("</pre>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "fixRecommendation" && $isFixRecommendation == "true" && $isAdvisory == "true"){
		$isFixRecommendation = "false";
		$fixRecommendation .= mysql_prep("</p>");
		$fixRecommendation = addslashes($fixRecommendation);
	/*---------------------------------------------------
		There are multiple fixRecommendations based on 
		type with a LARGE amount of information.  
		I will have to INSERT what I have so far
		into the database and then UPDATE when I have
		the rest of the information.  BULLSHIT AppSCAN!
	---------------------------------------------------*/
		$sql = "INSERT INTO AppScan_IssueTypes (agency, XmlReport_Name, IssueType_ID, IssueType_Count, RemediationID, advisory_name, advisory_testDescription, threatClassification_name, threatClassification_reference, testTechnicalDescription, causes, securityRisks, affectedProducts, linkName, linkTarget, fixRecommendation_type, fixRecommendation, Severity, EntityType, Invasive) VALUES ('$agency', '$XmlReport_Name', '$IssueType_ID', '$IssueType_Count', '$RemediationID', '$advisory_name', '$advisory_testDescription', '$threatClassification_name', '$threatClassification_reference', '$testTechnicalDescription', '$causes', '$securityRisks', '$affectedProducts', '$linkName', '$linkTarget', '$fixRecommendation_type', '$fixRecommendation', '$Severity', '$EntityType', '$Invasive')";
		$result = $db->query($sql);ifDBError($result);
	}
/*END OF THE ADVISORY*/
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "advisory"  && $isAdvisory == "true"){
		$isAdvisory = "false";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Severity"  && $isIssueType == "true"){
		$reader->read();
		$Severity = mysql_prep($reader->value);
		switch ($Severity) {
			case "High":
				$Severity_number = "3";
				break;
			case "Medium":
				$Severity_number = "2";
				break;
			case "Low":
				$Severity_number = "1";
				break;
			case "Informational":
				$Severity_number = "0";
				break;
		}
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "EntityType"  && $isIssueType == "true"){
		$reader->read();
		$EntityType = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Invasive"  && $isIssueType == "true"){
		$reader->read();
		$Invasive = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "IssueType"  && $isIssueType == "true"){
		$isIssueType = "false";
		$sql = "UPDATE AppScan_IssueTypes SET Severity='$Severity', Severity_number='$Severity_number', EntityType='$EntityType', Invasive='$Invasive' WHERE agency='$agency' AND XmlReport_Name='$XmlReport_Name' AND IssueType_ID='$IssueType_ID' AND RemediationID='$RemediationID' AND advisory_name='$advisory_name'";
		$result = $db->query($sql);ifDBError($result);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "IssueTypes"){
		$isIssueTypes = "false";
	}

/*--------------------------------------------------------------------------
		ISSUES
--------------------------------------------------------------------------*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Issues"){
		$isIssues = "true";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Issue" && $isIssues == "true"){
		$isIssue = "true";
		$Issue_IssueTypeID = $Issue_Noise = $Url = $Entity = $Variant_ID = $Comments = 
		$Difference = $Reasoning = $Validation_Location = $Validation_Length = 
		$Validation_String = $OriginalHttpTraffic = $TestHttpTraffic = "";
			$reader->moveToAttribute('IssueTypeID');
			$Issue_IssueTypeID = mysql_prep($reader->value);
			$reader->moveToAttribute('Noise');
			$Issue_Noise = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Url"  && $isIssue == "true"){
		$reader->read();
		$Url = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Entity"  && $isIssue == "true"){
		$reader->read();
		$Entity = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Variant" && $isIssue == "true"){
		$isVariant = "true";
		$reader->moveToAttribute('ID');
		$VariantID = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Comments" && $isIssue == "true" && $isVariant == "true"){
		$reader->read();
		$Comments = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Difference" && $isIssue == "true" && $isVariant == "true"){
		$reader->read();
		$Difference = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Reasoning" && $isIssue == "true" && $isVariant == "true"){
		$reader->read();
		$Reasoning = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "ValidationDataLocationAtTestResponse" && $isIssue == "true" && $isVariant == "true"){
		$isValidationDataLocationAtTestResponse = "true";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Validation" && $isIssue == "true" && $isVariant == "true" && $isValidationDataLocationAtTestResponse == "true"){
		$reader->moveToAttribute('Location');
		$Validation_Location .= mysql_prep($reader->value) . ",";
		$reader->moveToAttribute('Length');
		$Validation_Length .= mysql_prep($reader->value) . ",";
		$reader->moveToAttribute('String');
		$Validation_String .= mysql_prep($reader->value) . ",";
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "ValidationDataLocationAtTestResponse" && $isIssue == "true" && $isVariant == "true"){
		$isValidationDataLocationAtTestResponse = "false";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "OriginalHttpTraffic" && $isIssue == "true" && $isVariant == "true"){
		$reader->read();
		$OriginalHttpTraffic = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "TestHttpTraffic" && $isIssue == "true" && $isVariant == "true"){
		$reader->read();
		$TestHttpTraffic = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Variant" && $isIssue == "true" && $isVariant == "true"){
		$isVariant = "false";
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Issue" && $isIssue == "true"){
		$isIssue = "false";
		$sql = "INSERT INTO AppScan_Issues (agency, XmlReport_Name, Issue_IssueTypeID, Issue_Noise, Url, Entity, Variant_ID, Comments, Difference, Reasoning, Validation_Location, Validation_Length, Validation_String, OriginalHttpTraffic, TestHttpTraffic) VALUES ('$agency', '$XmlReport_Name', '$Issue_IssueTypeID', '$Issue_Noise', '$Url', '$Entity', '$Variant_ID', '$Comments', '$Difference', '$Reasoning', '$Validation_Location', '$Validation_Length', '$Validation_String', '$OriginalHttpTraffic', '$TestHttpTraffic')";
		$result = $db->query($sql);ifDBError($result);
		
	}
/*--------------------------------------------------------------------------
		APPLICATION DATA
--------------------------------------------------------------------------*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "ApplicationData"){
		$isApplicationData = "true";
	}
/*COOKIES*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Cookies" && $isApplicationData == "true"){
		$isCookies = "true";
		$reader->read();$reader->read();$reader->read();
		$Cookies_Total = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Cookie" && $isApplicationData == "true" && $isCookies == "true"){
		$isCookie = "true";
		$Cookie_Value = $Cookie_FirstSetInUrl = $Cookie_FirstRequestedUrl = $Cookie_Domain = $Cookie_Expires = $Cookie_Secure = $Cookie_Name = "";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Value" && $isApplicationData == "true" && $isCookies == "true" && $isCookie == "true"){
		$reader->read();
		$Cookie_Value = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "FirstSetInUrl" && $isApplicationData == "true" && $isCookies == "true" && $isCookie == "true"){
		$reader->read();
		$Cookie_FirstSetInUrl = mysql_prep($reader->value);	
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "FirstRequestedInUrl" && $isApplicationData == "true" && $isCookies == "true" && $isCookie == "true"){
		$reader->read();
		$Cookie_FirstRequestedInUrl = mysql_prep($reader->value);	
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Domain" && $isApplicationData == "true" && $isCookies == "true" && $isCookie == "true"){
		$reader->read();
		$Cookie_Domain = mysql_prep($reader->value);	
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Expires" && $isApplicationData == "true" && $isCookies == "true" && $isCookie == "true"){
		$reader->read();
		$Cookie_Expires = mysql_prep($reader->value);	
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Secure" && $isApplicationData == "true" && $isCookies == "true" && $isCookie == "true"){
		$reader->read();
		$Cookie_Secure = mysql_prep($reader->value);	
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Name" && $isApplicationData == "true" && $isCookies == "true" && $isCookie == "true"){
		$reader->read();
		$Cookie_Name = mysql_prep($reader->value);	
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Cookie" && $isApplicationData == "true" && $isCookies == "true" && $isCookie == "true"){
		$isCookie = "false";
		$sql = "INSERT INTO AppScan_ApplicationData_Cookies (agency, XmlReport_Name, Cookies_Total, Cookie_Value, Cookie_FirstSetInUrl, Cookie_FirstRequestedUrl, Cookie_Domain, Cookie_Expires, Cookie_Secure, Cookie_Name) VALUES ('$agency', '$XmlReport_Name', '$Cookies_Total', '$Cookie_Value', '$Cookie_FirstSetInUrl', '$Cookie_FirstRequestedUrl', '$Cookie_Domain', '$Cookie_Expires', '$Cookie_Secure', '$Cookie_Name')";
		$result = $db->query($sql);ifDBError($result);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Cookies" && $isApplicationData == "true" && $isCookies == "true"){
		$isCookies = "false";
	}
/*JAVASCRIPT*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "JavaScripts" && $isApplicationData == "true"){
		$isJavaScripts = "true";
		$reader->read();$reader->read();$reader->read();
		$JavaScripts_Total = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "JavaScript" && $isApplicationData == "true" && $isJavaScripts == "true"){
		$isJavaScript = "true";
		$JavaScript_Text = $JavaScript_Url = "";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Text" && $isApplicationData == "true" && $isJavaScripts == "true" && $isJavaScript == "true"){
		$reader->read();
		$JavaScript_Text = mysql_prep($reader->value);	
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Url" && $isApplicationData == "true" && $isJavaScripts == "true" && $isJavaScript == "true"){
		$reader->read();
		$JavaScript_Url = mysql_prep($reader->value);	
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "JavaScript" && $isApplicationData == "true" && $isJavaScripts == "true" && $isJavaScript == "true"){
		$isJavaScript = "false";
		$sql =  "INSERT INTO AppScan_ApplicationData_JavaScripts (agency, XmlReport_Name, JavaScripts_Total, JavaScript_Text, JavaScript_Url) VALUES ('$agency', '$XmlReport_Name', '$JavaScripts_Total', '$JavaScript_Text', '$JavaScript_Url')";
		$result = $db->query($sql);ifDBError($result);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "JavaScripts" && $isApplicationData == "true" && $isJavaScripts == "true"){
		$isJavaScripts = "false";
	}
/*COMMENTS*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Comments" && $isApplicationData == "true"){
		$isComments = "true";
		$reader->read();$reader->read();$reader->read();
		$Comments_Total = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Comment" && $isApplicationData == "true" && $isComments == "true"){
		$isComment = "true";
		$Comment_Text = $Comment_Url = "";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Text" && $isApplicationData == "true" && $isComments == "true" && $isComment == "true"){
		$reader->read();
		$Comment_Text = mysql_prep($reader->value);	
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Url" && $isApplicationData == "true" && $isComments == "true" && $isComment == "true"){
		$reader->read();
		$Comment_Url = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Comment" && $isApplicationData == "true" && $isComments == "true" && $isComment == "true"){
		$isComment = "false";
		$sql =  "INSERT INTO AppScan_ApplicationData_Comments (agency, XmlReport_Name, Comments_Total, Comment_Text, Comment_Url) VALUES ('$agency', '$XmlReport_Name', '$Comments_Total', '$Comment_Text', '$Comment_Url')";
		$result = $db->query($sql);ifDBError($result);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Comments" && $isApplicationData == "true" && $isComments == "true"){
		$isComments = "false";
	}
/*SCRIPT PARAMETERS*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "ScriptParameters" && $isApplicationData == "true"){
		$isScriptParameters = "true";
		$reader->read();$reader->read();$reader->read();
		$ScriptParameters_Total = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "ScriptParameter" && $isApplicationData == "true" && $isScriptParameters == "true"){
		$isScriptParameter = "true";
		$ScriptParameter_Name = $ScriptParameter_Values = $ScriptParameter_Url = $ScriptParameter_Type = "";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Name"  && $isApplicationData == "true" && $isScriptParameters == "true" && $isScriptParameter == "true"){
		$reader->read();
		$ScriptParameter_Name = mysql_prep($reader->value);	
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Values"  && $isApplicationData == "true" && $isScriptParameters == "true" && $isScriptParameter == "true"){
		$isValues = "true";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Value"  && $isValues == "true" && $isApplicationData == "true" && $isScriptParameters == "true" && $isScriptParameter == "true"){
		$reader->read();
		$ScriptParameter_Values .= mysql_prep($reader->value);
		$ScriptParameter_Values .= mysql_prep("<br>");
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "Values"  && $isScriptParameter == "true"){
		$isValues = "false";
		$Values = mysql_prep($Values);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Url" && $isApplicationData == "true" && $isScriptParameters == "true" && $isScriptParameter == "true"){
		$reader->read();
		$ScriptParameter_Url = mysql_prep($reader->value);	
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Type" && $isApplicationData == "true" && $isScriptParameters == "true" && $isScriptParameter == "true"){
		$reader->read();
		$ScriptParameter_Type = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "ScriptParameter" && $isApplicationData == "true" && $isScriptParameters == "true" && $isScriptParameter == "true"){
		$isScriptParameter = "false";
		$sql =  "INSERT INTO AppScan_ApplicationData_ScriptParameters (agency, XmlReport_Name, ScriptParameters_Total, ScriptParameter_Name, ScriptParameter_Values, ScriptParameter_Url, ScriptParameter_Type) VALUES ('$agency', '$XmlReport_Name', '$ScriptParameters_Total', '$ScriptParameter_Name', '$ScriptParameter_Values', '$ScriptParameter_Url', '$ScriptParameter_Type')";
		$result = $db->query($sql);ifDBError($result);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "ScriptParameters" && $isApplicationData == "true" && $isScriptParameters == "true"){
		$isScriptParameters = "false";
	}
/*VISITED LINK*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "VisitedLinks" && $isApplicationData == "true"){
		$isVisitedLinks = "true";
		$reader->read();$reader->read();$reader->read();
		$VisitedLinks_Total = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "VisitedLink" && $isApplicationData == "true" && $isVisitedLinks == "true"){
		$isVisitedLink = "true";
		$VisitedLink_Url = "";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Url" && $isApplicationData == "true" && $isVisitedLinks == "true" && $isVisitedLink == "true"){
		$reader->read();
		$VisitedLink_Url = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "VisitedLink" && $isApplicationData == "true" && $isVisitedLinks == "true" && $isVisitedLink == "true"){
		$isVisitedLink = "false";
		$sql =  "INSERT INTO AppScan_ApplicationData_VisitedLinks (agency, XmlReport_Name, VisitedLinks_Total, VisitedLink_Url) VALUES ('$agency', '$XmlReport_Name', '$VisitedLinks_Total', '$VisitedLink_Url')";
		$result = $db->query($sql);ifDBError($result);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "VisitedLinks" && $isApplicationData == "true" && $isVisitedLinks == "true"){
		$isVisitedLinks = "false";
	}
/*BROKEN LINK*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "BrokenLinks" && $isApplicationData == "true"){
		$isBrokenLinks = "true";
		$reader->read();$reader->read();$reader->read();
		$BrokenLinks_Total = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "BrokenLink" && $isApplicationData == "true" && $isBrokenLinks == "true"){
		$isBrokenLink = "true";
		$BrokenLink_Reason = $BrokenLink_Url = "";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Reason" && $isApplicationData == "true" && $isBrokenLinks == "true" && $isBrokenLink == "true"){
		$reader->read();
		$BrokenLink_Reason = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Url" && $isApplicationData == "true" && $isBrokenLinks == "true" && $isBrokenLink == "true"){
		$reader->read();
		$BrokenLink_Url = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "BrokenLink" && $isApplicationData == "true" && $isBrokenLinks == "true" && $isBrokenLink == "true"){
		$isBrokenLink = "false";
		$sql =  "INSERT INTO AppScan_ApplicationData_BrokenLinks (agency, XmlReport_Name, BrokenLinks_Total, BrokenLink_Reason, BrokenLink_Url) VALUES ('$agency', '$XmlReport_Name', '$BrokenLinks_Total', '$BrokenLink_Reason', '$BrokenLink_Url')";
		$result = $db->query($sql);ifDBError($result);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "BrokenLinks" && $isApplicationData == "true" && $isBrokenLinks == "true"){
		$isBrokenLinks = "false";
	}
/*FILTERED LINKS*/
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "FilteredLinks" && $isApplicationData == "true"){
		$isFilteredLinks = "true";
		$reader->read();$reader->read();$reader->read();
		$FilteredLinks_Total = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "FilteredLink" && $isApplicationData == "true" && $isFilteredLinks == "true"){
		$isFilteredLink = "true";
		$FilteredLink_Reason = $FilteredLink_Url = "";
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Reason" && $isApplicationData == "true" && $isFilteredLinks == "true" && $isFilteredLink == "true"){
		$reader->read();
		$FilteredLink_Reason = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::ELEMENT && $reader->name == "Url" && $isApplicationData == "true" && $isFilteredLinks == "true" && $isFilteredLink == "true"){
		$reader->read();
		$FilteredLink_Url = mysql_prep($reader->value);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "FilteredLink" && $isApplicationData == "true" && $isFilteredLinks == "true" && $isFilteredLink == "true"){
		$isFilteredLink = "false";
		$sql =  "INSERT INTO AppScan_ApplicationData_FilteredLinks (agency, XmlReport_Name, FilteredLinks_Total, FilteredLink_Reason, FilteredLink_Url) VALUES ('$agency', '$XmlReport_Name', '$FilteredLinks_Total', '$FilteredLink_Reason', '$FilteredLink_Url')";
		$result = $db->query($sql);ifDBError($result);
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "FilteredLinks" && $isApplicationData == "true" && $isFilteredLinks == "true"){
		$isFilteredLinks = "false";
	}
	if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == "ApplicationData" && $isApplicationData == "true"){
		$isApplicationData = "false";
	}
}//end reader while loop


?>
</td></tr></table>
</body>
</html>
<?php 

function ifDBError($error)
{
	if (PEAR::isError($error)) {
		echo 'Standard Message: ' . $error->getMessage() . "</br>";
		echo 'Standard Code: ' . $error->getCode() . "</br>";
		echo 'DBMS/User Message: ' . $error->getUserInfo() . "</br>";
		echo 'DBMS/Debug Message: ' . $error->getDebugInfo() . "</br>";
		exit;
	}
}
function mysql_prep($value)
{
    if(get_magic_quotes_gpc()){
        $value = stripslashes($value);
    } else {
		$value = htmlspecialchars($value, ENT_QUOTES);
        $value = addslashes($value);
    }
    return $value;
} 


?>
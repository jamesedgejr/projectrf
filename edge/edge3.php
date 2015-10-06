<?php
//Custom Report 1
//this custom report is for my current employer.  If it helps you than cool.

include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v = new Valitron\Validator($_POST);
$v->rule('accepted', ['isSSLIssues','isRDPIssues','isSMBIssues','isCleartext','isAllIssues']);
$v->rule('slug','agency');
if(!$v->validate()) {
    print_r($v->errors());
	exit;
} 

$agency_temp = explode("xxxxXXXXxxxx", $_POST["agency"]);
$agency = $agency_temp[0];
$scan_id = $agency_temp[1];
$severity = $_POST["severity"];
date_default_timezone_set('UTC');
$date = date('mdYHis');
$myDir = getcwd() . "/csvfiles/";
$vuln_table_filename = $agency . "_vuln_table_" . $date . ".csv";
$vuln_table_file = $myDir . $vuln_table_filename;
$fh_vuln = fopen($vuln_table_file, 'w') or die("can't open $vuln_table_file for writing.  Please check folder permissions.");

$exposure_rating_filename = $agency . "_exposure_table_" . $date . ".csv";
$exposure_rating_file = $myDir . $exposure_rating_filename;
$fh_exposure = fopen($exposure_rating_file, 'w') or die("can't open $exposure_rating_file for writing.  Please check folder permissions.");

$vuln_cat = $_POST["vuln_cat"];
if($vuln_cat == "isAllIssues"){$isAllIssues = "yes";}
$vuln_id_array = array();
$serviceList_array = array();
$servicePort_array = array();

if($vuln_cat == "isOutdatedOS"){
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"Exposure of unsupported operating systems increase the potential for vulnerabilities for which the operating system vendor will not provide a response.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"MEDIUM\",\"Although the identified system does not currently expose an exploitable flaw, zero-day exploits may become available that will increase the potential for compromise.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"MEDIUM\",\"6\"\n");
	array_push($vuln_id_array, "mssql-obsolete-version");
}
if($vuln_cat == "isSSLIssues"){
	fwrite($fh_exposure, "\"MEDIUM\",\"SSL Isues\",\"Weaknesses in SSL encryption or problems with the third-party trust associated with SSL negotiation can threaten the integrity of encrypted sessions and the confidentiality of encrypted data.\"\n\n");
	fwrite($fh_exposure, "\"Severity\",\"MEDIUM\",\"Weaknesses in SSL encryption or problems with the third-party trust associated with SSL negotiation can threaten the integrity of encrypted sessions and the confidentiality of encrypted data.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"LOW\",\"Attacks against SSL encryption require a high degree of expertise to perform.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"MEDIUM\",\"4\"\n");
	array_push($vuln_id_array, 
					"rc4-cve-2013-2566",
					"ssl-rsa-export-ciphers",
					"ssl-self-signed-certificate",
					"sslv2-and-up-enabled",
					"sslv3-supported",
					"ssl-weak-ciphers",
					"tls-server-cert-expired",
					"tls-server-cert-sig-alg-md5",
					"tls-server-cert-sig-alg-sha1",
					"tls-untrusted-ca",
					"dns-bind-ssl-signature-spoofing",
					"http-openssl-cve-2014-0224",
					"php-cve-2009-3291",
					"php-cve-2011-1468",
					"php-cve-2013-4248",
					"vmsa-2014-0006-cve-2010-5298",
					"vmsa-2014-0006-cve-2014-0195",
					"vmsa-2014-0006-cve-2014-0198",
					"vmsa-2014-0006-cve-2014-0221",
					"vmsa-2014-0006-cve-2014-0224",
					"vmsa-2014-0006-cve-2014-3470"
	);

}
if($vuln_cat == "isSSHIssues"){
	fwrite($fh_exposure, "\"LOW\",\"SSH Isues\",\"Weaknesses in SSH encryption or problems with the third-party trust associated with SSH negotiation can threaten the integrity of encrypted sessions and the confidentiality of encrypted data.\"\n\n");
	fwrite($fh_exposure, "\"Severity\",\"MEDIUM\",\"Weaknesses in SSH encryption or problems with the third-party trust associated with SSH negotiation can threaten the integrity of encrypted sessions and the confidentiality of encrypted data.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"LOW\",\"Attacks against SSH encryption require a high degree of expertise to perform.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"LOW\",\"3\"\n");
	array_push($vuln_id_array, "");
}
if($vuln_cat == "isRDPIssues"){
	fwrite($fh_exposure, "\"Severity\",\"MEDIUM\",\"Exploitation of the protocol weaknesses can lead to access to affected systems.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"LOW\",\"An attacker with a high level of expertise is required to exploit the man-in-the-middle vulnerabilities.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"MEDIUM\",\"4\"\n");
	array_push($vuln_id_array, "");
}
if($vuln_cat == "isSMBIssues"){
	fwrite($fh_exposure, "\"Severity\",\"MEDIUM\",\"Enable and require SMB signing.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"LOW\",\"Exploit code is available but requires significant expertise and effort to configure and successfully deploy.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"MEDIUM\",\"4\"\n");
	array_push($vuln_id_array, 
				"CIFS-NT-0001",
				"CIFS-NT-0002",
				"cifs-share-world-readable",
				"cifs-share-world-writeable"

	);
}
if($vuln_cat == "isHPIssues"){
	fwrite($fh_exposure, "\"HIGH\",\"HP System Management Homepage (Multiple Vulnerabilities)\",\"The version of HP System Management Homepage (SMH) hosted on the remote host is affected by buffer overflow, Cross-site Scripting (XSS), and Denial of Service (DoS) vulnerabilities in the web server, PHP web application code, and OpenSSL implementation.\"\n\n");
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"Readily available exploit code exists for the outdated versions of this software.  The available code can potentially provide access to the system or cause a Denial of Service.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"HIGH\",\"Attacks against the service can lead to compromise of the server and system level access.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"HIGH\",\"9\"\n");
	array_push($vuln_id_array, "");
}
if($vuln_cat == "isMSIssues"){
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"The vulnerabilities are vulnerable to code execution without user interaction. \"\n");
	fwrite($fh_exposure, "\"Threat\",\"HIGH\",\"Scenarios include self-propagating malware (e.g. network worms), or unavoidable common use scenarios where code execution occurs without warnings or prompts.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"HIGH\",\"10	\"\n");
	array_push($vuln_id_array, 	"WINDOWS-HOTFIX-MS03-026",
								"WINDOWS-HOTFIX-MS03-039",
								"WINDOWS-HOTFIX-MS06-035",
								"WINDOWS-HOTFIX-MS08-040",
								"WINDOWS-HOTFIX-MS08-067",
								"WINDOWS-HOTFIX-MS09-001",
								"WINDOWS-HOTFIX-MS09-004",
								"WINDOWS-HOTFIX-MS09-062",
								"WINDOWS-HOTFIX-MS10-012",
								"WINDOWS-HOTFIX-MS10-054",
								"WINDOWS-HOTFIX-MS11-020",
								"WINDOWS-HOTFIX-MS12-020",
								"WINDOWS-HOTFIX-MS15-034"
				);
}
if($vuln_cat == "isDatabaseIssues"){
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"\"\n");
	fwrite($fh_exposure, "\"Threat\",\"HIGH\",\"\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"HIGH\",\"10\"\n");
	array_push($vuln_id_array, 
							"mssql-obsolete-version",
							"database-open-access",
							"dns-processes-recursive-queries",
							"dns-allows-cache-snooping",
							"ldap-anonymous-directory-access"
	);
}
if($vuln_cat == "isCleartext"){
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"Failure to encrypt the authentication process can result in unencrypted exposure of transmitted credentials.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"MEDIUM\",\"Attacks involving interception of encrypted credentials are likely to require a moderate degree of sophistication, but tools exist that automate such attacks.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"MEDIUM\",\"6\"\n");
	array_push($vuln_id_array, "http-basic-auth-cleartext",
							"ftp-plaintext-auth",
							"service-rlogin",
							"service-rsh",
							"snmp-cleartext-credential",
							"telnet-open-port"
	);
}

if($vuln_cat == "isPHPIssues"){
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"\"\n");
	fwrite($fh_exposure, "\"Threat\",\"HIGH\",\"\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"HIGH\",\"8\"\n");
	array_push($vuln_id_array, "");
}
if($vuln_cat == "isInformationLeaks"){
	fwrite($fh_exposure, "\"LOW\",\"Information Leakage\",\"Leaking of sensitive information through error messages, misconfiguration, and vulnerable code provides information to an attacker can use to form a plan of attack or assist in a compromise of an organization's data and systems.\"\n\n");
	fwrite($fh_exposure, "\"Severity\",\"LOW\",\"Information leakage does not lead directly to a compromise and can only aid in a potential attack.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"HIGH\",\"Attackers look to identify as much information about a target when formulating a plan of attack.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"LOW\",\"3\"\n");
	array_push($vuln_id_array, "");
}



$sql = "CREATE temporary TABLE nexpose_tmp_vulnerabilities (vuln_id VARCHAR(255), INDEX ndx_vuln_id (vuln_id))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($vuln_id_array as $vIA){
	$sql="INSERT INTO nexpose_tmp_vulnerabilities (vuln_id) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($vIA));
}

//get the IP address and vulnerable port number for the plugins
$nexpose_lookup_sql = "SELECT DISTINCT
								nexpose_nodes.node_address,
								nexpose_nodes.node_name,
								nexpose_nodes.node_device_id,
								nexpose_endpoints.endpoint_id,
								nexpose_endpoints.endpoint_protocol,
								nexpose_endpoints.endpoint_port,
								nexpose_endpoints.service_name
							FROM
								nexpose_tests
								Inner Join nexpose_nodes ON nexpose_tests.device_id = nexpose_nodes.node_device_id
								Left Join nexpose_endpoints ON nexpose_tests.endpoint_id = nexpose_endpoints.endpoint_id
								";
if($isAllIssues != "yes"){
	$nexpose_lookup_sql .= "Inner Join nexpose_tmp_vulnerabilities ON nexpose_tmp_vulnerabilities.vuln_id = nexpose_tests.test_id";
}
$nexpose_lookup_sql .="
								WHERE
									nexpose_tests.agency = ? AND
									nexpose_tests.scan_id = ? 									
								";	
								//if you want to not include a specific port in the results...
								//say exclude Terminal Services from the SSL report...
								//then nessus_results.port <> 3389
$data = array($agency,$scan_id);
$nexpose_lookup_stmt = $db->prepare($nexpose_lookup_sql);
$nexpose_lookup_stmt->execute($data);
fwrite($fh_vuln, "\"IP\",\"Port\",\"Observations\"\n");
while($nexpose_lookup_row = $nexpose_lookup_stmt->fetch(PDO::FETCH_ASSOC)){
	$node_address = $nexpose_lookup_row["node_address"];
	$node_name = $nexpose_lookup_row["node_name"];
	$node_device_id = $nexpose_lookup_row["node_device_id"];
	$endpoint_id = $nexpose_lookup_row["endpoint_id"];
	$endpoint_port = $nexpose_lookup_row["endpoint_port"];
	$endpoint_protocol = strtoupper($nexpose_lookup_row["endpoint_protocol"]);
	$service_name = $nexpose_lookup_row["service_name"];
	fwrite($fh_vuln, "\"$node_address\",\"");//First column of the CSV file is the IP Address

	if($service_name != "msrpc"){
		//second column of the CSV file
		fwrite($fh_vuln, "$endpoint_protocol/$endpoint_port ($service_name)\n");
	} 

	fwrite($fh_vuln, "\",\"");//third column
	if($node_name != ""){
		fwrite($fh_vuln, "$node_name\n");//------------------------------------
	}
	$nexpose_os_sql = "SELECT DISTINCT
								nexpose_device_fingerprints.device_vendor,
								nexpose_device_fingerprints.device_family,
								nexpose_device_fingerprints.device_product,
								nexpose_device_fingerprints.device_version
							FROM
								nexpose_tests
								Inner Join nexpose_device_fingerprints ON nexpose_tests.device_id = nexpose_device_fingerprints.device_id
						";
	if($isAllIssues != "yes"){
		$nexpose_os_sql .= "Inner Join nexpose_tmp_vulnerabilities ON nexpose_tmp_vulnerabilities.vuln_id = nexpose_tests.test_id";
	}

	$nexpose_os_sql .="
							WHERE
								nexpose_device_fingerprints.device_certainty >=  '0.8' AND
								nexpose_tests.device_id = ? AND
								nexpose_tests.scan_id =  ? AND
								nexpose_tests.agency =  ?
							ORDER BY
								nexpose_device_fingerprints.device_certainty DESC
							LIMIT 1
						";
	$data = array($node_device_id,$scan_id,$agency);
	$nexpose_os_stmt = $db->prepare($nexpose_os_sql);
	$nexpose_os_stmt->execute($data);
	$nexpose_os_row = $nexpose_os_stmt->fetch(PDO::FETCH_ASSOC);
	$operating_system = $nexpose_os_row["device_vendor"] . " " . $nexpose_os_row["device_family"] . " " . $nexpose_os_row["device_product"] . " " . $nexpose_os_row["device_version"];
	fwrite($fh_vuln, "$operating_system\n");//-----------------------------

	$nexpose_service_sql = "SELECT DISTINCT
								nexpose_endpoint_fingerprints.endpoint_vendor,
								nexpose_endpoint_fingerprints.endpoint_family,
								nexpose_endpoint_fingerprints.endpoint_product,
								nexpose_endpoint_fingerprints.endpoint_version
							FROM
								nexpose_tests
								Inner Join nexpose_endpoint_fingerprints ON nexpose_tests.endpoint_id = nexpose_endpoint_fingerprints.endpoint_id
						";
	if($isAllIssues != "yes"){
		$nexpose_service_sql .= "Inner Join nexpose_tmp_vulnerabilities ON nexpose_tmp_vulnerabilities.vuln_id = nexpose_tests.test_id";
	}

	$nexpose_service_sql .="
							WHERE
								nexpose_endpoint_fingerprints.endpoint_certainty >=  '0.8' AND
								nexpose_tests.agency =  ? AND
								nexpose_tests.scan_id = ? AND
								nexpose_tests.endpoint_id =  ?
							ORDER BY
								nexpose_endpoint_fingerprints.endpoint_certainty DESC
							LIMIT 1
						";
	$data = array($agency,$scan_id,$endpoint_id);
	$nexpose_service_stmt = $db->prepare($nexpose_service_sql);
	$nexpose_service_stmt->execute($data);
	$nexpose_service_row = $nexpose_service_stmt->fetch(PDO::FETCH_ASSOC);
	$service_detail = $nexpose_service_row["endpoint_vendor"] . " " . $nexpose_service_row["endpoint_family"] . " " . $nexpose_service_row["endpoint_product"] . " " . $nexpose_service_row["endpoint_version"];
	fwrite($fh_vuln, "$endpoint_protocol/$endpoint_port:  $service_detail\n");//-----------------------------

	$nexpose_vuln_sql = "SELECT DISTINCT
							nexpose_vulnerabilities.vuln_title,
							nexpose_vulnerabilities.cvssScore
						FROM		
							nexpose_tests
							Inner Join nexpose_vulnerabilities ON nexpose_tests.test_id = nexpose_vulnerabilities.vuln_id
						";
	if($isAllIssues != "yes"){
		$nexpose_vuln_sql .= "Inner Join nexpose_tmp_vulnerabilities ON nexpose_tmp_vulnerabilities.vuln_id = nexpose_tests.test_id";
	}
	$nexpose_vuln_sql .="
						WHERE
							nexpose_tests.agency =  ? AND
							nexpose_tests.scan_id =  ? AND
							nexpose_tests.device_id =  ?
						ORDER BY
							nexpose_vulnerabilities.cvssScore DESC
						";	
	$data = array($agency,$scan_id,$node_device_id);
	$nexpose_vuln_stmt = $db->prepare($nexpose_vuln_sql);
	$nexpose_vuln_stmt->execute($data);
	while($nexpose_vuln_row = $nexpose_vuln_stmt->fetch(PDO::FETCH_ASSOC)){
		$cvssScore = $nexpose_vuln_row["cvssScore"];
		$vuln_title = $nexpose_vuln_row["vuln_title"];
		fwrite($fh_vuln, "($cvssScore) $vuln_title\n");
	}	
	fwrite($fh_vuln, "\"\n");	
}



?>

		<p align="center"><a href="csvfiles/<?php echo "$vuln_table_filename";?>">Click Here</a> to download the Vulnerability CSV file.</p>
		<p align="center"><a href="csvfiles/<?php echo "$exposure_rating_filename";?>">Click Here</a> to download the Exposure CSV file.</p>
		<hr>
<?php




if($vuln_cat == "isOutdatedOS"){
?>
	<p>Unsupported Operating Systems</p>
	<p>Exposure Rating and Justification</p>
	<p>Discussion</p>
	<p>Testing identified XX instances of operating systems that vendors no longer support.</p>
	<p>The operating systems identified may become vulnerable to remotely-exploitable attacks in the future for which vendors will not provide support.</p>
	<p>See Appendix X for the complete list of vulnerable hosts.</p>
	<p>Recommendations</p>
	<p>Upgrade the operating system to a vendor supported version.</p>
<?php
}





if($vuln_cat == "isSSLIssues"){
	$ssl_sql = "SELECT DISTINCT
					nessus_results.pluginName,
					nessus_results.plugin_output
				FROM
					nessus_results
				";
	if($isAllIssues != "yes"){
		$ssl_sql .= "INNER JOIN nessus_tmp_plugins ON nessus_tmp_plugins.pluginID = nessus_results.pluginID";
	}
	$ssl_sql .="
				WHERE
					nessus_results.agency = ? AND
					nessus_results.severity <> 0
				";	

	$ssl_stmt = $db->prepare($ssl_sql);
	$ssl_stmt->execute(array($agency));
	$ssl_table_filename = $agency . "_ssl_table_" . $date . ".csv";
	$ssl_table_file = $myDir . $ssl_table_filename;
	$fh_ssl = fopen($ssl_table_file, 'w') or die("can't open $ssl_table_file for writing.  Please check folder permissions.");

	while($ssl_row = $ssl_stmt->fetch(PDO::FETCH_ASSOC)){
		$ssl_pluginName = $ssl_row["pluginName"];
		$ssl_plugin_output = $ssl_row["plugin_output"];
		fwrite($fh_ssl, "\"$ssl_pluginName\",\"$ssl_plugin_output\"\n");
	
	}
?>
	<p align="center"><a href="csvfiles/<?php echo "$ssl_table_filename";?>">Click Here</a> to download the SSL Exposure CSV file.</p>
	<p></p>
	<p>Exposure Rating and Justification</p>
	<p>Discussion</p>
	<p>Testing identified XX hosts that use SSL for encryption but exhibit problems with the SSL certificates. These problems include the following:<br>
SSL / TLS Renegotiation Handshakes MiTM Plaintext Data Injection<br>
SSL certificate allows negotiation with weak ciphers (i.e., below 128-bit)<br>
Expired SSL Certificates<br>
SSL certificates are signed by an untrusted certificate authority. The certificates were likely generated by a vendor. Use of these certificate will result in user inability to distinguish between valid session negotiation and a “man in the middle” attack<br>
<br>The following example illustrates non-compliant configuration of one of the servers tested. <br>
</p>
	<p>Recommendations</p>
	<p>Replace SSL certificates that were not signed by a trusted third-party.<br>
Replace SSL certificates that have expired.<br>
Configure web servers to not support RC4 or cipher strengths weaker than 128-bit.<br>
</p>
<?php
}

if($vuln_cat == "isSSHIssues"){
?>
	<p></p>
	<p>Exposure Rating and Justification</p>
	<p>Discussion</p>
	<p>Testing identified XXXX servers that use SSH for secure remote access but issues with the algorithms used to protect the communications were identified. These problems include the following:
	<br>The SSH server is configured to support Cipher Block Chaining (CBC) encryption. This may allow an attacker to recover the plaintext message from the ciphertext.
	<br>SSH is configured to allow 96-bit MAC algorithms.
</p>
	<p>Recommendations</p>
	<p>Contact the vendor or consult product documentation to disable CBC mode cipher encryption, and enable CTR or GCM cipher mode encryption.</p>
<?php
}

if($vuln_cat == "isSMBIssues"){
?>
	<p>SMB Signatures not Enabled</p>
	<p>Exposure Rating and Justification</p>
	<p>Discussion</p>
	<p>These systems do not enabled or require SMB signing. SMB signing allows the recipient of SMB packets to confirm their authenticity and helps prevent MiTM attacks against SMB.</p>
	<p>See Appendix X for the complete list of vulnerable hosts.</p>
	<p>Recommendations</p>
	<p>Enable and require SMB signing.</p>
<?php
}

if($vuln_cat == "isRDPIssues"){

$rdp_counter = 0;
foreach($serviceList_array as $sLA)
{
  if($sLA === 'ms-wbt-server')
    $rdp_counter++;
}
?>
	<p>Microsoft Remote Desktop Protocol Weaknesses</p>
	<p>Exposure Rating and Justification</p>
	<p>Discussion</p>
	<p>Testing identified <?php echo $rdp_counter ?> hosts that are hosting Microsoft Remote Desktop services. Although the services require authentication to access, exposure of these services increases the potential for system compromise and three configuration weaknesses were identified.</p>
	<p>The remote version of the Remote Desktop Protocol Server (Terminal Service) is vulnerable to a man-in-the-middle (MiTM) attack. The RDP client makes no effort to validate the identity of the server when setting up encryption. An attacker with the ability to intercept traffic from the RDP server can establish encryption with the client and server without being detected. A MiTM attack of this nature would allow the attacker to obtain any sensitive information transmitted, including authentication credentials.<br><br>
This flaw exists because the RDP server stores a hard-coded RSA private key in the mstlsapi.dll library. Any local user with access to this file (on any Windows system) can retrieve the key and use it for this attack.<br>
The remote Terminal Services service is not configured to use strong cryptography.  Using weak cryptography with this service may allow an attacker to eavesdrop on the communications more easily and obtain screenshots and/or keystrokes.<br>
The remote Terminal Services is not configured to use Network Level Authentication (NLA). NLA uses the Credential Security Support Provider (CredSSP) protocol to perform strong server authentication either through TLS/SSL or Kerberos mechanisms, which protect against man-in-the-middle attacks. In addition to improving authentication, NLA also helps protect the remote computer from malicious users and software by completing user authentication before a full RDP connection is established.<br><br>
For the complete list of all vulnerable hosts see Appendix X.
</p>
	<p>Recommendations</p>
	<p>Force the use of SSL as a transport layer for this service if supported, or/and Select the 'Allow connections only from computers running Remote Desktop with Network Level Authentication' setting if it is available. (http://technet.microsoft.com/en-us/library/cc782610.aspx)<br>
Change RDP encryption level to FIPS Compliant.<br>
Enable Network Level Authentication (NLA) on the remote RDP server. This is generally done on the 'Remote' tab of the 'System' settings on Windows. (http://technet.microsoft.com/en-us/library/cc732713.aspx)  <br></p>
<?php
}

if($vuln_cat == "isHPIssues"){
$hp_counter = 0;
foreach($servicePort_array as $sPA)
{
  if($sPA === '2381')
    $hp_counter++;
}
?>
	<p>HP System Management Homepage (Multiple Vulnerabilities)</p>
	<p>Exposure Rating and Justification</p>
	<p>Discussion</p>
	<p>Testing identified <?php echo $hp_counter?> hosts that are running an outdated version of the HP System Management software with multiple vulnerabilities.  Seventy-nine (pull Nessus CVE CVS Report) unique vulnerabilities, listed in the Common Vulnerability Exposure (CVE) database, were identified.  The first vulnerability dates back to March 21, 2008 with the most recent published on June 5, 2014.</p>
	<p>Recommendations</p>
	<p>Upgrade to the latest version of the HP System Management Homepage software.</p>
<?php
}

if($vuln_cat == "isPHPIssues"){

?>
	<p>PHP Unsupported Version (Multiple High Risk Vulnerabilities)</p>
	<p>Exposure Rating and Justification</p>
	<p>Discussion</p>
	<p>Testing identified XXXX hosts that are running an outdated and unsupported version of the PHP software with multiple vulnerabilities.  XXXX (pull Nessus CVE CVS Report) unique vulnerabilities, listed in the Common Vulnerability Exposure (CVE) database, were identified.  The first vulnerability dates back to XX_DATE with the most recent published on XX_DATE.</p>
	<p>Recommendations</p>
	<p>Upgrade to the latest version of the PHP, currently at version XXXX.</p>
<?php
}

if($vuln_cat == "isMSIssues"){
?>
	<p>Microsoft Security Bulletins</p>
	<p>Exposure Rating and Justification</p>
	<p>Discussion</p>
	<p>Security Bulletins are released by Microsoft once a month and provide customers with the latest security updates to help protect their computers and servers.  The naming convention for each bulletin is MSYY-NNN where YY is the last two digits of the year the bulletin was released and NNN is the Nth patch for that year starting at 001 and incrementing.<br><br>

The Microsoft Security Bulletins are rated Critical, Important, Medium, and Low (http://technet.microsoft.com/en-us/security/gg309177.aspx) to assist in prioritizing the applications associated with the security bulletins.  Microsoft states that a bulletin with a Critical rating is for a a vulnerability whose exploitation could allow code execution without user interaction.<br><br>

XX of systems were identified that are missing XX number of Critical Microsoft patches.<br><br>
</p>
	<p>Recommendations</p>
	<p>Review the list of Microsoft Bulletins in Addendum XX and apply any patches according to Microsoft Support recomendations<br>
	Segement all systems required for operations where patching would effect the function of the system.<br>
	All systems missing patches should be documented with patching exceptions approved by the business, operations, and security management.  Exceptions should be reviewed on a periodic basis.<br>
	A plan should be developed to upgrade or sunset vulnerable systems.<br>
	</p>
<?php
}

if($vuln_cat == "isCleartext"){
$serviceList_array_unique = array_values(array_unique($serviceList_array));
$key = count($serviceList_array_unique) - 1;
?>
	<p>Unencrypted Authentication</p>
	<p>Exposure Rating and Justification</p>
	<p>Discussion</p>
	<p>Testing identified
	<?php if($count == 1){ echo " a ";}else{echo " ";}?>
	<?php 
		for($x=0;$x<=$key;$x++){
			if($x == 0){
				echo $serviceList_array_unique[$x];
			} 
			if($x != 0 && $x != $key){
					echo ", " . $serviceList_array_unique[$x];
			}
			if($x == $key){
				echo " and " . $serviceList_array_unique[$x];
			}
		}
	?>
	<?php if($count == 1){ echo " service that does ";}else{echo " services that do ";}?>	
	not provide the means to encrypt or enforce existing encryption to protect the confidentiality of transmitted credentials. An attacker who has the means to intercept network traffic between a user and the remote service can capture unencrypted credentials and other transmitted data.</p>
	<p>Recommendations</p>
	<p>Disable unnecessary services unless business requirements dictate the need for their exposure.<br>
For services that are required, enforce the use of existing encryption or replace services that do not provide the means for encryption.<br>
Review server hardening guidelines to ensure that measures exist that involve disabling of unnecessary services and enforcement of encryption for services that are Internet exposed.<br>
</p>
<?php
}
?>
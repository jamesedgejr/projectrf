<?php
//Custom Report 1
//this custom report is for my current employer.  If it helps you than cool.

include('../main/config.php');
$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$v = new Valitron\Validator($_POST);
$v->rule('accepted', ['isSSLIssues','isRDPIssues','isSMBIssues','isCleartext','isAllIssues','isCustom']);
$v->rule('slug','agency');
if(!$v->validate()) {
    print_r($v->errors());
	exit;
} 

$agency = $_POST["agency"];
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
$pluginID_array = array();
$serviceList_array = array();
$servicePort_array = array();

if($vuln_cat == "isOutdatedOS"){
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"Exposure of unsupported operating systems increase the potential for vulnerabilities for which the operating system vendor will not provide a response.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"MEDIUM\",\"Although the identified system does not currently expose an exploitable flaw, zero-day exploits may become available that will increase the potential for compromise.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"MEDIUM\",\"6\"\n");
	array_push($pluginID_array, "73182","47709","21626","19699");
}
if($vuln_cat == "isSSLIssues"){
	fwrite($fh_exposure, "\"MEDIUM\",\"SSL Isues\",\"Weaknesses in SSL encryption or problems with the third-party trust associated with SSL negotiation can threaten the integrity of encrypted sessions and the confidentiality of encrypted data.\"\n\n");
	fwrite($fh_exposure, "\"Severity\",\"MEDIUM\",\"Weaknesses in SSL encryption or problems with the third-party trust associated with SSL negotiation can threaten the integrity of encrypted sessions and the confidentiality of encrypted data.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"LOW\",\"Attacks against SSL encryption require a high degree of expertise to perform.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"MEDIUM\",\"4\"\n");
	array_push($pluginID_array, "26928","20007","57582","65821","42873","60108","69551","42880","77200","73412","78479","80035","35291","84152","66848","83875","81606","83738","83186");
	// plugins not included on purpose
	// plugin 51192 is for SSL Certificate Cannot Be Trusted 
	// plugin 45411 is for SSL Certificate with Wrong Hostname
}
if($vuln_cat == "isSSHIssues"){
	fwrite($fh_exposure, "\"LOW\",\"SSH Isues\",\"Weaknesses in SSH encryption or problems with the third-party trust associated with SSH negotiation can threaten the integrity of encrypted sessions and the confidentiality of encrypted data.\"\n\n");
	fwrite($fh_exposure, "\"Severity\",\"MEDIUM\",\"Weaknesses in SSH encryption or problems with the third-party trust associated with SSH negotiation can threaten the integrity of encrypted sessions and the confidentiality of encrypted data.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"LOW\",\"Attacks against SSH encryption require a high degree of expertise to perform.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"LOW\",\"3\"\n");
	array_push($pluginID_array, "70658","71049","44076","44075","19592","44077","44078","44079","44065","44073","31737","44080","10882","80556","53841");
}
if($vuln_cat == "isRDPIssues"){
	fwrite($fh_exposure, "\"Severity\",\"MEDIUM\",\"Exploitation of the protocol weaknesses can lead to access to affected systems.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"LOW\",\"An attacker with a high level of expertise is required to exploit the man-in-the-middle vulnerabilities.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"MEDIUM\",\"4\"\n");
	array_push($pluginID_array, "30218","57690","58453","18405");
	# add SSL RDP issues "57582","65821"
}
if($vuln_cat == "isSMBIssues"){
	fwrite($fh_exposure, "\"Severity\",\"MEDIUM\",\"Enable and require SMB signing.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"LOW\",\"Exploit code is available but requires significant expertise and effort to configure and successfully deploy.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"MEDIUM\",\"4\"\n");
	array_push($pluginID_array, "57608");
}
if($vuln_cat == "isHPIssues"){
	fwrite($fh_exposure, "\"HIGH\",\"HP System Management Homepage (Multiple Vulnerabilities)\",\"The version of HP System Management Homepage (SMH) hosted on the remote host is affected by buffer overflow, Cross-site Scripting (XSS), and Denial of Service (DoS) vulnerabilities in the web server, PHP web application code, and OpenSSL implementation.\"\n\n");
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"Readily available exploit code exists for the outdated versions of this software.  The available code can potentially provide access to the system or cause a Denial of Service.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"HIGH\",\"Attacks against the service can lead to compromise of the server and system level access.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"HIGH\",\"9\"\n");
	array_push($pluginID_array, "34694","38832","46015","46677","49272","53532","58811","59851","66541","69020","76345","72959","10746","70118","73639","78090");
}
if($vuln_cat == "isMSIssues"){
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"The vulnerabilities are vulnerable to code execution without user interaction. \"\n");
	fwrite($fh_exposure, "\"Threat\",\"HIGH\",\"Scenarios include self-propagating malware (e.g. network worms), or unavoidable common use scenarios where code execution occurs without warnings or prompts.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"HIGH\",\"10	\"\n");
	array_push($pluginID_array, "11808","11835","12054","12209","13852","15572","18502","19408","21193","21655","22034","34311","34477","35362","35635","40887","48405","53503","58435","79638","82828");
}
if($vuln_cat == "isDatabaseIssues"){
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"\"\n");
	fwrite($fh_exposure, "\"Threat\",\"HIGH\",\"\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"HIGH\",\"10\"\n");
	array_push($pluginID_array, "25492","12047","11563","10673","14641","17654","10660","12067");
}
if($vuln_cat == "isCleartext"){
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"Failure to encrypt the authentication process can result in unencrypted exposure of transmitted credentials.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"MEDIUM\",\"Attacks involving interception of encrypted credentials are likely to require a moderate degree of sophistication, but tools exist that automate such attacks.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"MEDIUM\",\"6\"\n");
	array_push($pluginID_array, "10079","34324","19782","15855","10203","54582","42263");
}

if($vuln_cat == "isPHPIssues"){
	fwrite($fh_exposure, "\"Severity\",\"HIGH\",\"\"\n");
	fwrite($fh_exposure, "\"Threat\",\"HIGH\",\"\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"HIGH\",\"8\"\n");
	array_push($pluginID_array, "43351","43351","58966","58988","44921","57537","48244","51139","51439","73289","58987");
}
if($vuln_cat == "isInformationLeaks"){
	fwrite($fh_exposure, "\"LOW\",\"Information Leakage\",\"Leaking of sensitive information through error messages, misconfiguration, and vulnerable code provides information to an attacker can use to form a plan of attack or assist in a compromise of an organization's data and systems.\"\n\n");
	fwrite($fh_exposure, "\"Severity\",\"LOW\",\"Information leakage does not lead directly to a compromise and can only aid in a potential attack.\"\n");
	fwrite($fh_exposure, "\"Threat\",\"HIGH\",\"Attackers look to identify as much information about a target when formulating a plan of attack.\"\n");
	fwrite($fh_exposure, "\"Exposure Rating/ Mitigation Weight\",\"LOW\",\"3\"\n");
	array_push($pluginID_array, "11714","57792","10759","12113");
}

if($vuln_cat == "isCustom"){
	$pluginID_array = explode(",",$_POST["custom_list"]);
}

$sql = "CREATE temporary TABLE nessus_tmp_plugins (pluginID VARCHAR(255), INDEX ndx_pluginID (pluginID))";
$stmt = $db->prepare($sql);
$stmt->execute();
foreach ($pluginID_array as $pIA){
	$sql="INSERT INTO nessus_tmp_plugins (pluginID) VALUES (?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($pIA));
}

//get the IP address and vulnerable port number for the plugins
$nessus_lookup_host_sql = "SELECT DISTINCT
									nessus_tags.ip_addr
								FROM
									nessus_tags
								INNER JOIN nessus_results ON nessus_results.tagID = nessus_tags.tagID
								";
if($isAllIssues != "yes"){
	$nessus_lookup_host_sql .= "INNER JOIN nessus_tmp_plugins ON nessus_tmp_plugins.pluginID = nessus_results.pluginID";
}
$nessus_lookup_host_sql .="
								WHERE
									nessus_results.agency = ? AND
									nessus_results.severity = ? 									
								";	
$data = array($agency,$severity);
$nessus_lookup_host_stmt = $db->prepare($nessus_lookup_host_sql);
$nessus_lookup_host_stmt->execute($data);
fwrite($fh_vuln, "\"IP\",\"Port\",\"Observations\"\n");
while($nessus_host_row = $nessus_lookup_host_stmt->fetch(PDO::FETCH_ASSOC)){
	$ip_addr = $nessus_host_row["ip_addr"];
	$nessus_lookup_port_sql = "SELECT DISTINCT
									nessus_results.port,
									nessus_results.protocol,
									nessus_results.service
								FROM
									nessus_tags
								INNER JOIN nessus_results ON nessus_results.tagID = nessus_tags.tagID
								";
	if($isAllIssues != "yes"){
		$nessus_lookup_port_sql .= "INNER JOIN nessus_tmp_plugins ON nessus_tmp_plugins.pluginID = nessus_results.pluginID";
	}
	$nessus_lookup_port_sql .="
								WHERE
									nessus_results.agency = ? AND
									nessus_results.severity = ? AND
									nessus_tags.ip_addr = ?
								";	
	$data = array($agency,$severity,$ip_addr);
	$nessus_lookup_port_stmt = $db->prepare($nessus_lookup_port_sql);
	$nessus_lookup_port_stmt->execute($data);	
	while($nessus_port_row = $nessus_lookup_port_stmt->fetch(PDO::FETCH_ASSOC)){
		fwrite($fh_vuln, "\"$ip_addr\"");//First column of the CSV file is the IP Address
		fwrite($fh_vuln, ",\"");
		$port = $nessus_port_row["port"];
		$protocol = $nessus_port_row["protocol"];
		$service = $nessus_port_row["service"];
		$nmap_lookup_sql = "SELECT DISTINCT
							nmap_ports_xml.port_protocol,
							nmap_ports_xml.port_portid,
							nmap_ports_xml.port_service_name,
							nmap_ports_xml.port_service_product,
							nmap_ports_xml.port_service_version,
							nmap_ports_xml.port_service_extrainfo
						FROM
						nmap_runstats_xml
						INNER JOIN nmap_hosts_xml ON nmap_hosts_xml.runstats_id = nmap_runstats_xml.id
						INNER JOIN nmap_ports_xml ON nmap_ports_xml.host_id = nmap_hosts_xml.id
						WHERE
							nmap_runstats_xml.agency = ? AND
							nmap_hosts_xml.address_addr = ? AND
							nmap_ports_xml.port_portid = ? AND
							nmap_ports_xml.port_protocol = ? AND
							nmap_ports_xml.port_state = 'open'
						";
		$nmap_lookup_data = array($agency,$ip_addr,$port,$protocol);
		$nmap_lookup_stmt = $db->prepare($nmap_lookup_sql);
		$nmap_lookup_stmt->execute($nmap_lookup_data);
		$nmap_lookup_Array = $nmap_lookup_stmt->fetchAll(PDO::FETCH_ASSOC);
		$num_rows = count($nmap_lookup_Array);
		if($num_rows > 0){
			$port_protocol = strtoupper($nmap_lookup_Array[0]["port_protocol"]);
			$port_portid = $nmap_lookup_Array[0]["port_portid"];
			$port_service_name = $nmap_lookup_Array[0]["port_service_name"];
			if($port_service_name != "msrpc"){
				//second column of the CSV file is the Nmap related port information
				fwrite($fh_vuln, "$port_protocol/$port_portid ($port_service_name)");
			}
		} else {
			if($service != "msrpc"){
				//second column of the CSV file is the Nessus related port information
				fwrite($fh_vuln, "$protocol/$port ($service)");
			} 
		}		
		fwrite($fh_vuln, "\",\"");//end second column


		$nessus_lookup_sql = "SELECT DISTINCT
							nessus_tags.fqdn,
							nessus_tags.netbios,
							nessus_tags.operating_system
						FROM
							nessus_tags
						INNER JOIN nessus_results ON nessus_results.tagID = nessus_tags.tagID
						WHERE
							nessus_results.agency = ? AND
							nessus_tags.ip_addr = ? 
						";
		$data = array($agency,$ip_addr);
		$nessus_lookup_stmt = $db->prepare($nessus_lookup_sql);
		$nessus_lookup_stmt->execute($data);
		$nessus_row = $nessus_lookup_stmt->fetch(PDO::FETCH_ASSOC);
		$fqdn = $nessus_row["fqdn"];
		$netbios = $nessus_row["netbios"];
		$operating_system = $nessus_row["operating_system"];
		if($fqdn != ""){
			fwrite($fh_vuln, "$fqdn\n");
		} else {
			if($netbios != ""){
				fwrite($fh_vuln, "$netbios\n");
			}
		}
		if($operating_system != ""){
			fwrite($fh_vuln, "$operating_system\n");
		}
		if($num_rows > 0){
			$port_protocol = strtoupper($nmap_lookup_Array[0]["port_protocol"]);
			$port_portid = $nmap_lookup_Array[0]["port_portid"];
			$port_service_name = $nmap_lookup_Array[0]["port_service_name"];
			$port_service_product = $nmap_lookup_Array[0]["port_service_product"];
			$port_service_version = $nmap_lookup_Array[0]["port_service_version"];
			$port_service_extrainfo = $nmap_lookup_Array[0]["port_service_extrainfo"];
			if($port_service_name != "msrpc"){
				if($port_service_product != ""){
					fwrite($fh_vuln, "$port_protocol/$port_portid:  $port_service_product $port_service_version $port_service_extrainfo\n");
				}
			}
		}
		$nessus_lookup_pluginName_sql = "SELECT DISTINCT
											nessus_results.pluginName
										FROM
											nessus_tags
										INNER JOIN nessus_results ON nessus_results.tagID = nessus_tags.tagID
										";
		if($isAllIssues != "yes"){
			$nessus_lookup_pluginName_sql .= "INNER JOIN nessus_tmp_plugins ON nessus_tmp_plugins.pluginID = nessus_results.pluginID";
		}
		$nessus_lookup_pluginName_sql .="
										WHERE
											nessus_results.agency = ? AND
											nessus_tags.ip_addr = ? AND
											nessus_results.port = ? AND
											nessus_results.protocol = ? AND
											nessus_results.service = ? 
										ORDER BY
											nessus_results.severity DESC
										";	
		$data = array($agency,$ip_addr,$port,$protocol,$service);
		$nessus_lookup_pluginName_stmt = $db->prepare($nessus_lookup_pluginName_sql);
		$nessus_lookup_pluginName_stmt->execute($data);
		while($nessus_row = $nessus_lookup_pluginName_stmt->fetch(PDO::FETCH_ASSOC)){
			$pluginName = $nessus_row["pluginName"];
			fwrite($fh_vuln, "$pluginName\n");
		}	
		fwrite($fh_vuln, "\"\n");	
	}
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
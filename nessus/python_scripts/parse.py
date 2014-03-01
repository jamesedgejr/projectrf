#!/usr/bin/python

from argparse import ArgumentParser
from random import randint
import sys
import time
import xml.etree.cElementTree as ET
from pprint import pprint
from os.path import isdir, exists, basename

# Software Version
PROG_VER	=	"0.2"
PROG_NAME	=	"Nessus XML Parser"

class nessus_parser:

	_xml_source     =    ''
	_con			=    ''
	_nessusDateFormat = "%a %b %d %H:%M:%S %Y"
	_new_tags = {}
	
	def __init__(self, filename_xml, database, dbname, user, password, host):
		self._upload_files(filename_xml)
		self._connect_database(database, dbname, user, password, host)
		# For each .nessus file found...
		for report in self._xml_source:
			# Parse and extract information
			self._parse_results(report)
			
	def _connect_database(self, database, dbname, user, password, host):
		if database == 'mysql':
			try:
				import MySQLdb as mdb
				self._con = mdb.connect(host, user, password, dbname)
			except mdb.Error, e:
				print "Error %d: %s" % (e.args[0],e.args[1])
				sys.exit(1)
		else:
			print "[!] No database specified!"
			exit(1)
			
	def _upload_files(self, filename_xml):
		if filename_xml == None or filename_xml == "":
			print "[!] No filename specified!"
			exit(1)
		# Parse input values in order to find valid .nessus files
		self._xml_source = []
		if filename_xml.endswith(".nessus"):
			if not exists(filename_xml):
				print "[!] File specified '%s' not exist!" % filename_xml
				exit(3)
			self._xml_source.append(filename_xml)
		if not self._xml_source:
			print "[!] No file .nessus to parse was found!"
			exit(3)

	def _parse_results(self, file_report):
		tree = ET.ElementTree(file=file_report)

		randValue = randint(1,10000000)
		scan_start = scan_end = randValue
		startScanList = []
		endScanList = []

		report_name = tree.find('Report').attrib['name']
		for rh in tree.getiterator(tag='ReportHost'):
			host_name = rh.attrib['name']	
			for host in rh:
				if host.tag == 'HostProperties':
					tags = {
						'host_start': '',
						'host_end': '',
						'operating_system': '',
						'host_fqdn':'',
						'netbios_name': '',
						'mac_addr': '',
						'ip_addr': '',
						'fqdn': '',
						'system_type': '',
						'ssh_auth_meth': '',
						'ssh_login_used': '',
						'smb_login_used': '',
						'local_checks_proto': '',
					}
					for tag in host:
						if tag.attrib['name']=='HOST_END':
							tags['host_end'] = tag.text
						elif tag.attrib['name']=='operating-system':
							tags['operating_system'] = tag.text
						elif tag.attrib['name']=='mac-address':
							tags['mac_addr'] = tag.text
						elif tag.attrib['name']=='host-ip':
							tags['ip_addr'] = tag.text
						elif tag.attrib['name']=='host-fqdn':
							tags['fqdn'] = tag.text
						elif tag.attrib['name']=='netbios-name':
							tags['netbios_name'] = tag.text
						elif tag.attrib['name']=='HOST_START':
							tags['host_start'] = tag.text
						elif tag.attrib['name']=='system-type':
							tags['system_type'] = tag.text
						elif tag.attrib['name']=='ssh-auth-meth':
							tags['ssh_auth_meth'] = tag.text
						elif tag.attrib['name']=='ssh-login-used':
							tags['ssh_login_used'] = tag.text
						elif tag.attrib['name']=='smb-login-used':
							tags['smb_login_used'] = tag.text
						elif tag.attrib['name']=='local-checks-proto':
							tags['local_checks_proto'] = tag.text
						else:
							_new_tags[tag.attrib['name']] = tag.text
					host_start_epoch = time.mktime(time.strptime(tags['host_start'], _nessusDateFormat))
					startScanList.append(host_start_epoch)
					host_end_epoch = time.mktime(time.strptime(tags['host_end'], _nessusDateFormat))
					endScanList.append(host_end_epoch)
					try:
						cur = self._con.cursor()
						cur.execute("INSERT INTO nessus_tags (fqdn,host_end,host_start,ip_addr,local_checks_proto,mac_addr,netbios,operating_system,ssh_auth_meth,ssh_login_used,smb_login_used,system_type) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", (tags['fqdn'], tags['host_end'], tags['host_start'], tags['ip_addr'], tags['local_checks_proto'], tags['mac_addr'], tags['netbios_name'], tags['operating_system'], tags['ssh_auth_meth'], tags['ssh_login_used'], tags['smb_login_used'], tags['system_type']))
						cur.execute("SELECT LAST_INSERT_ID()")
						row = cur.fetchone()
						tagID = row[0]
					except mdb.Error, e:
						print "Error %d: %s" % (e.args[0],e.args[1])
						sys.exit(1)
		else:
			for r in tree.getiterator(tag='ReportItem'):
				ReportItem = {
						'bidList': [],
						'canvas_package': '',
						'certList': [],
						'cveList': [],
						'cpe': '',
						'cvss_base_score': '0.0',
						'cvss_temporal_score': '0.0',
						'cvss__temporal_vector': '',
						'cvss_vector': '',
						'cweList': [],
						'd2_elliot_name': '',
						'description': '',
						'edbList': [],
						'exploit_available': 'false',
						'exploit_framework_canvase': '',
						'exploit_framework_core': '',
						'exploit_framework_d2_elliot': '',
						'exploit_framework_metasploit': '',
						'exploitability_ease': '',
						'fname': '',
						'iavaList': [],
						'iavbList': [],
						'icsaList': [],
						'metasploit_name': '',
						'msftList': [],
						'osvdbList': [],
						'patch_publication_date': 'NotKnown',
						'pluginFamily': '',
						'pluginID': '',
						'pluginName': '',
						'plugin_modification_date': 'NotKnown',
						'plugin_output': '',
						'plugin_publication_date': 'NotKnown',
						'port': '',
						'protocol': '',
						'risk_factor': '',
						'script_version': 'NotKnown',
						'secuniaList': [],
						'see_alsoList': [],
						'severity': '',
						'solution': '',
						'stig_severity': '',
						'svc_name': '',
						'synopsis': '',
						'vuln_publication_date': 'NotKnown',
				}
				ReportItem['port'] = r.attrib['port']
				ReportItem['svc_name'] = r.attrib['svc_name']
				ReportItem['protocol'] = r.attrib['protocol']
				ReportItem['severity'] = r.attrib['severity']
				ReportItem['pluginID'] = r.attrib['pluginID']
				ReportItem['pluginName'] = r.attrib['pluginName']
				ReportItem['pluginFamily'] = r.attrib['pluginFamily']
				if r.find('exploitability_ease') is not None: ReportItem['exploitability_ease'] = r.find('exploitability_ease').text
				if r.find('exploit_framweork_metasploit') is not None: ReportItem['exploit_framweork_metasploit'] = r.find('exploit_framweork_metasploit').text
				if r.find('metasploit_name') is not None: ReportItem['metasploit_name'] = r.find('metasploit_name').text
				if r.find('exploit_available') is not None: ReportItem['exploit_available'] = r.find('exploit_available').text
				if r.find('cvss_vector') is not None: ReportItem['cvss_vector'] = r.find('cvss_vector').text
				if r.find('cvss_temporal_vector') is not None: ReportItem['cvss_temporal_vector'] = r.find('cvss_temporal_vector').text
				ReportItem['solution'] = r.find('solution').text
				if r.find('cvss_score') is not None: ReportItem['cvss_score'] = r.find('cvss_score').text
				if r.find('cvss_base_score') is not None: ReportItem['cvss_base_score'] = r.find('cvss_base_score').text
				ReportItem['risk_factor'] = r.find('risk_factor').text
				ReportItem['description'] = r.find('description').text
				ReportItem['synopsis'] = r.find('synopsis').text
				if r.find('vuln_publication_date') is not None: ReportItem['vuln_publication_date'] = r.find('vuln_publication_date').text
				if r.find('plugin_publication_date') is not None: ReportItem['plugin_publication_date'] = r.find('plugin_publication_date').text
				if r.find('patch_publication_date') is not None: ReportItem['patch_publication_date'] = r.find('patch_publication_date').text
				if r.find('plugin_modification_date') is not None: ReportItem['plugin_modification_date'] = r.find('plugin_modification_date').text
				if r.find('plugin_output') is not None: ReportItem['plugin_output'] = r.find('plugin_output').text
				if r.find('plugin_type') is not None: ReportItem['plugin_type'] = r.find('plugin_type').text
				if r.find('script_version') is not None: ReportItem['script_version'] = r.find('script_version').text
				ReportItem['cveList']=[]
				if r.find('cve') is not None:
					for cve in r.findall('cve'):
						ReportItem['cveList'].append(cve.text)
				if r.find('bid') is not None:
					for bid in r.findall('bid'):
						ReportItem['bidList'].append(bid.text)
				if r.find('osvdb') is not None:
					for osvdb in r.findall('osvdb'):
						ReportItem['osvdbList'].append(osvdb.text)
				if r.find('cert') is not None:
					for cert in r.findall('cert'):
						ReportItem['certList'].append(cert.text)
				if r.find('iava') is not None:
					for iava in r.findall('iava'):
						ReportItem['iavaList'].append(iava.text)
				if r.find('cwe') is not None:
					for cwe in r.findall('cwe'):
						ReportItem['cweList'].append(cwe.text)
				if r.find('msft') is not None:
					for msft in r.findall('msft'):
						ReportItem['msftList'].append(msft.text)
				if r.find('secunia') is not None:
					for secunia in r.findall('secunia'):
						ReportItem['secuniaList'].append(secunia.text)
				if r.find('edb-id') is not None:
					for edb in r.findall('edb-id'):
						ReportItem['edbList'].append(edb.text)
				if r.find('see_also') is not None:
					for see_also in r.findall('see_also'):
						ReportItem['see_alsoList'].append(see_also.text)
				
				sql = "INSERT INTO nessus_results (agency, report_name, scan_start, scan_end, tagID, host_name, pluginID, pluginName, pluginFamily, port, service, protocol, host_start, host_end, severity, cvss_vector,  cvss_score, risk_factor, exploitability_ease, vuln_publication_date, exploit_framework_metasploit, metasploit_name, description, plugin_publication_date, synopsis, see_also, patch_publication_date, exploit_available, plugin_modification_date, plugin_output, script_version, plugin_type, solution, cveList, bidList, osvdbList, certList, iavaList, cweList, msftList, secuniaList, edbList) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
				values = 
				try:
					cur = self._con.cursor()
					cur.execute(sql, ('agency', report_name, scan_start, scan_end, tagID, host_name, ReportItem['pluginID'], ReportItem['pluginName'], ReportItem['pluginFamily'], ReportItem['port'], ReportItem['svc_name'], ReportItem['protocol'], host_start_epoch, host_end_epoch, ReportItem['severity'], ReportItem['cvss_vector'], ReportItem['cvss_score'], ReportItem['risk_factor'], ReportItem['exploitability_ease'], ReportItem['vuln_publication_date'], ReportItem['exploit_framework_metasploit'], ReportItem['metasploit_name'], ReportItem['description'], ReportItem['plugin_publication_date'], ReportItem['synopsis'], ','.join(ReportItem['see_alsoList']), ReportItem['patch_publication_date'], ReportItem['exploit_available'], ReportItem['plugin_modification_date'], ReportItem['plugin_output'], ReportItem['script_version'], ReportItem['plugin_type'], ReportItem['solution'], ','.join(ReportItem['cveList']), ','.join(ReportItem['bidList']), ','.join(ReportItem['osvdbList']), ','.join(ReportItem['certList']), ','.join(ReportItem['iavaList']), ','.join(ReportItem['cweList']), ','.join(ReportItem['msftList']), ','.join(ReportItem['secuniaList']), ','.join(ReportItem['edbList'])))
				except mdb.Error, e:
					print "Error %d: %s" % (e.args[0],e.args[1])
					sys.exit(1)
		scan_start = sorted(startScanList)
		scan_end = sorted(endScanList, reverse=True)
		try:
			cur = self._con.cursor()
			cur.execute("UPDATE nessus_results SET scan_start = %s, scan_end = %s WHERE scan_start = %s AND scan_end = %s", (scan_start[0], scan_end[0], randValue, randValue))
		except mdb.Error, e:
			print "Error %d: %s" % (e.args[0],e.args[1])
			sys.exit(1)
		
		
# Entry point
if __name__ == "__main__":

	# Arguments parser
	cmdline = ArgumentParser(description="%s performs information extraction from .nessus files and creates a customized output. (Compatible with Nessus v5 release)" % PROG_NAME,
							epilog="Developed by James Edge(james.edge@jedge.com)"
							)
	cmdline.add_argument("-f", "--file",
						metavar="[.nessus]",
						help="Report exported in .nessus format.",
						)
	cmdline.add_argument("-d", "--dir",
						metavar="[DIR]",
						help="Directory containing reports exported in .nessus format.",
						)
	cmdline.add_argument("-H", "--host",
						metavar="[IP|Hostname]",
						help="IP address or hostname of database server.",
						default="localhost",
						)
	cmdline.add_argument("-u", "--user",
						metavar="[user]",
						help="Database username.",
						default="root",
						)
	cmdline.add_argument("-p", "--password",
						metavar="[password]",
						help="Database password",
						default="h1ds2n",
						)
	cmdline.add_argument("-m", "--database",
						metavar="[database]",
						choices="['mysql', 'sqlite', 'postgresql']",
						help="Database Type (MySQL, SQLite, or Postgresql)",
						default="mysql",
						)
	cmdline.add_argument("-D", "--dbname",
						metavar="[dbname]",
						help="Database.",
						default="projectRF",
						)
	# Parse arguments provided
	args = cmdline.parse_args()

	# Process command line
	parser = nessus_parser(args.file, args.database, args.dbname, args.user, args.password, args.host)

	# Exit successfully
	exit(0)
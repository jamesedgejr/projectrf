#!/usr/bin/python

from collections import OrderedDict
import csv
from argparse import ArgumentParser
from random import randint
import sys
import time
import xml.etree.cElementTree as ET
from pprint import pprint
from os.path import isdir, exists, basename

PROG_VER	=	"0.1"
PROG_NAME	=	"Nmap XML to CSV Parser"

class nmap_xml_parser:

	_xml_source     =    ''
	_NmapRunServices = OrderedDict()
	_NmapRunOS = OrderedDict()
	
	def __init__(self, filename_xml, output_name, services, osmatch):
		self._NmapRunOS['host_hostname'] = self._NmapRunOS['host_ipv4_addr'] = self._NmapRunOS['os_osmatch_name'] = self._NmapRunOS['os_osmatch_accuracy'] = self._NmapRunOS['os_osclass_type'] = self._NmapRunOS['os_osclass_vendor'] = self._NmapRunOS['os_osclass_osfamily'] = self._NmapRunOS['os_osclass_osgen'] = self._NmapRunOS['os_osclass_accuracy'] = ''
		self._NmapRunServices['host_hostname'] = self._NmapRunServices['host_ipv4_addr'] = self._NmapRunServices['port_protocol'] = self._NmapRunServices['port_portid'] = self._NmapRunServices['port_state'] = self._NmapRunServices['service_name'] = self._NmapRunServices['service_product'] = self._NmapRunServices['service_version'] = self._NmapRunServices['service_extrainfo'] = ''
		self._check_files(filename_xml)
		
		for xml_file in self._xml_source:
			if services:
				self._parse_services(xml_file, output_name)
			if osmatch:
				self._parse_os(xml_file, output_name)
			
	def _check_files(self, filename_xml):
		if filename_xml == None or filename_xml == "":
			print("[!] No filename specified!")
			exit(1)
		
		self._xml_source = []
		if filename_xml.endswith(".xml"):
			if not exists(filename_xml):
				print("[!] File specified '%s' not exist!") % filename_xml
				exit(3)
			self._xml_source.append(filename_xml)
		if not self._xml_source:
			print("[!] No nmap .xml file to parse was found!")
			exit(3)
			
	def _parse_os(self, file_report, output_name):
		os_outfile = open(output_name, 'a') 
		os_w = csv.DictWriter(os_outfile, self._NmapRunOS.keys())
		os_w.writeheader()
		tree = ET.ElementTree(file=file_report)
		for host in tree.iter(tag='host'):
			self._NmapRunOS.update((k, '') for k in self._NmapRunOS.keys())
			if host.find('address').attrib['addrtype'] == 'ipv4':  self._NmapRunOS['host_ipv4_addr'] = host.find('address').attrib['addr']
			if host.find('hostnames/hostname') is not None:  self._NmapRunOS['host_hostname'] = host.find('hostnames/hostname').attrib['name']
			if host.find('status').attrib['state'] == 'up':
				for os in host.iter(tag='osmatch'):
					self._NmapRunOS['os_osmatch_name'] = os.attrib['name']
					self._NmapRunOS['os_osmatch_accuracy'] = os.attrib['accuracy']
					for osclass in os.iter(tag='osclass'):
						if 'type' in osclass.attrib: 
							self._NmapRunOS['os_osclass_type'] = osclass.attrib['type'] 
						else: 
							self._NmapRunOS['os_osclass_type'] = ''
						if 'vendor' in osclass.attrib: 
							self._NmapRunOS['os_osclass_vendor'] = osclass.attrib['vendor'] 
						else: 
							self._NmapRunOS['os_osclass_vendor'] = ''
						if 'osfamily' in osclass.attrib: 
							self._NmapRunOS['os_osclass_osfamily'] = osclass.attrib['osfamily'] 
						else: 
							self._NmapRunOS['os_osclass_osfamily'] = ''
						if 'osgen' in osclass.attrib: 
							self._NmapRunOS['os_osclass_osgen'] = osclass.attrib['osgen'] 
						else: 
							self._NmapRunOS['os_osclass_osgen'] = ''
						if 'accuracy' in osclass.attrib: 
							self._NmapRunOS['os_osclass_accuracy'] = osclass.attrib['accuracy'] 
						else: 
							self._NmapRunOS['os_osclass_accuracy'] = ''
						os_w.writerow(self._NmapRunOS)
						
	
	def _parse_services(self, file_report, output_name):
		services_outfile = open(output_name, 'a') 
		services_w = csv.DictWriter(services_outfile, self._NmapRunServices.keys())
		services_w.writeheader()
		tree = ET.ElementTree(file=file_report)
		for host in tree.iter(tag='host'):
			self._NmapRunServices.update((k, '') for k in self._NmapRunServices.keys())
			if host.find('address').attrib['addrtype'] == 'ipv4':  self._NmapRunServices['host_ipv4_addr'] = host.find('address').attrib['addr']
			if host.find('hostnames/hostname') is not None:  self._NmapRunServices['host_hostname'] = host.find('hostnames/hostname').attrib['name']
			if host.find('status').attrib['state'] == 'up':
				for port in host.iter(tag='port'):
					self._NmapRunServices['port_protocol'] = port.attrib['protocol']
					self._NmapRunServices['port_portid'] = port.attrib['portid']
					if port.find('state').attrib['state'] != 'filtered':
						self._NmapRunServices['port_state'] = port.find('state').attrib['state']
						if 'name' in port.find('service').attrib: 
							self._NmapRunServices['service_name'] = port.find('service').attrib['name']
						else:
							self._NmapRunServices['service_name'] = ''
						if 'product' in port.find('service').attrib: 
							self._NmapRunServices['service_product'] = port.find('service').attrib['product']
						else:
							self._NmapRunServices['service_product'] = ''
						if 'version' in port.find('service').attrib: 
							self._NmapRunServices['service_version'] = port.find('service').attrib['version']
						else:
							self._NmapRunServices['service_version'] = ''
						if 'extrainfo' in port.find('service').attrib: 
							self._NmapRunServices['service_extrainfo'] = port.find('service').attrib['extrainfo']
						else:
							self._NmapRunServices['service_extrainfo'] = ''
						services_w.writerow(self._NmapRunServices)

if __name__ == "__main__":

	cmdline = ArgumentParser(description="%s performs information extraction from Nmap XML files and produces output in csv format." % PROG_NAME,
							epilog="Developed by James Edge(james.edge@jedge.com)"
							)
	cmdline.add_argument("-i", "--input",
						metavar="[.xml]",
						help="Nmap XML file created from -oA or -oX options.",
						)
	cmdline.add_argument("-o", "--output",
						help="Report file name.",
						)
	cmdline.add_argument("-t", "--type",
						metavar="[.csv|.html|ALL]",
						help="Report file type.",
						)
	cmdline.add_argument("-S", "--services",
						action='store_true',
						help="Output detailed information on ports, protocols, and services identified",
						)
	cmdline.add_argument("-O", "--osmatch",
						action='store_true',
						help="Output detailed information on operating system identification.",
						)
	cmdline.add_argument("-N", "--NSE",

						help="Output detailed information on operating system identification.",
						)

	args = cmdline.parse_args()
	if args.type == "csv":
		output_name = args.output + ".csv"

	parser = nmap_xml_parser(args.input, output_name, args.services, args.osmatch)

	exit(0)
#!/usr/bin/python

import sys
import pprint
import Parser

parser = Parser.Parser( 'i.xml' )
	
print '\nscan session:'

session = parser.get_session()

print "\tstart time:\t" + session.start_time
print "\tstop time:\t" + session.finish_time
print "\tnmap version:\t" + session.nmap_version
print "\tnmap args:\t" + session.scan_args
print "\ttotal hosts:\t" + session.total_hosts
print "\tup hosts:\t" + session.up_hosts
print "\tdown hosts:\t" + session.down_hosts

for h in parser.all_hosts():
	print 'host ' +h.ip + ' is ' + h.status

	for port in h.get_ports( 'tcp', 'open' ):
		print "\tservice of tcp port " + port + ":"
		s = h.get_service( 'tcp', port )
			
		if s == None:
			print "\t\tno service"
	
		else:
			print "\t\t" + s.name
			print "\t\t" + s.product
			print "\t\t" + s.version
			print "\t\t" + s.extrainfo
			print "\t\t" + s.fingerprint
	
pprint.pprint( parser.all_ips() )

#!/usr/bin/python

__author__ =  'yunshu(wustyunshu@hotmail.com)'
__version__=  '0.2'

import sys
import pprint
import Service
import xml.dom.minidom

class Host:
	
	def __init__( self, HostNode ):
		self.host_node = HostNode
		self.status = HostNode.getElementsByTagName('status')[0].getAttribute('state');
		self.ip = HostNode.getElementsByTagName('address')[0].getAttribute('addr');
	
	def get_ports( self, protocol, state ):
		'''get a list of ports which is in the special state'''
		
		open_ports = [ ]

		for port_node in self.host_node.getElementsByTagName('port'):
			if port_node.getAttribute('protocol') == protocol and port_node.getElementsByTagName('state')[0].getAttribute('state') == state:
				open_ports.append( port_node.getAttribute('portid') )
			
		return open_ports
	
	def get_service( self, protocol, port ):
		'''return a Service object'''
		
		for port_node in self.host_node.getElementsByTagName('port'):
			if port_node.getAttribute('protocol') == protocol and port_node.getAttribute('portid') == port:
				service_node = port_node.getElementsByTagName('service')[0]
				service = Service.Service( service_node )

				return service
		return None

if __name__ == '__main__':

	dom = xml.dom.minidom.parse('i.xml')
	host_nodes = dom.getElementsByTagName('host')

	if len(host_nodes) == 0:
		sys.exit( )
	
	host_node = dom.getElementsByTagName('host')[0]

	h = Host( host_node )
	print 'host status: ' + h.status
	print 'host ip: ' + h.ip

	for port in h.get_ports( 'tcp', 'open' ):
		print port + " is open"

	print "service of tcp port 80:"
	s = h.get_service( 'tcp', '80' )
	if s == None:
		print "\tno service"
	
	else:
		print "\t" + s.name
		print "\t" + s.product
		print "\t" + s.version
		print "\t" + s.extrainfo
		print "\t" + s.fingerprint

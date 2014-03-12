

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for nmap_hosts_xml
-- ----------------------------
DROP TABLE IF EXISTS `nmap_hosts_xml`;
CREATE TABLE `nmap_hosts_xml` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `runstats_id` int(11) unsigned DEFAULT NULL,
  `status_state` enum('') DEFAULT NULL,
  `status_reason` enum('') DEFAULT NULL,
  `address_addr` varchar(15) DEFAULT '',
  `address_addrtype` enum('') DEFAULT '',
  `hostname_name` varchar(255) DEFAULT NULL,
  `hostname_type` enum('') DEFAULT NULL,
  `extraports_state` varchar(25) DEFAULT NULL,
  `extraports_count` varchar(5) DEFAULT NULL,
  `os_portused_state` text,
  `os_portused_proto` text,
  `os_portused_portid` text,
  `os_osfingerprint` text,
  `uptime_seconds` text,
  `uptime_lastboot` text,
  `tcpsequence_index` text,
  `tcpsequence_class` text,
  `tcpsequence_difficulty` text,
  `tcpsequence_values` text,
  `ipidsequence_class` text,
  `ipidsequence_values` text,
  `tcptsequence_class` text,
  `tcptsequence_values` text,
  `times_srtt` varchar(5) DEFAULT NULL,
  `times_rttvar` varchar(5) DEFAULT NULL,
  `times_to` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hosts_xml` (`id`,`runstats_id`,`status_state`) USING HASH
) ENGINE=MyISAM AUTO_INCREMENT=63837 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `nmap_osclass_xml`;
CREATE TABLE `nmap_osclass_xml` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `runstats_id` int(11) unsigned DEFAULT NULL,
  `host_id` int(11) unsigned DEFAULT NULL,
  `osmatch_id` int(11) unsigned DEFAULT NULL,
  `os_osclass_type` text,
  `os_osclass_vendor` text,
  `os_osclass_osfamily` text,
  `os_osclass_accuracy` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=63837 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `nmap_osmatch_xml`;
CREATE TABLE `nmap_osmatch_xml` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `runstats_id` int(11) unsigned DEFAULT NULL,
  `host_id` int(11) unsigned DEFAULT NULL,
  `os_osmatch_name` text,
  `os_osmatch_accuracy` text,
  `os_osmatch_line` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=63837 DEFAULT CHARSET=latin1;
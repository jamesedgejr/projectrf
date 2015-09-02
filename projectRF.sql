
-- ----------------------------
-- Table structure for dumpsec_group_table
-- ----------------------------
DROP TABLE IF EXISTS `dumpsec_group_table`;
CREATE TABLE `dumpsec_group_table` (
  `RowID` int(10) NOT NULL AUTO_INCREMENT,
  `Agency` varchar(255) NOT NULL,
  `FileDate` varchar(255) NOT NULL,
  `FileName` varchar(255) NOT NULL,
  `Host` varchar(255) NOT NULL,
  `GroupName` varchar(255) DEFAULT NULL,
  `Comment` varchar(255) DEFAULT NULL,
  `GroupType` varchar(255) DEFAULT NULL,
  `GroupMember` varchar(255) DEFAULT NULL,
  `MemberType` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`RowID`)
) ENGINE=MyISAM AUTO_INCREMENT=64146 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dumpsec_temp_groups
-- ----------------------------
DROP TABLE IF EXISTS `dumpsec_temp_groups`;
CREATE TABLE `dumpsec_temp_groups` (
  `groups` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dumpsec_user_table
-- ----------------------------
DROP TABLE IF EXISTS `dumpsec_user_table`;
CREATE TABLE `dumpsec_user_table` (
  `RowID` int(10) NOT NULL AUTO_INCREMENT,
  `Agency` varchar(255) DEFAULT NULL,
  `FileDate` varchar(255) DEFAULT NULL,
  `FileName` varchar(255) DEFAULT NULL,
  `Host` varchar(255) DEFAULT NULL,
  `UserName` varchar(255) DEFAULT NULL,
  `FullName` varchar(255) DEFAULT NULL,
  `AccountType` varchar(255) DEFAULT NULL,
  `Comment` varchar(255) DEFAULT NULL,
  `HomeDrive` varchar(255) DEFAULT NULL,
  `HomeDir` varchar(255) DEFAULT NULL,
  `Profile` varchar(255) DEFAULT NULL,
  `LogonScript` varchar(255) DEFAULT NULL,
  `Workstations` varchar(255) DEFAULT NULL,
  `PswdCanBeChanged` varchar(255) DEFAULT NULL,
  `PswdLastSetTime` varchar(255) DEFAULT NULL,
  `PswdRequired` varchar(255) DEFAULT NULL,
  `PswdExpires` varchar(255) DEFAULT NULL,
  `PswdExpiresTime` varchar(255) DEFAULT NULL,
  `AcctDisabled` varchar(255) DEFAULT NULL,
  `AcctLockedOut` varchar(255) DEFAULT NULL,
  `AcctExpiresTime` varchar(255) DEFAULT NULL,
  `LastLogonTime` varchar(255) DEFAULT NULL,
  `LastLogonServer` varchar(255) DEFAULT NULL,
  `LogonHours` varchar(255) DEFAULT NULL,
  `RasDialin` varchar(255) DEFAULT NULL,
  `RasCallback` varchar(255) DEFAULT NULL,
  `RasCallbackNumber` varchar(255) DEFAULT NULL,
  `PasswordAgeDays` varchar(255) DEFAULT NULL,
  `LastLogonAgeDays` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`RowID`)
) ENGINE=MyISAM AUTO_INCREMENT=335305 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for kismet_results_newcore
-- ----------------------------
DROP TABLE IF EXISTS `kismet_results_newcore`;
CREATE TABLE `kismet_results_newcore` (
  `record_id` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `floor` varchar(100) DEFAULT NULL,
  `file_name` varchar(100) DEFAULT NULL,
  `kismet_version` varchar(50) DEFAULT NULL,
  `start_time` varchar(50) DEFAULT NULL,
  `end_time` varchar(50) DEFAULT NULL,
  `cs_uuid` varchar(50) DEFAULT NULL,
  `cs_card_name` varchar(50) DEFAULT NULL,
  `cs_card_interface` varchar(50) DEFAULT NULL,
  `cs_card_type` varchar(50) DEFAULT NULL,
  `cs_card_packets` varchar(50) DEFAULT NULL,
  `cs_card_hop` varchar(50) DEFAULT NULL,
  `cs_card_channels` varchar(50) DEFAULT NULL,
  `wn_number` varchar(50) DEFAULT NULL,
  `wn_type` varchar(50) DEFAULT NULL,
  `wn_first_time` varchar(50) DEFAULT NULL,
  `wn_last_time` varchar(50) DEFAULT NULL,
  `wn_SSID_first_time` varchar(50) DEFAULT NULL,
  `wn_SSID_last_time` varchar(50) DEFAULT NULL,
  `wn_SSID_type` varchar(50) DEFAULT NULL,
  `wn_SSID_max_rate` varchar(50) DEFAULT NULL,
  `wn_SSID_packets` varchar(50) DEFAULT NULL,
  `wn_SSID_beaconrate` varchar(50) DEFAULT NULL,
  `wn_SSID_encryption` varchar(50) DEFAULT NULL,
  `wn_SSID__dot11d_country` varchar(50) DEFAULT NULL,
  `wn_SSID_dot11d_range_start` varchar(50) DEFAULT NULL,
  `wn_SSID_dot11d_range_end` varchar(50) DEFAULT NULL,
  `wn_SSID_dot11d_range_max_power` varchar(50) DEFAULT NULL,
  `wn_SSID_essid_cloaked` varchar(50) DEFAULT NULL,
  `wn_SSID_essid_name` varchar(50) DEFAULT NULL,
  `wn_SSID_info` varchar(255) DEFAULT NULL,
  `wn_BSSID` varchar(50) DEFAULT NULL,
  `wn_manuf` varchar(50) DEFAULT NULL,
  `wn_channel` varchar(50) DEFAULT NULL,
  `wn_freqmhz` varchar(50) DEFAULT NULL,
  `wn_maxrate` varchar(50) DEFAULT NULL,
  `wn_maxseenrate` varchar(50) DEFAULT NULL,
  `wn_carrier` varchar(50) DEFAULT NULL,
  `wn_encoding` varchar(50) DEFAULT NULL,
  `wn_packets_LLC` varchar(50) DEFAULT NULL,
  `wn_packets_data` varchar(50) DEFAULT NULL,
  `wn_packets_crypt` varchar(50) DEFAULT NULL,
  `wn_packets_total` varchar(50) DEFAULT NULL,
  `wn_packets_fragments` varchar(50) DEFAULT NULL,
  `wn_packets_retries` varchar(50) DEFAULT NULL,
  `wn_gps_info_min_lat` varchar(50) DEFAULT NULL,
  `wn_gps_info_min_lon` varchar(50) DEFAULT NULL,
  `wn_gps_info_min_alt` varchar(50) DEFAULT NULL,
  `wn_gps_info_min_spd` varchar(50) DEFAULT NULL,
  `wn_gps_info_max_lat` varchar(50) DEFAULT NULL,
  `wn_gps_info_max_lon` varchar(50) DEFAULT NULL,
  `wn_gps_info_max_alt` varchar(50) DEFAULT NULL,
  `wn_gps_info_max_spd` varchar(50) DEFAULT NULL,
  `wn_gps_info_peak_lat` varchar(50) DEFAULT NULL,
  `wn_gps_info_peak_lon` varchar(50) DEFAULT NULL,
  `wn_gps_info_peak_alt` varchar(50) DEFAULT NULL,
  `wn_gps_info_avg_lat` varchar(50) DEFAULT NULL,
  `wn_gps_info_avg_lon` varchar(50) DEFAULT NULL,
  `wn_gps_info_avg_alt` varchar(50) DEFAULT NULL,
  `wn_ip_address` varchar(50) DEFAULT NULL,
  `wn_ip_range` varchar(50) DEFAULT NULL,
  `wn_datasize` varchar(50) DEFAULT NULL,
  `wn_snr_info_last_signal_dbm` varchar(50) DEFAULT NULL,
  `wn_snr_info_last_noise_dbm` varchar(50) DEFAULT NULL,
  `wn_snr_info_last_signal_rssi` varchar(50) DEFAULT NULL,
  `wn_snr_info_last_noise_rssi` varchar(50) DEFAULT NULL,
  `wn_snr_info_min_signal_dbm` varchar(50) DEFAULT NULL,
  `wn_snr_info_min_noise_dbm` varchar(50) DEFAULT NULL,
  `wn_snr_info_min_signal_rssi` varchar(50) DEFAULT NULL,
  `wn_snr_info_min_noise_rssi` varchar(50) DEFAULT NULL,
  `wn_snr_info_max_signal_dbm` varchar(50) DEFAULT NULL,
  `wn_snr_info_max_noise_dbm` varchar(50) DEFAULT NULL,
  `wn_snr_info_max_signal_rssi` varchar(50) DEFAULT NULL,
  `wn_snr_info_max_noise_rssi` varchar(50) DEFAULT NULL,
  `wn_bsstimestamp` varchar(50) DEFAULT NULL,
  `wn_cdp_device` varchar(50) DEFAULT NULL,
  `wn_cdp_portid` varchar(50) DEFAULT NULL,
  `wn_seen_card_seen_uuid` varchar(50) DEFAULT NULL,
  `wn_seen_card_seen_time` varchar(50) DEFAULT NULL,
  `wn_seen_card_seen_packets` varchar(50) DEFAULT NULL,
  `wc_number` varchar(50) DEFAULT NULL,
  `wc_type` varchar(50) DEFAULT NULL,
  `wc_first_time` varchar(50) DEFAULT NULL,
  `wc_last_time` varchar(50) DEFAULT NULL,
  `wc_client_mac` varchar(50) DEFAULT NULL,
  `wc_client_manuf` varchar(50) DEFAULT NULL,
  `wc_SSID_first_time` varchar(50) DEFAULT NULL,
  `wc_SSID_last_time` varchar(50) DEFAULT NULL,
  `wc_SSID_type` varchar(50) DEFAULT NULL,
  `wc_SSID_max_rate` varchar(50) DEFAULT NULL,
  `wc_SSID_packets` varchar(50) DEFAULT NULL,
  `wc_SSID_beaconrate` varchar(50) DEFAULT NULL,
  `wc_SSID_encryption` varchar(50) DEFAULT NULL,
  `wc_SSID_ssid` varchar(50) DEFAULT NULL,
  `wc_channel` varchar(50) DEFAULT NULL,
  `wc_freqmhz` varchar(50) DEFAULT NULL,
  `wc_maxrate` varchar(50) DEFAULT NULL,
  `wc_maxseenrate` varchar(50) DEFAULT NULL,
  `wc_encoding` varchar(50) DEFAULT NULL,
  `wc_packets_LLC` varchar(50) DEFAULT NULL,
  `wc_packets_data` varchar(50) DEFAULT NULL,
  `wc_packets_crypt` varchar(50) DEFAULT NULL,
  `wc_packets_total` varchar(50) DEFAULT NULL,
  `wc_packets_fragments` varchar(50) DEFAULT NULL,
  `wc_packets_retries` varchar(50) DEFAULT NULL,
  `wc_gps_info_min_lat` varchar(50) DEFAULT NULL,
  `wc_gps_info_min_lon` varchar(50) DEFAULT NULL,
  `wc_gps_info_min_alt` varchar(50) DEFAULT NULL,
  `wc_gps_info_min_spd` varchar(50) DEFAULT NULL,
  `wc_gps_info_max_lat` varchar(50) DEFAULT NULL,
  `wc_gps_info_max_lon` varchar(50) DEFAULT NULL,
  `wc_gps_info_max_alt` varchar(50) DEFAULT NULL,
  `wc_gps_info_max_spd` varchar(50) DEFAULT NULL,
  `wc_gps_info_peak_lat` varchar(50) DEFAULT NULL,
  `wc_gps_info_peak_lon` varchar(50) DEFAULT NULL,
  `wc_gps_info_peak_alt` varchar(50) DEFAULT NULL,
  `wc_gps_info_avg_lat` varchar(50) DEFAULT NULL,
  `wc_gps_info_avg_lon` varchar(50) DEFAULT NULL,
  `wc_gps_info_avg_alt` varchar(50) DEFAULT NULL,
  `wc_datasize` varchar(50) DEFAULT NULL,
  `wc_snr_info_last_signal_dbm` varchar(50) DEFAULT NULL,
  `wc_snr_info_last_noise_dbm` varchar(50) DEFAULT NULL,
  `wc_snr_info_last_signal_rssi` varchar(50) DEFAULT NULL,
  `wc_snr_info_last_noise_rssi` varchar(50) DEFAULT NULL,
  `wc_snr_info_min_signal_dbm` varchar(50) DEFAULT NULL,
  `wc_snr_info_min_noise_dbm` varchar(50) DEFAULT NULL,
  `wc_snr_info_min_signal_rssi` varchar(50) DEFAULT NULL,
  `wc_snr_info_min_noise_rssi` varchar(50) DEFAULT NULL,
  `wc_snr_info_max_signal_dbm` varchar(50) DEFAULT NULL,
  `wc_snr_info_max_noise_dbm` varchar(50) DEFAULT NULL,
  `wc_snr_info_max_signal_rssi` varchar(50) DEFAULT NULL,
  `wc_snr_info_max_noise_rssi` varchar(50) DEFAULT NULL,
  `wc_cdp_device` varchar(50) DEFAULT NULL,
  `wc_cdp_portid` varchar(50) DEFAULT NULL,
  `wc_seen_card_seen_uuid` varchar(50) DEFAULT NULL,
  `wc_seen_card_seen_time` varchar(50) DEFAULT NULL,
  `wc_seen_card_seen_packets` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`record_id`)
) ENGINE=MyISAM AUTO_INCREMENT=31149 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nessus_audit_file
-- ----------------------------
DROP TABLE IF EXISTS `nessus_audit_file`;
CREATE TABLE `nessus_audit_file` (
  `auditCheckID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(100) NOT NULL,
  `report_name` varchar(100) NOT NULL,
  `check_type` varchar(7) DEFAULT NULL,
  `check_policy` varchar(50) DEFAULT NULL,
  `custom_item_type` varchar(50) DEFAULT NULL,
  `description` tinytext,
  `value_type` varchar(50) DEFAULT NULL,
  `value_data` varchar(50) DEFAULT NULL,
  `service` varchar(50) DEFAULT NULL,
  `service_name` varchar(50) DEFAULT NULL,
  `svc_option` varchar(50) DEFAULT NULL,
  `acl_option` varchar(50) DEFAULT NULL,
  `file` varchar(100) DEFAULT NULL,
  `reg_option` varchar(50) DEFAULT NULL,
  `reg_key` varchar(200) DEFAULT NULL,
  `reg_item` varchar(50) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `info` text,
  `account_type` varchar(50) DEFAULT NULL,
  `custom_item_check_type` varchar(100) DEFAULT NULL,
  `right_type` varchar(100) DEFAULT NULL,
  `group_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`auditCheckID`),
  KEY `audit_file_index` (`agency`,`report_name`,`check_type`,`custom_item_type`,`value_type`,`value_data`,`service_name`,`svc_option`,`acl_option`,`file`,`reg_key`,`reg_item`) USING HASH
) ENGINE=MyISAM AUTO_INCREMENT=39681 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nessus_compliance_results
-- ----------------------------
DROP TABLE IF EXISTS `nessus_compliance_results`;
CREATE TABLE `nessus_compliance_results` (
  `resultsComplianceID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(100) NOT NULL,
  `scan_start` varchar(10) DEFAULT NULL,
  `scan_end` varchar(10) DEFAULT NULL,
  `report_name` varchar(100) NOT NULL,
  `host_name` varchar(50) DEFAULT NULL,
  `tagID` varchar(10) DEFAULT NULL,
  `pluginID` varchar(5) DEFAULT NULL,
  `pluginName` varchar(100) DEFAULT NULL,
  `severity` varchar(1) DEFAULT NULL,
  `description` tinytext,
  `plugin_output` tinytext,
  `remoteValue` mediumtext,
  `policyValue` mediumtext,
  `complianceError` tinytext,
  PRIMARY KEY (`resultsComplianceID`),
  KEY `compliance_index` (`agency`,`report_name`,`host_name`,`severity`) USING HASH
) ENGINE=MyISAM AUTO_INCREMENT=296816 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nessus_results
-- ----------------------------
DROP TABLE IF EXISTS `nessus_results`;
CREATE TABLE `nessus_results` (
  `resultsID` int(20) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `agency` varchar(50) NOT NULL,
  `bidList` text,
  `canvas_package` varchar(255) DEFAULT NULL,
  `certList` text,
  `cpe` text,
  `cveList` text,
  `cvss_base_score` decimal(5,1) DEFAULT NULL,
  `cvss_temporal_score` decimal(5,1) DEFAULT NULL,
  `cvss_temporal_vector` varchar(10) DEFAULT NULL,
  `cvss_vector` varchar(10) DEFAULT NULL,
  `cweList` text,
  `d2_elliot_name` varchar(255) DEFAULT NULL,
  `description` text,
  `edbList` text,
  `exploit_available` varchar(255) DEFAULT NULL,
  `exploit_framework_canvas` varchar(255) DEFAULT NULL,
  `exploit_framework_core` varchar(255) DEFAULT NULL,
  `exploit_framework_d2_elliot` varchar(255) DEFAULT NULL,
  `exploit_framework_metasploit` varchar(255) DEFAULT NULL,
  `exploitability_ease` varchar(255) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `icsaList` text,
  `iavaList` text,
  `iavbList` text,
  `metasploit_name` varchar(255) DEFAULT NULL,
  `msftList` text,
  `osvdbList` text,
  `patch_publication_date` varchar(10) DEFAULT NULL,
  `plugin_modification_date` varchar(10) DEFAULT NULL,
  `plugin_output` text,
  `plugin_publication_date` varchar(10) DEFAULT NULL,
  `plugin_type` varchar(100) DEFAULT NULL,
  `pluginFamily` varchar(100) DEFAULT NULL,
  `pluginID` int(5) NOT NULL,
  `pluginName` varchar(255) NOT NULL,
  `port` varchar(5) DEFAULT NULL,
  `protocol` varchar(3) DEFAULT NULL,
  `report_name` varchar(100) NOT NULL,
  `risk_factor` varchar(10) DEFAULT NULL,
  `scan_end` varchar(10) NOT NULL,
  `scan_start` varchar(10) NOT NULL,
  `script_version` varchar(255) DEFAULT NULL,
  `secuniaList` text,
  `see_also` text,
  `service` varchar(50) DEFAULT NULL,
  `severity` varchar(1) DEFAULT NULL,
  `solution` text,
  `stig_severity` varchar(5) DEFAULT NULL,
  `synopsis` text,
  `tagID` varchar(10) DEFAULT NULL,
  `vuln_publication_date` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`resultsID`),
  KEY `nessus_pluginID_index` (`pluginID`) USING HASH,
  KEY `nessus_host_index` (`agency`,`scan_start`,`scan_end`,`report_name`,`port`,`service`,`protocol`) USING HASH,
  FULLTEXT KEY `nessus_plugin_index` (`pluginName`,`pluginFamily`,`severity`,`cvss_vector`,`risk_factor`,`description`,`synopsis`,`see_also`,`plugin_output`,`solution`,`cveList`,`bidList`,`msftList`)
) ENGINE=MyISAM AUTO_INCREMENT=817630 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nessus_tags
-- ----------------------------
DROP TABLE IF EXISTS `nessus_tags`;
CREATE TABLE `nessus_tags` (
  `tagID` int(10) NOT NULL AUTO_INCREMENT,
  `bios_uuid` varchar(50) DEFAULT NULL,
  `fqdn` varchar(100) DEFAULT NULL,
  `host_end` varchar(10) DEFAULT NULL,
  `host_name` varchar(50) NOT NULL,
  `host_start` varchar(10) DEFAULT NULL,
  `ip_addr` varchar(15) DEFAULT NULL,
  `local_checks_proto` varchar(255) DEFAULT NULL,
  `mac_addr` varchar(17) DEFAULT NULL,
  `netbios` varchar(16) DEFAULT NULL,
  `operating_system` varchar(100) DEFAULT NULL,
  `operating_system_unsupported` varchar(5) DEFAULT NULL,
  `pcidss_compliance_failed` varchar(10) DEFAULT NULL,
  `pcidss_compliance` varchar(10) DEFAULT NULL,
  `pcidss_low_risk_flaw` varchar(10) DEFAULT NULL,
  `pcidss_medium_risk_flaw` varchar(10) DEFAULT NULL,
  `pcidss_high_risk_flaw` varchar(10) DEFAULT NULL,
  `pcidss_www_xss` varchar(10) DEFAULT NULL,
  `pcidss_www_header_injection` varchar(10) DEFAULT NULL,
  `pcidss_directory_browsing` varchar(10) DEFAULT NULL,
  `pcidss_obsolete_operating_system` varchar(10) DEFAULT NULL,
  `pcidss_deprecated_ssl` varchar(10) DEFAULT NULL,
  `pcidss_reachable_db` varchar(10) DEFAULT NULL,
  `pcidss_expired_ssl_certificate` varchar(10) DEFAULT NULL,
  `ssh_auth_meth` varchar(255) DEFAULT NULL,
  `smb_login_used` varchar(255) DEFAULT NULL,
  `ssh_login_used` varchar(255) DEFAULT NULL,
  `system_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`tagID`),
  KEY `compliance_index` (`ip_addr`,`mac_addr`,`fqdn`,`netbios`,`operating_system`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=221326 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nessus_temp_severity
-- ----------------------------
DROP TABLE IF EXISTS `nessus_temp_severity`;
CREATE TABLE `nessus_temp_severity` (
  `severity` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nessus_tmp_family
-- ----------------------------
DROP TABLE IF EXISTS `nessus_tmp_family`;
CREATE TABLE `nessus_tmp_family` (
  `pluginFamily` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nessus_tmp_hosts
-- ----------------------------
DROP TABLE IF EXISTS `nessus_tmp_hosts`;
CREATE TABLE `nessus_tmp_hosts` (
  `host_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nessus_tmp_itemType
-- ----------------------------
DROP TABLE IF EXISTS `nessus_tmp_itemType`;
CREATE TABLE `nessus_tmp_itemType` (
  `custom_item_type` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nmap_host_nse_xml
-- ----------------------------
DROP TABLE IF EXISTS `nmap_host_nse_xml`;
CREATE TABLE `nmap_host_nse_xml` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `host_id` int(11) unsigned NOT NULL,
  `script_id` varchar(255) NOT NULL,
  `script_output` text,
  PRIMARY KEY (`id`),
  KEY `nse_xml` (`id`,`host_id`,`script_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=142095 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nmap_host_trace_xml
-- ----------------------------
DROP TABLE IF EXISTS `nmap_host_trace_xml`;
CREATE TABLE `nmap_host_trace_xml` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host_id` int(11) DEFAULT NULL,
  `trace_port` varchar(5) DEFAULT NULL,
  `trace_proto` varchar(5) DEFAULT NULL,
  `hop_ttl` varchar(2) DEFAULT NULL,
  `hop_ipaddr` varchar(20) DEFAULT NULL,
  `hop_rtt` varchar(10) DEFAULT NULL,
  `hop_host` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=630 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nmap_hosts_xml
-- ----------------------------
DROP TABLE IF EXISTS `nmap_hosts_xml`;
CREATE TABLE `nmap_hosts_xml` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `runstats_id` int(11) unsigned DEFAULT NULL,
  `status_state` varchar(10) DEFAULT NULL,
  `status_reason` varchar(10) DEFAULT NULL,
  `address_addr` varchar(15) DEFAULT '',
  `address_addrtype` varchar(255) DEFAULT '',
  `hostname_name` varchar(255) DEFAULT NULL,
  `hostname_type` varchar(255) DEFAULT NULL,
  `extraports_state` varchar(25) DEFAULT NULL,
  `extraports_count` varchar(5) DEFAULT NULL,
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
  KEY `hosts_xml` (`id`,`runstats_id`,`status_state`(1)) USING HASH
) ENGINE=MyISAM AUTO_INCREMENT=72554 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nmap_osclass_xml
-- ----------------------------
DROP TABLE IF EXISTS `nmap_osclass_xml`;
CREATE TABLE `nmap_osclass_xml` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `runstats_id` int(11) unsigned DEFAULT NULL,
  `host_id` int(11) unsigned DEFAULT NULL,
  `osmatch_id` int(11) unsigned DEFAULT NULL,
  `os_osclass_type` varchar(50) DEFAULT NULL,
  `os_osclass_vendor` varchar(50) DEFAULT NULL,
  `os_osclass_osfamily` varchar(50) DEFAULT NULL,
  `os_osclass_osgen` varchar(10) DEFAULT NULL,
  `os_osclass_accuracy` varchar(3) DEFAULT NULL,
  `os_osclass_cpe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=64121 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nmap_osmatch_xml
-- ----------------------------
DROP TABLE IF EXISTS `nmap_osmatch_xml`;
CREATE TABLE `nmap_osmatch_xml` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `runstats_id` int(11) unsigned DEFAULT NULL,
  `host_id` int(11) unsigned DEFAULT NULL,
  `os_portused_state` varchar(100) DEFAULT NULL,
  `os_portused_proto` varchar(50) DEFAULT NULL,
  `os_portused_portid` varchar(25) DEFAULT NULL,
  `os_osmatch_name` varchar(50) DEFAULT NULL,
  `os_osmatch_accuracy` varchar(3) DEFAULT NULL,
  `os_osmatch_line` varchar(10) DEFAULT NULL,
  `os_osfingerprint` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=64018 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nmap_port_nse_xml
-- ----------------------------
DROP TABLE IF EXISTS `nmap_port_nse_xml`;
CREATE TABLE `nmap_port_nse_xml` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `host_id` int(11) unsigned NOT NULL,
  `port_id` int(11) NOT NULL,
  `script_id` varchar(255) NOT NULL,
  `script_output` text,
  PRIMARY KEY (`id`),
  KEY `nse_xml` (`id`,`host_id`,`script_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=142127 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nmap_ports_xml
-- ----------------------------
DROP TABLE IF EXISTS `nmap_ports_xml`;
CREATE TABLE `nmap_ports_xml` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `host_id` int(11) unsigned NOT NULL,
  `port_protocol` varchar(5) DEFAULT NULL,
  `port_portid` int(5) DEFAULT NULL,
  `port_state` varchar(15) DEFAULT NULL,
  `port_service_name` varchar(100) DEFAULT NULL,
  `port_service_product` varchar(255) DEFAULT NULL,
  `port_service_tunnel` varchar(10) DEFAULT NULL,
  `port_service_version` varchar(100) DEFAULT NULL,
  `port_service_extrainfo` varchar(100) DEFAULT NULL,
  `port_service_servicefp` varchar(255) DEFAULT NULL,
  `port_service_method` varchar(255) DEFAULT NULL,
  `port_service_conf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ports_xml` (`id`,`host_id`,`port_portid`,`port_state`(1),`port_service_name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=225151 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nmap_runstats_xml
-- ----------------------------
DROP TABLE IF EXISTS `nmap_runstats_xml`;
CREATE TABLE `nmap_runstats_xml` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `nmaprun_scanner` varchar(10) DEFAULT NULL,
  `nmaprun_args` text,
  `nmaprun_start` varchar(20) DEFAULT NULL,
  `nmaprun_startstr` varchar(255) DEFAULT NULL,
  `nmaprun_version` varchar(20) DEFAULT NULL,
  `nmaprun_xmloutputversion` varchar(20) DEFAULT NULL,
  `scaninfo_type` varchar(20) DEFAULT NULL,
  `scaninfo_protocol` varchar(20) DEFAULT NULL,
  `scaninfo_numservices` int(6) DEFAULT NULL,
  `scaninfo_services` text,
  `finished_time` varchar(10) DEFAULT NULL,
  `finished_timestr` varchar(255) DEFAULT NULL,
  `finished_elapsed` varchar(10) DEFAULT NULL,
  `finished_summary` text,
  `hosts_up` int(11) DEFAULT NULL,
  `hosts_down` int(11) DEFAULT NULL,
  `hosts_total` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `runstats` (`id`,`agency`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nmap_temp_hosts
-- ----------------------------
DROP TABLE IF EXISTS `nmap_temp_hosts`;
CREATE TABLE `nmap_temp_hosts` (
  `address_addr` varchar(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nmap_temp_nse
-- ----------------------------
DROP TABLE IF EXISTS `nmap_temp_nse`;
CREATE TABLE `nmap_temp_nse` (
  `script_type` varchar(255) DEFAULT NULL,
  `script_id` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nmap_temp_ports
-- ----------------------------
DROP TABLE IF EXISTS `nmap_temp_ports`;
CREATE TABLE `nmap_temp_ports` (
  `port_portid` varchar(255) DEFAULT NULL,
  `port_service_name` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nmap_temp_portState
-- ----------------------------
DROP TABLE IF EXISTS `nmap_temp_portState`;
CREATE TABLE `nmap_temp_portState` (
  `portState` varchar(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------

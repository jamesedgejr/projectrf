/*
MySQL Data Transfer
Source Host: 192.168.1.102
Source Database: projectRF
Target Host: 192.168.1.102
Target Database: projectRF
Date: 9/1/2015 11:09:42 PM
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for AppScan_ApplicationData_BrokenLinks
-- ----------------------------
DROP TABLE IF EXISTS `AppScan_ApplicationData_BrokenLinks`;
CREATE TABLE `AppScan_ApplicationData_BrokenLinks` (
  `ApplicationDataID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `XmlReport_Name` varchar(255) DEFAULT NULL,
  `BrokenLinks_Total` varchar(255) DEFAULT NULL,
  `BrokenLink_Reason` varchar(255) DEFAULT NULL,
  `BrokenLink_Url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ApplicationDataID`)
) ENGINE=MyISAM AUTO_INCREMENT=529 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for AppScan_ApplicationData_Comments
-- ----------------------------
DROP TABLE IF EXISTS `AppScan_ApplicationData_Comments`;
CREATE TABLE `AppScan_ApplicationData_Comments` (
  `ApplicationDataID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `XmlReport_Name` varchar(255) DEFAULT NULL,
  `Comments_Total` varchar(255) DEFAULT NULL,
  `Comment_Text` text,
  `Comment_Url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ApplicationDataID`)
) ENGINE=MyISAM AUTO_INCREMENT=119 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for AppScan_ApplicationData_Cookies
-- ----------------------------
DROP TABLE IF EXISTS `AppScan_ApplicationData_Cookies`;
CREATE TABLE `AppScan_ApplicationData_Cookies` (
  `ApplicationDataID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `XmlReport_Name` varchar(255) DEFAULT NULL,
  `Cookies_Total` varchar(255) DEFAULT NULL,
  `Cookie_Value` text,
  `Cookie_FirstSetInUrl` varchar(255) DEFAULT NULL,
  `Cookie_FirstRequestedUrl` varchar(255) DEFAULT NULL,
  `Cookie_Domain` varchar(255) DEFAULT NULL,
  `Cookie_Expires` varchar(255) DEFAULT NULL,
  `Cookie_Secure` varchar(255) DEFAULT NULL,
  `Cookie_Name` varchar(255) DEFAULT NULL,
  `JavaScripts_Total` varchar(255) DEFAULT NULL,
  `JavaScript_Text` text,
  `JavaScript_Url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ApplicationDataID`)
) ENGINE=MyISAM AUTO_INCREMENT=353 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for AppScan_ApplicationData_FilteredLinks
-- ----------------------------
DROP TABLE IF EXISTS `AppScan_ApplicationData_FilteredLinks`;
CREATE TABLE `AppScan_ApplicationData_FilteredLinks` (
  `ApplicationDataID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `XmlReport_Name` varchar(255) DEFAULT NULL,
  `FilteredLinks_Total` varchar(255) DEFAULT NULL,
  `FilteredLink_Reason` varchar(255) DEFAULT NULL,
  `FilteredLink_Url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ApplicationDataID`)
) ENGINE=MyISAM AUTO_INCREMENT=987 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for AppScan_ApplicationData_JavaScripts
-- ----------------------------
DROP TABLE IF EXISTS `AppScan_ApplicationData_JavaScripts`;
CREATE TABLE `AppScan_ApplicationData_JavaScripts` (
  `ApplicationDataID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `XmlReport_Name` varchar(255) DEFAULT NULL,
  `JavaScripts_Total` varchar(255) DEFAULT NULL,
  `JavaScript_Text` text,
  `JavaScript_Url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ApplicationDataID`)
) ENGINE=MyISAM AUTO_INCREMENT=326 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for AppScan_ApplicationData_ScriptParameters
-- ----------------------------
DROP TABLE IF EXISTS `AppScan_ApplicationData_ScriptParameters`;
CREATE TABLE `AppScan_ApplicationData_ScriptParameters` (
  `ApplicationDataID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `XmlReport_Name` varchar(255) DEFAULT NULL,
  `ScriptParameters_Total` varchar(255) DEFAULT NULL,
  `ScriptParameter_Name` varchar(255) DEFAULT NULL,
  `ScriptParameter_Values` text,
  `ScriptParameter_Url` varchar(255) DEFAULT NULL,
  `ScriptParameter_Type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ApplicationDataID`)
) ENGINE=MyISAM AUTO_INCREMENT=927 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for AppScan_ApplicationData_VisitedLinks
-- ----------------------------
DROP TABLE IF EXISTS `AppScan_ApplicationData_VisitedLinks`;
CREATE TABLE `AppScan_ApplicationData_VisitedLinks` (
  `ApplicationDataID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `XmlReport_Name` varchar(255) DEFAULT NULL,
  `VisitedLinks_Total` varchar(255) DEFAULT NULL,
  `VisitedLink_Url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ApplicationDataID`)
) ENGINE=MyISAM AUTO_INCREMENT=1103 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for AppScan_Issues
-- ----------------------------
DROP TABLE IF EXISTS `AppScan_Issues`;
CREATE TABLE `AppScan_Issues` (
  `IssueID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `XmlReport_Name` varchar(255) DEFAULT NULL,
  `Issue_IssueTypeID` varchar(255) DEFAULT NULL,
  `Issue_Noise` varchar(255) DEFAULT NULL,
  `Url` varchar(255) DEFAULT NULL,
  `Entity` varchar(255) DEFAULT NULL,
  `Variant_ID` varchar(255) DEFAULT NULL,
  `Comments` text,
  `Difference` text,
  `Reasoning` text,
  `Validation_Location` text,
  `Validation_Length` text,
  `Validation_String` text,
  `OriginalHttpTraffic` text,
  `TestHttpTraffic` text,
  PRIMARY KEY (`IssueID`)
) ENGINE=MyISAM AUTO_INCREMENT=2125 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for AppScan_IssueTypes
-- ----------------------------
DROP TABLE IF EXISTS `AppScan_IssueTypes`;
CREATE TABLE `AppScan_IssueTypes` (
  `IssueTypesID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `XmlReport_Name` varchar(255) DEFAULT NULL,
  `IssueType_ID` varchar(255) DEFAULT NULL,
  `IssueType_Count` varchar(255) DEFAULT NULL,
  `RemediationID` varchar(255) DEFAULT NULL,
  `advisory_name` varchar(255) DEFAULT NULL,
  `advisory_testDescription` varchar(255) DEFAULT NULL,
  `threatClassification_name` varchar(255) DEFAULT NULL,
  `threatClassification_reference` varchar(255) DEFAULT NULL,
  `testTechnicalDescription` text,
  `causes` text,
  `securityRisks` text,
  `affectedProducts` text,
  `linkName` text,
  `linkTarget` text,
  `fixRecommendation_type` varchar(255) DEFAULT NULL,
  `fixRecommendation` text,
  `Severity` varchar(255) DEFAULT NULL,
  `Severity_number` varchar(1) DEFAULT NULL,
  `EntityType` varchar(255) DEFAULT NULL,
  `Invasive` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IssueTypesID`)
) ENGINE=MyISAM AUTO_INCREMENT=1977 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for AppScan_RemediationTypes
-- ----------------------------
DROP TABLE IF EXISTS `AppScan_RemediationTypes`;
CREATE TABLE `AppScan_RemediationTypes` (
  `RemediationTypesID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `XmlReport_Name` varchar(255) DEFAULT NULL,
  `Total` varchar(255) DEFAULT NULL,
  `RemediationType_ID` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Priority` varchar(255) DEFAULT NULL,
  `fixRecommendation` text,
  `fixRecommendation_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`RemediationTypesID`)
) ENGINE=MyISAM AUTO_INCREMENT=1141 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for AppScan_Summary
-- ----------------------------
DROP TABLE IF EXISTS `AppScan_Summary`;
CREATE TABLE `AppScan_Summary` (
  `SummaryID` int(10) NOT NULL AUTO_INCREMENT,
  `agency` varchar(255) DEFAULT NULL,
  `XmlReport_Name` varchar(255) DEFAULT NULL,
  `TotalIssues` varchar(255) DEFAULT NULL,
  `TotalVariants` varchar(255) DEFAULT NULL,
  `TotalRemediations` varchar(255) DEFAULT NULL,
  `TotalScanDuration` varchar(255) DEFAULT NULL,
  `Host_Name` varchar(255) DEFAULT NULL,
  `Host_TotalInformationalIssues` varchar(255) DEFAULT NULL,
  `Host_TotalLowSeverityIssues` varchar(255) DEFAULT NULL,
  `Host_TotalMediumSeverityIssues` varchar(255) DEFAULT NULL,
  `Host_TotalHighSeverityIssues` varchar(255) DEFAULT NULL,
  `Host_Total` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`SummaryID`)
) ENGINE=MyISAM AUTO_INCREMENT=208 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for appscan_temp_severity
-- ----------------------------
DROP TABLE IF EXISTS `appscan_temp_severity`;
CREATE TABLE `appscan_temp_severity` (
  `severity` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for appscan_temp_threat
-- ----------------------------
DROP TABLE IF EXISTS `appscan_temp_threat`;
CREATE TABLE `appscan_temp_threat` (
  `threatClassification_name` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for appscan_temp_url
-- ----------------------------
DROP TABLE IF EXISTS `appscan_temp_url`;
CREATE TABLE `appscan_temp_url` (
  `url` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

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
) ENGINE=MyISAM AUTO_INCREMENT=233678 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dumpsec_hashes_pass
-- ----------------------------
DROP TABLE IF EXISTS `dumpsec_hashes_pass`;
CREATE TABLE `dumpsec_hashes_pass` (
  `hash` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dumpsec_temp_groups
-- ----------------------------
DROP TABLE IF EXISTS `dumpsec_temp_groups`;
CREATE TABLE `dumpsec_temp_groups` (
  `groups` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dumpsec_user_hashes
-- ----------------------------
DROP TABLE IF EXISTS `dumpsec_user_hashes`;
CREATE TABLE `dumpsec_user_hashes` (
  `username` varchar(255) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=358007 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for hashes#and#passwords
-- ----------------------------
DROP TABLE IF EXISTS `hashes#and#passwords`;
CREATE TABLE `hashes#and#passwords` (
  `Hashes` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hashes#and#users
-- ----------------------------
DROP TABLE IF EXISTS `hashes#and#users`;
CREATE TABLE `hashes#and#users` (
  `Username` varchar(255) DEFAULT NULL,
  `Hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=33752 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for low2pwn
-- ----------------------------
DROP TABLE IF EXISTS `low2pwn`;
CREATE TABLE `low2pwn` (
  `pluginID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `cm:compliance-actual-value` varchar(255) DEFAULT NULL,
  `cm:compliance-audit-file` varchar(255) DEFAULT NULL,
  `cm:compliance-check-id` varchar(255) DEFAULT NULL,
  `cm:compliance-check-name` varchar(255) DEFAULT NULL,
  `cm:compliance-info` text,
  `cm:compliance-policy-value` varchar(255) DEFAULT NULL,
  `cm:compliance-reference` text,
  `cm:compliance-result` varchar(255) DEFAULT NULL,
  `cm:compliance-see-also` text,
  `cm:compliance-solution` text,
  `compliance` varchar(10) DEFAULT NULL,
  `description` text,
  `fname` varchar(255) DEFAULT NULL,
  `plugin_name` varchar(255) DEFAULT NULL,
  `plugin_publication_date` varchar(10) DEFAULT NULL,
  `plugin_type` varchar(10) DEFAULT NULL,
  `pluginFamily` varchar(100) DEFAULT NULL,
  `pluginID` varchar(10) DEFAULT NULL,
  `pluginName` varchar(255) DEFAULT NULL,
  `port` varchar(10) DEFAULT NULL,
  `protocol` varchar(10) DEFAULT NULL,
  `risk_factor` varchar(10) DEFAULT NULL,
  `script_version` varchar(50) DEFAULT NULL,
  `severity` varchar(10) DEFAULT NULL,
  `svc_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`resultsComplianceID`),
  KEY `compliance_index` (`agency`,`report_name`,`host_name`,`severity`) USING HASH
) ENGINE=MyISAM AUTO_INCREMENT=297025 DEFAULT CHARSET=latin1;

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
  `cvss_temporal_vector` varchar(25) DEFAULT NULL,
  `cvss_vector` varchar(25) DEFAULT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=1031813 DEFAULT CHARSET=latin1;

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
  `operating_system` varchar(255) DEFAULT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=225044 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for nessus_temp_family
-- ----------------------------
DROP TABLE IF EXISTS `nessus_temp_family`;
CREATE TABLE `nessus_temp_family` (
  `pluginFamily` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nessus_temp_hosts
-- ----------------------------
DROP TABLE IF EXISTS `nessus_temp_hosts`;
CREATE TABLE `nessus_temp_hosts` (
  `host_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nessus_temp_itemType
-- ----------------------------
DROP TABLE IF EXISTS `nessus_temp_itemType`;
CREATE TABLE `nessus_temp_itemType` (
  `custom_item_type` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
-- Table structure for nexpose_device_fingerprints
-- ----------------------------
DROP TABLE IF EXISTS `nexpose_device_fingerprints`;
CREATE TABLE `nexpose_device_fingerprints` (
  `device_fingerprint_id` int(10) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(100) DEFAULT NULL,
  `device_certainty` float(4,2) DEFAULT NULL,
  `device_class` varchar(100) DEFAULT NULL,
  `device_vendor` varchar(255) DEFAULT NULL,
  `device_family` varchar(255) DEFAULT NULL,
  `device_product` varchar(255) DEFAULT NULL,
  `device_version` varchar(255) DEFAULT NULL,
  `agency` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`device_fingerprint_id`),
  KEY `device_id` (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5721 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nexpose_endpoint_fingerprints
-- ----------------------------
DROP TABLE IF EXISTS `nexpose_endpoint_fingerprints`;
CREATE TABLE `nexpose_endpoint_fingerprints` (
  `endpoint_fingerprint_id` int(10) NOT NULL AUTO_INCREMENT,
  `endpoint_id` varchar(100) DEFAULT NULL,
  `endpoint_certainty` float(4,2) DEFAULT NULL,
  `endpoint_vendor` varchar(255) DEFAULT NULL,
  `endpoint_family` varchar(255) DEFAULT NULL,
  `endpoint_product` varchar(255) DEFAULT NULL,
  `endpoint_version` varchar(255) DEFAULT NULL,
  `agency` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`endpoint_fingerprint_id`),
  KEY `endpoint_index` (`endpoint_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2892 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nexpose_endpoints
-- ----------------------------
DROP TABLE IF EXISTS `nexpose_endpoints`;
CREATE TABLE `nexpose_endpoints` (
  `endpoint_id` int(10) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(100) DEFAULT NULL,
  `endpoint_protocol` varchar(10) DEFAULT NULL,
  `endpoint_port` varchar(10) DEFAULT NULL,
  `endpoint_status` varchar(10) DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `agency` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`endpoint_id`),
  KEY `endpoint_index` (`device_id`,`endpoint_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11157 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nexpose_exploits
-- ----------------------------
DROP TABLE IF EXISTS `nexpose_exploits`;
CREATE TABLE `nexpose_exploits` (
  `exploit_table_id` int(10) NOT NULL AUTO_INCREMENT,
  `vuln_id` varchar(255) DEFAULT NULL,
  `exploit_id` varchar(10) DEFAULT NULL,
  `exploit_title` varchar(255) DEFAULT NULL,
  `exploit_type` varchar(100) DEFAULT NULL,
  `exploit_link` varchar(255) DEFAULT NULL,
  `exploit_skillLevel` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`exploit_table_id`),
  KEY `exploit_index` (`vuln_id`,`exploit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2874 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nexpose_nodes
-- ----------------------------
DROP TABLE IF EXISTS `nexpose_nodes`;
CREATE TABLE `nexpose_nodes` (
  `node_id` int(10) NOT NULL AUTO_INCREMENT,
  `node_address` varchar(100) DEFAULT NULL,
  `node_status` varchar(100) DEFAULT NULL,
  `node_hardware_address` varchar(100) DEFAULT NULL,
  `node_device_id` varchar(100) DEFAULT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `site_importance` varchar(255) DEFAULT NULL,
  `scan_template` varchar(255) DEFAULT NULL,
  `node_risk_score` varchar(255) DEFAULT NULL,
  `node_name` varchar(255) DEFAULT NULL,
  `agency` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`node_id`),
  KEY `node_index` (`node_address`,`node_device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1526 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nexpose_scans
-- ----------------------------
DROP TABLE IF EXISTS `nexpose_scans`;
CREATE TABLE `nexpose_scans` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `scan_id` varchar(100) NOT NULL,
  `scan_name` varchar(255) DEFAULT NULL,
  `scan_startTime` varchar(20) DEFAULT NULL,
  `scan_endTime` varchar(20) DEFAULT NULL,
  `scan_status` varchar(20) DEFAULT NULL,
  `xml_version` varchar(20) DEFAULT NULL,
  `agency` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scan_index` (`scan_id`,`scan_name`,`scan_startTime`,`scan_endTime`,`agency`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nexpose_tags
-- ----------------------------
DROP TABLE IF EXISTS `nexpose_tags`;
CREATE TABLE `nexpose_tags` (
  `tag_id` int(10) NOT NULL AUTO_INCREMENT,
  `vuln_id` varchar(255) DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`tag_id`),
  KEY `tag_index` (`vuln_id`,`tag`)
) ENGINE=InnoDB AUTO_INCREMENT=25371 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nexpose_temp_nodes
-- ----------------------------
DROP TABLE IF EXISTS `nexpose_temp_nodes`;
CREATE TABLE `nexpose_temp_nodes` (
  `node_address` varchar(255) DEFAULT NULL,
  `node_device_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nexpose_temp_vulnerabiliites
-- ----------------------------
DROP TABLE IF EXISTS `nexpose_temp_vulnerabiliites`;
CREATE TABLE `nexpose_temp_vulnerabiliites` (
  `vuln_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nexpose_tests
-- ----------------------------
DROP TABLE IF EXISTS `nexpose_tests`;
CREATE TABLE `nexpose_tests` (
  `tests_id` int(10) NOT NULL AUTO_INCREMENT,
  `test_id` varchar(100) DEFAULT NULL,
  `scan_id` varchar(100) DEFAULT NULL,
  `device_id` varchar(100) DEFAULT NULL,
  `endpoint_id` varchar(100) DEFAULT NULL,
  `test_key` varchar(100) DEFAULT NULL,
  `test_status` varchar(100) DEFAULT NULL,
  `test_vulnerable_since` varchar(100) DEFAULT NULL,
  `test_pci_compliance_status` varchar(100) DEFAULT NULL,
  `test_paragraph` text,
  `agency` varchar(255) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`tests_id`),
  KEY `tests_index` (`test_id`,`scan_id`,`device_id`,`endpoint_id`,`agency`)
) ENGINE=InnoDB AUTO_INCREMENT=35822 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nexpose_vulnerabilities
-- ----------------------------
DROP TABLE IF EXISTS `nexpose_vulnerabilities`;
CREATE TABLE `nexpose_vulnerabilities` (
  `vuln_table_id` int(10) NOT NULL AUTO_INCREMENT,
  `vuln_id` varchar(255) DEFAULT NULL,
  `vuln_title` varchar(255) DEFAULT NULL,
  `pciSeverity` varchar(100) DEFAULT NULL,
  `cvssScore` float(4,1) DEFAULT NULL,
  `cvssVector` varchar(50) DEFAULT NULL,
  `vuln_published` varchar(50) DEFAULT NULL,
  `vuln_added` varchar(50) DEFAULT NULL,
  `vuln_modified` varchar(50) DEFAULT NULL,
  `riskScore` varchar(50) DEFAULT NULL,
  `description` text,
  `solution` text,
  `appleList` varchar(255) DEFAULT NULL,
  `bidList` varchar(255) DEFAULT NULL,
  `certList` varchar(255) DEFAULT NULL,
  `cveList` varchar(255) DEFAULT NULL,
  `msftList` varchar(255) DEFAULT NULL,
  `osvdbList` varchar(255) DEFAULT NULL,
  `redhatList` varchar(255) DEFAULT NULL,
  `urlList` text,
  `xfList` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`vuln_table_id`),
  KEY `vuln_index` (`vuln_id`,`vuln_title`)
) ENGINE=InnoDB AUTO_INCREMENT=8529 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=142600 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=15235 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=84587 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=74115 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=72947 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=148039 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=318340 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=855 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for nmap_temp_host_nse
-- ----------------------------
DROP TABLE IF EXISTS `nmap_temp_host_nse`;
CREATE TABLE `nmap_temp_host_nse` (
  `script_id` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
-- Table structure for nmap_temp_port_nse
-- ----------------------------
DROP TABLE IF EXISTS `nmap_temp_port_nse`;
CREATE TABLE `nmap_temp_port_nse` (
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
-- Table structure for phpsec
-- ----------------------------
DROP TABLE IF EXISTS `phpsec`;
CREATE TABLE `phpsec` (
  `type` varchar(255) NOT NULL COMMENT 'Type of data.',
  `id` varchar(255) NOT NULL COMMENT 'Item ID.',
  `mac` binary(32) NOT NULL COMMENT 'Message Authentication Message.',
  `time` int(11) unsigned NOT NULL COMMENT 'Unix time stamp of creation time.',
  `data` text NOT NULL COMMENT 'Serialized object.',
  UNIQUE KEY `id` (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------

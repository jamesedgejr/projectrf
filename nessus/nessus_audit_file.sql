
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for nessus_audit_file
-- ----------------------------
DROP TABLE IF EXISTS `nessus_audit_file`;
CREATE TABLE `nessus_audit_file` (
  `auditCheckID` int(10) NOT NULL AUTO_INCREMENT,
  `account_type` varchar(50) DEFAULT NULL,
  `acl_option` varchar(50) DEFAULT NULL,
  `audit_file_name` varchar(100) NOT NULL,
  `check_policy` varchar(50) DEFAULT NULL,
  `check_type` varchar(7) DEFAULT NULL,
  `custom_item_check_type` varchar(100) DEFAULT NULL,
  `custom_item_type` varchar(50) DEFAULT NULL,
  `description` text,
  `file_element` varchar(100) DEFAULT NULL,
  `group_name` varchar(50) DEFAULT NULL,
  `info_element` text,  
  `reg_item` varchar(50) DEFAULT NULL,
  `reg_key` varchar(200) DEFAULT NULL,
  `reg_option` varchar(50) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `right_type` varchar(100) DEFAULT NULL,
  `service` varchar(50) DEFAULT NULL,
  `service_name` varchar(50) DEFAULT NULL,
  `svc_option` varchar(50) DEFAULT NULL,
  `value_data` varchar(50) DEFAULT NULL,
  `value_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`auditCheckID`),
  KEY `audit_file_index` (`audit_file_name`,`check_type`,`custom_item_type`,`value_type`,`value_data`,`service_name`,`svc_option`,`acl_option`,`file_element`,`reg_key`,`reg_item`) USING HASH
) ENGINE=MyISAM AUTO_INCREMENT=39681 DEFAULT CHARSET=latin1;


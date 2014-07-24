--
-- Table structure for table `phpsec`
--
CREATE TABLE IF NOT EXISTS `phpsec` (
  `type` varchar(255) NOT NULL COMMENT 'Type of data.',
  `id` varchar(255) NOT NULL COMMENT 'Item ID.',
  `mac` binary(32) NOT NULL COMMENT 'Message Authentication Message.',
  `time` int(11) unsigned NOT NULL COMMENT 'Unix time stamp of creation time.',
  `data` text NOT NULL COMMENT 'Serialized object.',
  UNIQUE KEY `id` (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
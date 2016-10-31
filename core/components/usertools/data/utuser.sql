CREATE TABLE `modx_ut_users` (
  `id` int(10) unsigned NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `inn` varchar(10) DEFAULT NULL,
  `kpp` varchar(10) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `type` enum('ul','fl') NOT NULL DEFAULT 'ul',
  `director_fullname` varchar(255) DEFAULT NULL,
  `accept_rules` tinyint(1) NOT NULL DEFAULT '0',
  `discount` smallint(5) unsigned NOT NULL DEFAULT '0',
  `max_discount` smallint(5) unsigned DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `registerdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='utUserData';
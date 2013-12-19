CREATE TABLE `shorty` (
  `slug` varchar(25) NOT NULL DEFAULT '',
  `url` text,
  `hits` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
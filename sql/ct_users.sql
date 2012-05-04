CREATE TABLE `ct_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `access` smallint(6) NOT NULL,
  `cookieid` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
);

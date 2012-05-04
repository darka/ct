CREATE TABLE `ct_censoredwords` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `word` varchar(50) NOT NULL,
  `replacement` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
);

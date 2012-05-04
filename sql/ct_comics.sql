CREATE TABLE `ct_comics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `filename` varchar(32) NOT NULL,
  `post_date` datetime NOT NULL,
  `mod_date` datetime NOT NULL,
  `comicgroup_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
);

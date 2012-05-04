CREATE TABLE `ct_comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `text` text NOT NULL,
  `post_date` datetime NOT NULL,
  `mod_date` datetime NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `comic_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `options_meta` (
`meta_id` int(11) NOT NULL,
  `meta_key` varchar(60) NOT NULL DEFAULT '',
  `meta_value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `options_meta` ADD PRIMARY KEY (`meta_id`), ADD UNIQUE KEY `option_name` (`meta_key`);

ALTER TABLE `options_meta` MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `options_meta` SELECT * FROM `et_option`;

INSERT INTO `permission` VALUES('', 'access_gradebook', 'Access Gradebook');

UPDATE `options_meta` SET meta_value = '00039.1' WHERE meta_key = 'dbversion';
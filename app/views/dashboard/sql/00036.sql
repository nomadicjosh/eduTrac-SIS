CREATE TABLE IF NOT EXISTS `stu_rgn_cart` (
  `stuID` bigint(20) NOT NULL,
  `courseSecID` bigint(20) NOT NULL,
  `deleteDate` date NOT NULL,
  UNIQUE KEY `stu_rgn` (`stuID`,`courseSecID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `course` ADD COLUMN `creditType` varchar(6) NOT NULL DEFAULT 'I' AFTER `courseDesc`;

ALTER TABLE `met_link` ADD COLUMN `sort` tinyint(2) NOT NULL AFTER `status`;

ALTER TABLE `met_page` ADD COLUMN `sort` tinyint(2) NOT NULL AFTER `status`;

ALTER TABLE `course_sec` CHANGE `stuReg` `webReg` enum('1','0') NOT NULL DEFAULT '1';

INSERT INTO `permission` VALUES('', 'delete_student', 'Delete Student');

UPDATE `et_option` SET option_value = '00037' WHERE option_name = 'dbversion';
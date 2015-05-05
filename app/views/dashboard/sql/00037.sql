INSERT INTO `screen` VALUES('', 'EXTR', 'External Course', 'course/extr/');

INSERT INTO `screen` VALUES('', 'ATCEQ', 'New Transfer Course Equivalency', 'course/atceq/');

INSERT INTO `screen` VALUES('', 'TCEQ', 'Transfer Course Equivalency', 'course/tceq/');

INSERT INTO `screen` VALUES('', 'TCRE', 'Transfer Credit', 'course/tcre/');

ALTER TABLE `stu_acad_cred` CHANGE `courseSecCode` `courseSecCode` varchar(50) DEFAULT NULL;

ALTER TABLE `stu_acad_cred` ADD COLUMN `addDate` date DEFAULT NULL AFTER `addedBy`;

ALTER TABLE `person` ADD COLUMN `photo` varchar(255) DEFAULT NULL AFTER `emergency_contact_phone`;

CREATE TABLE IF NOT EXISTS `external_course` (
  `extrID` bigint(20) NOT NULL AUTO_INCREMENT,
  `courseTitle` varchar(180) NOT NULL,
  `instCode` varchar(11) NOT NULL,
  `courseName` varchar(60) NOT NULL,
  `term` varchar(11) NOT NULL,
  `credits` double(4,2) NOT NULL,
  `currStatus` enum('A','I','P','O') NOT NULL DEFAULT 'A',
  `statusDate` date NOT NULL,
  `minGrade` varchar(2) NOT NULL,
  `comments` text NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`extrID`),
  KEY `instCode` (`instCode`),
  KEY `addedBy` (`addedBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `transfer_credit` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `equivID` bigint(20) NOT NULL,
  `stuAcadCredID` bigint(20) NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `addDate` date NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `equivID` (`equivID`),
  KEY `stuAcadCredID` (`stuAcadCredID`),
  KEY `addedBy` (`addedBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `transfer_equivalent` (
  `equivID` bigint(20) NOT NULL AUTO_INCREMENT,
  `extrID` bigint(20) NOT NULL,
  `courseID` bigint(20) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `grade` varchar(2) NOT NULL,
  `comment` text NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `addDate` date NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`equivID`),
  KEY `extrID` (`extrID`),
  KEY `courseID` (`courseID`),
  KEY `addedBy` (`addedBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `external_course` ADD FOREIGN KEY (`instCode`) REFERENCES `institution` (`fice_ceeb`) ON UPDATE CASCADE;

ALTER TABLE `external_course` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_credit` ADD FOREIGN KEY (`equivID`) REFERENCES `transfer_equivalent` (`equivID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_credit` ADD FOREIGN KEY (`stuAcadCredID`) REFERENCES `stu_acad_cred` (`stuAcadCredID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_credit` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_equivalent` ADD FOREIGN KEY (`extrID`) REFERENCES `external_course` (`extrID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_equivalent` ADD FOREIGN KEY (`courseID`) REFERENCES `course` (`courseID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_equivalent` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

UPDATE `et_option` SET option_value = '00038' WHERE option_name = 'dbversion';
ALTER TABLE `acad_program` CHANGE `acadProgCode` `acadProgCode` varchar(20) NOT NULL;

ALTER TABLE `application` CHANGE `acadProgCode` `acadProgCode` varchar(20) NOT NULL;

ALTER TABLE `stu_acad_level` CHANGE `acadProgCode` `acadProgCode` varchar(20) NOT NULL;

ALTER TABLE `student` CHANGE `status` `status` enum('A','I') NOT NULL DEFAULT 'A';

ALTER TABLE `stu_program` CHANGE `acadProgCode` `acadProgCode` varchar(20) NOT NULL;

ALTER TABLE `application` ADD COLUMN `applStatus` enum('Pending','Under Review','Accepted','Not Accepted') NOT NULL AFTER `ACT_Math`;

ALTER TABLE `error` CHANGE `ID` `ID` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `person` ADD COLUMN `status` enum('A','I') NOT NULL DEFAULT 'A' AFTER `password`;

ALTER TABLE `bill` CHANGE `comment` `stu_comments` text NOT NULL;

ALTER TABLE `bill` ADD COLUMN `staff_comments` text NOT NULL AFTER `stu_comments`;

ALTER TABLE `application` ADD COLUMN `appl_comments` text NOT NULL AFTER `applDate`;

ALTER TABLE `application` ADD COLUMN `staff_comments` text NOT NULL AFTER `appl_comments`;

ALTER TABLE `stu_program` ADD COLUMN `comments` text NOT NULL AFTER `endDate`;

CREATE TABLE IF NOT EXISTS `hiatus` (
  `shisID` bigint(20) NOT NULL AUTO_INCREMENT,
  `stuID` bigint(20) NOT NULL,
  `shisCode` varchar(6) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `comment` text NOT NULL,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`shisID`),
  KEY `shisCode` (`shisCode`),
  KEY `stuID` (`stuID`),
  KEY `addedBy` (`addedBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `met_link` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `link_title` varchar(180) NOT NULL,
  `link_src` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `met_news` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `news_title` varchar(255) NOT NULL,
  `news_slug` varchar(255) NOT NULL,
  `news_content` text NOT NULL,
  `status` enum('draft','publish') NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `addDate` date NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='News and announcements for eduTrac''s frontend.';

CREATE TABLE IF NOT EXISTS `met_page` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `page_title` varchar(255) NOT NULL,
  `page_slug` varchar(255) NOT NULL,
  `page_content` text NOT NULL,
  `status` enum('draft','publish') NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `addDate` date NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Pages created by an admin for eduTrac''s frontend.';

UPDATE `et_option` SET `option_name` = 'institution_name' WHERE `option_name` = 'site_title';

INSERT INTO `et_option` VALUES('', 'myet_welcome_message', '<p>Welcome to the <em>my</em>eduTrac campus portal. The <em>my</em>eduTrac campus portal&nbsp;is your personalized campus web site at Eastbound University.</p>\r\n<p>If you are a prospective student who is interested in applying to the college, checkout the <a href="pages/?pg=admissions">admissions</a>&nbsp;page for more information.</p>');

INSERT INTO `et_option` VALUES('', 'contact_phone', '888.888.8888');

INSERT INTO `et_option` VALUES('', 'contact_email', 'contact@colegio.edu');

INSERT INTO `et_option` VALUES('', 'mailing_address', '10 Eliot Street, Suite 2\r\nSomerville, MA 02140');

INSERT INTO `et_option` VALUES('', 'enable_myet_portal', '0');

INSERT INTO `et_option` VALUES('', 'screen_caching', '1');

INSERT INTO `et_option` VALUES('', 'db_caching', '1');

INSERT INTO `et_option` VALUES('', 'admissions_email', 'admissions@colegio.edu');

INSERT INTO `et_option` VALUES('', 'coa_form_text', '<p>Dear Admin,</p>\r\n<p>#name# has submitted a change of address. Please see below for details.</p>\r\n<p><strong>ID:</strong> #id#</p>\r\n<p><strong>Address1:</strong> #address1#</p>\r\n<p><strong>Address2:</strong> #address2#</p>\r\n<p><strong>City:</strong> #city#</p>\r\n<p><strong>State:</strong> #state#</p>\r\n<p><strong>Zip:</strong> #zip#</p>\r\n<p><strong>Country:</strong> #country#</p>\r\n<p><strong>Phone:</strong> #phone#</p>\r\n<p><strong>Email:</strong> #email#</p>\r\n<p>&nbsp;</p>\r\n<p>----<br /><em>This is a system generated email.</em></p>');

INSERT INTO `et_option` VALUES('', 'enable_myet_appl_form', '1');

INSERT INTO `et_option` VALUES('', 'enable_myet_portal_signup', '0');

INSERT INTO `et_option` VALUES('', 'myet_offline_message', 'Please excuse the dust. We are giving the portal a new facelift. Please try back again in an hour.\r\n\r\nSincerely,\r\nIT Department');

ALTER TABLE `campus` CHANGE `site_title` `institution_name` varchar(255) NOT NULL;
  
UPDATE `et_option` SET option_value = '00034' WHERE option_name = 'dbversion';
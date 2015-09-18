CREATE TABLE IF NOT EXISTS `acad_program` (
  `acadProgID` bigint(20) NOT NULL AUTO_INCREMENT,
  `acadProgCode` varchar(20) NOT NULL,
  `acadProgTitle` varchar(180) NOT NULL,
  `programDesc` varchar(255) NOT NULL,
  `currStatus` varchar(1) NOT NULL,
  `statusDate` date NOT NULL,
  `deptCode` varchar(11) NOT NULL,
  `schoolCode` varchar(11) NOT NULL,
  `acadYearCode` varchar(11) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `degreeCode` varchar(11) NOT NULL,
  `ccdCode` varchar(11) DEFAULT NULL,
  `majorCode` varchar(11) DEFAULT NULL,
  `minorCode` varchar(11) DEFAULT NULL,
  `specCode` varchar(11) DEFAULT NULL,
  `acadLevelCode` varchar(11) NOT NULL,
  `cipCode` varchar(11) DEFAULT NULL,
  `locationCode` varchar(11) DEFAULT NULL,
  `approvedDate` date NOT NULL,
  `approvedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`acadProgID`),
  UNIQUE KEY `acadProgCode` (`acadProgCode`),
  KEY `acad_prog_code` (`acadProgCode`),
  KEY `acad_level_code` (`acadLevelCode`),
  KEY `deptCode` (`deptCode`),
  KEY `schoolCode` (`schoolCode`),
  KEY `acadYearCode` (`acadYearCode`),
  KEY `degreeCode` (`degreeCode`),
  KEY `ccdCode` (`ccdCode`),
  KEY `majorCode` (`majorCode`),
  KEY `minorCode` (`minorCode`),
  KEY `specCode` (`specCode`),
  KEY `cipCode` (`cipCode`),
  KEY `locationCode` (`locationCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `acad_year` (
  `acadYearID` bigint(20) NOT NULL AUTO_INCREMENT,
  `acadYearCode` varchar(11) NOT NULL,
  `acadYearDesc` varchar(30) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`acadYearID`),
  UNIQUE KEY `acadYear` (`acadYearCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `acad_year` VALUES(00000000001, 'NULL', 'Null', '{now}');

CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `action` varchar(50) NOT NULL,
  `process` varchar(255) NOT NULL,
  `record` text,
  `uname` varchar(180) NOT NULL,
  `created_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `address` (
  `addressID` bigint(20) NOT NULL AUTO_INCREMENT,
  `personID` bigint(20) NOT NULL,
  `address1` varchar(80) NOT NULL,
  `address2` varchar(80) NOT NULL,
  `city` varchar(30) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `country` varchar(2) NOT NULL,
  `addressType` varchar(2) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `addressStatus` varchar(2) NOT NULL,
  `phone1` varchar(15) NOT NULL,
  `phone2` varchar(15) NOT NULL,
  `ext1` varchar(5) NOT NULL,
  `ext2` varchar(5) NOT NULL,
  `phoneType1` varchar(3) NOT NULL,
  `phoneType2` varchar(3) NOT NULL,
  `email1` varchar(80) NOT NULL,
  `email2` varchar(80) NOT NULL,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`addressID`),
  KEY `personID` (`personID`),
  KEY `addedBy` (`addedBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `address` VALUES(00000000001, 00000001, '125 Montgomery Street', '#2', 'Cambridge', 'MA', '02140', 'US', 'P', '2013-08-01', '0000-00-00', 'C', '6718997836', '', '', '', 'CEL', '', 'edutrac@7mediaws.org', '', '{addDate}', 00000001, '{now}');

CREATE TABLE IF NOT EXISTS `application` (
  `applID` bigint(20) NOT NULL AUTO_INCREMENT,
  `personID` bigint(20) NOT NULL,
  `acadProgCode` varchar(20) NOT NULL,
  `startTerm` varchar(11) NOT NULL,
  `admitStatus` varchar(2) DEFAULT NULL,
  `PSAT_Verbal` varchar(5) NOT NULL,
  `PSAT_Math` varchar(5) NOT NULL,
  `SAT_Verbal` varchar(5) NOT NULL,
  `SAT_Math` varchar(5) NOT NULL,
  `ACT_English` varchar(5) NOT NULL,
  `ACT_Math` varchar(5) NOT NULL,
  `applStatus` enum('Pending','Under Review','Accepted','Not Accepted') NOT NULL,
  `applDate` date NOT NULL,
  `appl_comments` text NOT NULL,
  `staff_comments` text NOT NULL,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`applID`),
  UNIQUE KEY `application` (`personID`,`acadProgCode`),
  KEY `startTerm` (`startTerm`),
  KEY `addedBy` (`addedBy`),
  KEY `acadProgCode` (`acadProgCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `assignment` (
  `assignID` bigint(20) NULL AUTO_INCREMENT,
  `courseSecID` bigint(20) DEFAULT NULL,
  `facID` bigint(20) NOT NULL,
  `shortName` varchar(6) NOT NULL,
  `title` varchar(180) NOT NULL,
  `dueDate` date NOT NULL,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`assignID`),
  KEY `courseSecID` (`courseSecID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `attendance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `courseSecID` bigint(20) DEFAULT NULL,
  `stuID` bigint(20) NOT NULL,
  `status` varchar(1) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_index` (`courseSecID`,`stuID`,`date`),
  KEY `stuID` (`stuID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `billing_table` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(180) NOT NULL,
  `amount` double(6,2) NOT NULL DEFAULT '0.00',
  `status` enum('A','I') NOT NULL DEFAULT 'A',
  `addDate` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `building` (
  `buildingID` int(11) NOT NULL AUTO_INCREMENT,
  `buildingCode` varchar(11) NOT NULL,
  `buildingName` varchar(180) NOT NULL,
  `locationCode` varchar(11) NOT NULL,
  PRIMARY KEY (`buildingID`),
  UNIQUE KEY `buildingCode` (`buildingCode`),
  KEY `locationCode` (`locationCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `building` VALUES(00000000001, 'NULL', '', 'NULL');

CREATE TABLE IF NOT EXISTS `campus` (
  `campusID` bigint(20) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `institution_name` varchar(255) NOT NULL,
  `path` varchar(80) NOT NULL DEFAULT '/',
  `dbname` varchar(255) NOT NULL,
  `registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `public` tinyint(2) NOT NULL DEFAULT '1',
  `archived` tinyint(2) NOT NULL DEFAULT '0',
  `deleted` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`campusID`),
  UNIQUE KEY `campus` (`domain`,`path`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ccd` (
  `ccdID` int(11) NOT NULL AUTO_INCREMENT,
  `ccdCode` varchar(11) NOT NULL,
  `ccdName` varchar(80) NOT NULL,
  `addDate` date NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ccdID`),
  UNIQUE KEY `ccdKey` (`ccdCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `ccd` VALUES(00000000001, 'NULL', 'Null', '{addDate}', '{now}');

CREATE TABLE IF NOT EXISTS `cip` (
  `cipID` int(11) NOT NULL AUTO_INCREMENT,
  `cipCode` varchar(11) NOT NULL,
  `cipName` varchar(80) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cipID`),
  UNIQUE KEY `cipKey` (`cipCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `cip` VALUES(00000000001, 'NULL', 'Null', '{now}');

CREATE TABLE IF NOT EXISTS `class_year` (
  `yearID` int(11) NOT NULL AUTO_INCREMENT,
  `acadLevelCode` varchar(4) NOT NULL,
  `classYear` varchar(4) NOT NULL,
  `minCredits` double(4,1) NOT NULL DEFAULT '0.0',
  `maxCredits` double(4,1) NOT NULL DEFAULT '0.0',
  PRIMARY KEY (`yearID`),
  UNIQUE KEY `classYear` (`classYear`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `country` (
  `country_id` int(5) NOT NULL AUTO_INCREMENT,
  `iso2` char(2) DEFAULT NULL,
  `short_name` varchar(80) NOT NULL DEFAULT '',
  `long_name` varchar(80) NOT NULL DEFAULT '',
  `iso3` char(3) DEFAULT NULL,
  `numcode` varchar(6) DEFAULT NULL,
  `un_member` varchar(12) DEFAULT NULL,
  `calling_code` varchar(8) DEFAULT NULL,
  `cctld` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `country` VALUES(1, 'AF', 'Afghanistan', 'Islamic Republic of Afghanistan', 'AFG', '004', 'yes', '93', '.af');

INSERT INTO `country` VALUES(2, 'AX', 'Aland Islands', '&Aring;land Islands', 'ALA', '248', 'no', '358', '.ax');

INSERT INTO `country` VALUES(3, 'AL', 'Albania', 'Republic of Albania', 'ALB', '008', 'yes', '355', '.al');

INSERT INTO `country` VALUES(4, 'DZ', 'Algeria', 'People''s Democratic Republic of Algeria', 'DZA', '012', 'yes', '213', '.dz');

INSERT INTO `country` VALUES(5, 'AS', 'American Samoa', 'American Samoa', 'ASM', '016', 'no', '1+684', '.as');

INSERT INTO `country` VALUES(6, 'AD', 'Andorra', 'Principality of Andorra', 'AND', '020', 'yes', '376', '.ad');

INSERT INTO `country` VALUES(7, 'AO', 'Angola', 'Republic of Angola', 'AGO', '024', 'yes', '244', '.ao');

INSERT INTO `country` VALUES(8, 'AI', 'Anguilla', 'Anguilla', 'AIA', '660', 'no', '1+264', '.ai');

INSERT INTO `country` VALUES(9, 'AQ', 'Antarctica', 'Antarctica', 'ATA', '010', 'no', '672', '.aq');

INSERT INTO `country` VALUES(10, 'AG', 'Antigua and Barbuda', 'Antigua and Barbuda', 'ATG', '028', 'yes', '1+268', '.ag');

INSERT INTO `country` VALUES(11, 'AR', 'Argentina', 'Argentine Republic', 'ARG', '032', 'yes', '54', '.ar');

INSERT INTO `country` VALUES(12, 'AM', 'Armenia', 'Republic of Armenia', 'ARM', '051', 'yes', '374', '.am');

INSERT INTO `country` VALUES(13, 'AW', 'Aruba', 'Aruba', 'ABW', '533', 'no', '297', '.aw');

INSERT INTO `country` VALUES(14, 'AU', 'Australia', 'Commonwealth of Australia', 'AUS', '036', 'yes', '61', '.au');

INSERT INTO `country` VALUES(15, 'AT', 'Austria', 'Republic of Austria', 'AUT', '040', 'yes', '43', '.at');

INSERT INTO `country` VALUES(16, 'AZ', 'Azerbaijan', 'Republic of Azerbaijan', 'AZE', '031', 'yes', '994', '.az');

INSERT INTO `country` VALUES(17, 'BS', 'Bahamas', 'Commonwealth of The Bahamas', 'BHS', '044', 'yes', '1+242', '.bs');

INSERT INTO `country` VALUES(18, 'BH', 'Bahrain', 'Kingdom of Bahrain', 'BHR', '048', 'yes', '973', '.bh');

INSERT INTO `country` VALUES(19, 'BD', 'Bangladesh', 'People''s Republic of Bangladesh', 'BGD', '050', 'yes', '880', '.bd');

INSERT INTO `country` VALUES(20, 'BB', 'Barbados', 'Barbados', 'BRB', '052', 'yes', '1+246', '.bb');

INSERT INTO `country` VALUES(21, 'BY', 'Belarus', 'Republic of Belarus', 'BLR', '112', 'yes', '375', '.by');

INSERT INTO `country` VALUES(22, 'BE', 'Belgium', 'Kingdom of Belgium', 'BEL', '056', 'yes', '32', '.be');

INSERT INTO `country` VALUES(23, 'BZ', 'Belize', 'Belize', 'BLZ', '084', 'yes', '501', '.bz');

INSERT INTO `country` VALUES(24, 'BJ', 'Benin', 'Republic of Benin', 'BEN', '204', 'yes', '229', '.bj');

INSERT INTO `country` VALUES(25, 'BM', 'Bermuda', 'Bermuda Islands', 'BMU', '060', 'no', '1+441', '.bm');

INSERT INTO `country` VALUES(26, 'BT', 'Bhutan', 'Kingdom of Bhutan', 'BTN', '064', 'yes', '975', '.bt');

INSERT INTO `country` VALUES(27, 'BO', 'Bolivia', 'Plurinational State of Bolivia', 'BOL', '068', 'yes', '591', '.bo');

INSERT INTO `country` VALUES(28, 'BQ', 'Bonaire, Sint Eustatius and Saba', 'Bonaire, Sint Eustatius and Saba', 'BES', '535', 'no', '599', '.bq');

INSERT INTO `country` VALUES(29, 'BA', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'BIH', '070', 'yes', '387', '.ba');

INSERT INTO `country` VALUES(30, 'BW', 'Botswana', 'Republic of Botswana', 'BWA', '072', 'yes', '267', '.bw');

INSERT INTO `country` VALUES(31, 'BV', 'Bouvet Island', 'Bouvet Island', 'BVT', '074', 'no', 'NONE', '.bv');

INSERT INTO `country` VALUES(32, 'BR', 'Brazil', 'Federative Republic of Brazil', 'BRA', '076', 'yes', '55', '.br');

INSERT INTO `country` VALUES(33, 'IO', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'IOT', '086', 'no', '246', '.io');

INSERT INTO `country` VALUES(34, 'BN', 'Brunei', 'Brunei Darussalam', 'BRN', '096', 'yes', '673', '.bn');

INSERT INTO `country` VALUES(35, 'BG', 'Bulgaria', 'Republic of Bulgaria', 'BGR', '100', 'yes', '359', '.bg');

INSERT INTO `country` VALUES(36, 'BF', 'Burkina Faso', 'Burkina Faso', 'BFA', '854', 'yes', '226', '.bf');

INSERT INTO `country` VALUES(37, 'BI', 'Burundi', 'Republic of Burundi', 'BDI', '108', 'yes', '257', '.bi');

INSERT INTO `country` VALUES(38, 'KH', 'Cambodia', 'Kingdom of Cambodia', 'KHM', '116', 'yes', '855', '.kh');

INSERT INTO `country` VALUES(39, 'CM', 'Cameroon', 'Republic of Cameroon', 'CMR', '120', 'yes', '237', '.cm');

INSERT INTO `country` VALUES(40, 'CA', 'Canada', 'Canada', 'CAN', '124', 'yes', '1', '.ca');

INSERT INTO `country` VALUES(41, 'CV', 'Cape Verde', 'Republic of Cape Verde', 'CPV', '132', 'yes', '238', '.cv');

INSERT INTO `country` VALUES(42, 'KY', 'Cayman Islands', 'The Cayman Islands', 'CYM', '136', 'no', '1+345', '.ky');

INSERT INTO `country` VALUES(43, 'CF', 'Central African Republic', 'Central African Republic', 'CAF', '140', 'yes', '236', '.cf');

INSERT INTO `country` VALUES(44, 'TD', 'Chad', 'Republic of Chad', 'TCD', '148', 'yes', '235', '.td');

INSERT INTO `country` VALUES(45, 'CL', 'Chile', 'Republic of Chile', 'CHL', '152', 'yes', '56', '.cl');

INSERT INTO `country` VALUES(46, 'CN', 'China', 'People''s Republic of China', 'CHN', '156', 'yes', '86', '.cn');

INSERT INTO `country` VALUES(47, 'CX', 'Christmas Island', 'Christmas Island', 'CXR', '162', 'no', '61', '.cx');

INSERT INTO `country` VALUES(48, 'CC', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'CCK', '166', 'no', '61', '.cc');

INSERT INTO `country` VALUES(49, 'CO', 'Colombia', 'Republic of Colombia', 'COL', '170', 'yes', '57', '.co');

INSERT INTO `country` VALUES(50, 'KM', 'Comoros', 'Union of the Comoros', 'COM', '174', 'yes', '269', '.km');

INSERT INTO `country` VALUES(51, 'CG', 'Congo', 'Republic of the Congo', 'COG', '178', 'yes', '242', '.cg');

INSERT INTO `country` VALUES(52, 'CK', 'Cook Islands', 'Cook Islands', 'COK', '184', 'some', '682', '.ck');

INSERT INTO `country` VALUES(53, 'CR', 'Costa Rica', 'Republic of Costa Rica', 'CRI', '188', 'yes', '506', '.cr');

INSERT INTO `country` VALUES(54, 'CI', 'Cote d''ivoire (Ivory Coast)', 'Republic of C&ocirc;te D''Ivoire (Ivory Coast)', 'CIV', '384', 'yes', '225', '.ci');

INSERT INTO `country` VALUES(55, 'HR', 'Croatia', 'Republic of Croatia', 'HRV', '191', 'yes', '385', '.hr');

INSERT INTO `country` VALUES(56, 'CU', 'Cuba', 'Republic of Cuba', 'CUB', '192', 'yes', '53', '.cu');

INSERT INTO `country` VALUES(57, 'CW', 'Curacao', 'Cura&ccedil;ao', 'CUW', '531', 'no', '599', '.cw');

INSERT INTO `country` VALUES(58, 'CY', 'Cyprus', 'Republic of Cyprus', 'CYP', '196', 'yes', '357', '.cy');

INSERT INTO `country` VALUES(59, 'CZ', 'Czech Republic', 'Czech Republic', 'CZE', '203', 'yes', '420', '.cz');

INSERT INTO `country` VALUES(60, 'CD', 'Democratic Republic of the Congo', 'Democratic Republic of the Congo', 'COD', '180', 'yes', '243', '.cd');

INSERT INTO `country` VALUES(61, 'DK', 'Denmark', 'Kingdom of Denmark', 'DNK', '208', 'yes', '45', '.dk');

INSERT INTO `country` VALUES(62, 'DJ', 'Djibouti', 'Republic of Djibouti', 'DJI', '262', 'yes', '253', '.dj');

INSERT INTO `country` VALUES(63, 'DM', 'Dominica', 'Commonwealth of Dominica', 'DMA', '212', 'yes', '1+767', '.dm');

INSERT INTO `country` VALUES(64, 'DO', 'Dominican Republic', 'Dominican Republic', 'DOM', '214', 'yes', '1+809, 8', '.do');

INSERT INTO `country` VALUES(65, 'EC', 'Ecuador', 'Republic of Ecuador', 'ECU', '218', 'yes', '593', '.ec');

INSERT INTO `country` VALUES(66, 'EG', 'Egypt', 'Arab Republic of Egypt', 'EGY', '818', 'yes', '20', '.eg');

INSERT INTO `country` VALUES(67, 'SV', 'El Salvador', 'Republic of El Salvador', 'SLV', '222', 'yes', '503', '.sv');

INSERT INTO `country` VALUES(68, 'GQ', 'Equatorial Guinea', 'Republic of Equatorial Guinea', 'GNQ', '226', 'yes', '240', '.gq');

INSERT INTO `country` VALUES(69, 'ER', 'Eritrea', 'State of Eritrea', 'ERI', '232', 'yes', '291', '.er');

INSERT INTO `country` VALUES(70, 'EE', 'Estonia', 'Republic of Estonia', 'EST', '233', 'yes', '372', '.ee');

INSERT INTO `country` VALUES(71, 'ET', 'Ethiopia', 'Federal Democratic Republic of Ethiopia', 'ETH', '231', 'yes', '251', '.et');

INSERT INTO `country` VALUES(72, 'FK', 'Falkland Islands (Malvinas)', 'The Falkland Islands (Malvinas)', 'FLK', '238', 'no', '500', '.fk');

INSERT INTO `country` VALUES(73, 'FO', 'Faroe Islands', 'The Faroe Islands', 'FRO', '234', 'no', '298', '.fo');

INSERT INTO `country` VALUES(74, 'FJ', 'Fiji', 'Republic of Fiji', 'FJI', '242', 'yes', '679', '.fj');

INSERT INTO `country` VALUES(75, 'FI', 'Finland', 'Republic of Finland', 'FIN', '246', 'yes', '358', '.fi');

INSERT INTO `country` VALUES(76, 'FR', 'France', 'French Republic', 'FRA', '250', 'yes', '33', '.fr');

INSERT INTO `country` VALUES(77, 'GF', 'French Guiana', 'French Guiana', 'GUF', '254', 'no', '594', '.gf');

INSERT INTO `country` VALUES(78, 'PF', 'French Polynesia', 'French Polynesia', 'PYF', '258', 'no', '689', '.pf');

INSERT INTO `country` VALUES(79, 'TF', 'French Southern Territories', 'French Southern Territories', 'ATF', '260', 'no', NULL, '.tf');

INSERT INTO `country` VALUES(80, 'GA', 'Gabon', 'Gabonese Republic', 'GAB', '266', 'yes', '241', '.ga');

INSERT INTO `country` VALUES(81, 'GM', 'Gambia', 'Republic of The Gambia', 'GMB', '270', 'yes', '220', '.gm');

INSERT INTO `country` VALUES(82, 'GE', 'Georgia', 'Georgia', 'GEO', '268', 'yes', '995', '.ge');

INSERT INTO `country` VALUES(83, 'DE', 'Germany', 'Federal Republic of Germany', 'DEU', '276', 'yes', '49', '.de');

INSERT INTO `country` VALUES(84, 'GH', 'Ghana', 'Republic of Ghana', 'GHA', '288', 'yes', '233', '.gh');

INSERT INTO `country` VALUES(85, 'GI', 'Gibraltar', 'Gibraltar', 'GIB', '292', 'no', '350', '.gi');

INSERT INTO `country` VALUES(86, 'GR', 'Greece', 'Hellenic Republic', 'GRC', '300', 'yes', '30', '.gr');

INSERT INTO `country` VALUES(87, 'GL', 'Greenland', 'Greenland', 'GRL', '304', 'no', '299', '.gl');

INSERT INTO `country` VALUES(88, 'GD', 'Grenada', 'Grenada', 'GRD', '308', 'yes', '1+473', '.gd');

INSERT INTO `country` VALUES(89, 'GP', 'Guadaloupe', 'Guadeloupe', 'GLP', '312', 'no', '590', '.gp');

INSERT INTO `country` VALUES(90, 'GU', 'Guam', 'Guam', 'GUM', '316', 'no', '1+671', '.gu');

INSERT INTO `country` VALUES(91, 'GT', 'Guatemala', 'Republic of Guatemala', 'GTM', '320', 'yes', '502', '.gt');

INSERT INTO `country` VALUES(92, 'GG', 'Guernsey', 'Guernsey', 'GGY', '831', 'no', '44', '.gg');

INSERT INTO `country` VALUES(93, 'GN', 'Guinea', 'Republic of Guinea', 'GIN', '324', 'yes', '224', '.gn');

INSERT INTO `country` VALUES(94, 'GW', 'Guinea-Bissau', 'Republic of Guinea-Bissau', 'GNB', '624', 'yes', '245', '.gw');

INSERT INTO `country` VALUES(95, 'GY', 'Guyana', 'Co-operative Republic of Guyana', 'GUY', '328', 'yes', '592', '.gy');

INSERT INTO `country` VALUES(96, 'HT', 'Haiti', 'Republic of Haiti', 'HTI', '332', 'yes', '509', '.ht');

INSERT INTO `country` VALUES(97, 'HM', 'Heard Island and McDonald Islands', 'Heard Island and McDonald Islands', 'HMD', '334', 'no', 'NONE', '.hm');

INSERT INTO `country` VALUES(98, 'HN', 'Honduras', 'Republic of Honduras', 'HND', '340', 'yes', '504', '.hn');

INSERT INTO `country` VALUES(99, 'HK', 'Hong Kong', 'Hong Kong', 'HKG', '344', 'no', '852', '.hk');

INSERT INTO `country` VALUES(100, 'HU', 'Hungary', 'Hungary', 'HUN', '348', 'yes', '36', '.hu');

INSERT INTO `country` VALUES(101, 'IS', 'Iceland', 'Republic of Iceland', 'ISL', '352', 'yes', '354', '.is');

INSERT INTO `country` VALUES(102, 'IN', 'India', 'Republic of India', 'IND', '356', 'yes', '91', '.in');

INSERT INTO `country` VALUES(103, 'ID', 'Indonesia', 'Republic of Indonesia', 'IDN', '360', 'yes', '62', '.id');

INSERT INTO `country` VALUES(104, 'IR', 'Iran', 'Islamic Republic of Iran', 'IRN', '364', 'yes', '98', '.ir');

INSERT INTO `country` VALUES(105, 'IQ', 'Iraq', 'Republic of Iraq', 'IRQ', '368', 'yes', '964', '.iq');

INSERT INTO `country` VALUES(106, 'IE', 'Ireland', 'Ireland', 'IRL', '372', 'yes', '353', '.ie');

INSERT INTO `country` VALUES(107, 'IM', 'Isle of Man', 'Isle of Man', 'IMN', '833', 'no', '44', '.im');

INSERT INTO `country` VALUES(108, 'IL', 'Israel', 'State of Israel', 'ISR', '376', 'yes', '972', '.il');

INSERT INTO `country` VALUES(109, 'IT', 'Italy', 'Italian Republic', 'ITA', '380', 'yes', '39', '.jm');

INSERT INTO `country` VALUES(110, 'JM', 'Jamaica', 'Jamaica', 'JAM', '388', 'yes', '1+876', '.jm');

INSERT INTO `country` VALUES(111, 'JP', 'Japan', 'Japan', 'JPN', '392', 'yes', '81', '.jp');

INSERT INTO `country` VALUES(112, 'JE', 'Jersey', 'The Bailiwick of Jersey', 'JEY', '832', 'no', '44', '.je');

INSERT INTO `country` VALUES(113, 'JO', 'Jordan', 'Hashemite Kingdom of Jordan', 'JOR', '400', 'yes', '962', '.jo');

INSERT INTO `country` VALUES(114, 'KZ', 'Kazakhstan', 'Republic of Kazakhstan', 'KAZ', '398', 'yes', '7', '.kz');

INSERT INTO `country` VALUES(115, 'KE', 'Kenya', 'Republic of Kenya', 'KEN', '404', 'yes', '254', '.ke');

INSERT INTO `country` VALUES(116, 'KI', 'Kiribati', 'Republic of Kiribati', 'KIR', '296', 'yes', '686', '.ki');

INSERT INTO `country` VALUES(117, 'XK', 'Kosovo', 'Republic of Kosovo', '---', '---', 'some', '381', '');

INSERT INTO `country` VALUES(118, 'KW', 'Kuwait', 'State of Kuwait', 'KWT', '414', 'yes', '965', '.kw');

INSERT INTO `country` VALUES(119, 'KG', 'Kyrgyzstan', 'Kyrgyz Republic', 'KGZ', '417', 'yes', '996', '.kg');

INSERT INTO `country` VALUES(120, 'LA', 'Laos', 'Lao People''s Democratic Republic', 'LAO', '418', 'yes', '856', '.la');

INSERT INTO `country` VALUES(121, 'LV', 'Latvia', 'Republic of Latvia', 'LVA', '428', 'yes', '371', '.lv');

INSERT INTO `country` VALUES(122, 'LB', 'Lebanon', 'Republic of Lebanon', 'LBN', '422', 'yes', '961', '.lb');

INSERT INTO `country` VALUES(123, 'LS', 'Lesotho', 'Kingdom of Lesotho', 'LSO', '426', 'yes', '266', '.ls');

INSERT INTO `country` VALUES(124, 'LR', 'Liberia', 'Republic of Liberia', 'LBR', '430', 'yes', '231', '.lr');

INSERT INTO `country` VALUES(125, 'LY', 'Libya', 'Libya', 'LBY', '434', 'yes', '218', '.ly');

INSERT INTO `country` VALUES(126, 'LI', 'Liechtenstein', 'Principality of Liechtenstein', 'LIE', '438', 'yes', '423', '.li');

INSERT INTO `country` VALUES(127, 'LT', 'Lithuania', 'Republic of Lithuania', 'LTU', '440', 'yes', '370', '.lt');

INSERT INTO `country` VALUES(128, 'LU', 'Luxembourg', 'Grand Duchy of Luxembourg', 'LUX', '442', 'yes', '352', '.lu');

INSERT INTO `country` VALUES(129, 'MO', 'Macao', 'The Macao Special Administrative Region', 'MAC', '446', 'no', '853', '.mo');

INSERT INTO `country` VALUES(130, 'MK', 'Macedonia', 'The Former Yugoslav Republic of Macedonia', 'MKD', '807', 'yes', '389', '.mk');

INSERT INTO `country` VALUES(131, 'MG', 'Madagascar', 'Republic of Madagascar', 'MDG', '450', 'yes', '261', '.mg');

INSERT INTO `country` VALUES(132, 'MW', 'Malawi', 'Republic of Malawi', 'MWI', '454', 'yes', '265', '.mw');

INSERT INTO `country` VALUES(133, 'MY', 'Malaysia', 'Malaysia', 'MYS', '458', 'yes', '60', '.my');

INSERT INTO `country` VALUES(134, 'MV', 'Maldives', 'Republic of Maldives', 'MDV', '462', 'yes', '960', '.mv');

INSERT INTO `country` VALUES(135, 'ML', 'Mali', 'Republic of Mali', 'MLI', '466', 'yes', '223', '.ml');

INSERT INTO `country` VALUES(136, 'MT', 'Malta', 'Republic of Malta', 'MLT', '470', 'yes', '356', '.mt');

INSERT INTO `country` VALUES(137, 'MH', 'Marshall Islands', 'Republic of the Marshall Islands', 'MHL', '584', 'yes', '692', '.mh');

INSERT INTO `country` VALUES(138, 'MQ', 'Martinique', 'Martinique', 'MTQ', '474', 'no', '596', '.mq');

INSERT INTO `country` VALUES(139, 'MR', 'Mauritania', 'Islamic Republic of Mauritania', 'MRT', '478', 'yes', '222', '.mr');

INSERT INTO `country` VALUES(140, 'MU', 'Mauritius', 'Republic of Mauritius', 'MUS', '480', 'yes', '230', '.mu');

INSERT INTO `country` VALUES(141, 'YT', 'Mayotte', 'Mayotte', 'MYT', '175', 'no', '262', '.yt');

INSERT INTO `country` VALUES(142, 'MX', 'Mexico', 'United Mexican States', 'MEX', '484', 'yes', '52', '.mx');

INSERT INTO `country` VALUES(143, 'FM', 'Micronesia', 'Federated States of Micronesia', 'FSM', '583', 'yes', '691', '.fm');

INSERT INTO `country` VALUES(144, 'MD', 'Moldava', 'Republic of Moldova', 'MDA', '498', 'yes', '373', '.md');

INSERT INTO `country` VALUES(145, 'MC', 'Monaco', 'Principality of Monaco', 'MCO', '492', 'yes', '377', '.mc');

INSERT INTO `country` VALUES(146, 'MN', 'Mongolia', 'Mongolia', 'MNG', '496', 'yes', '976', '.mn');

INSERT INTO `country` VALUES(147, 'ME', 'Montenegro', 'Montenegro', 'MNE', '499', 'yes', '382', '.me');

INSERT INTO `country` VALUES(148, 'MS', 'Montserrat', 'Montserrat', 'MSR', '500', 'no', '1+664', '.ms');

INSERT INTO `country` VALUES(149, 'MA', 'Morocco', 'Kingdom of Morocco', 'MAR', '504', 'yes', '212', '.ma');

INSERT INTO `country` VALUES(150, 'MZ', 'Mozambique', 'Republic of Mozambique', 'MOZ', '508', 'yes', '258', '.mz');

INSERT INTO `country` VALUES(151, 'MM', 'Myanmar (Burma)', 'Republic of the Union of Myanmar', 'MMR', '104', 'yes', '95', '.mm');

INSERT INTO `country` VALUES(152, 'NA', 'Namibia', 'Republic of Namibia', 'NAM', '516', 'yes', '264', '.na');

INSERT INTO `country` VALUES(153, 'NR', 'Nauru', 'Republic of Nauru', 'NRU', '520', 'yes', '674', '.nr');

INSERT INTO `country` VALUES(154, 'NP', 'Nepal', 'Federal Democratic Republic of Nepal', 'NPL', '524', 'yes', '977', '.np');

INSERT INTO `country` VALUES(155, 'NL', 'Netherlands', 'Kingdom of the Netherlands', 'NLD', '528', 'yes', '31', '.nl');

INSERT INTO `country` VALUES(156, 'NC', 'New Caledonia', 'New Caledonia', 'NCL', '540', 'no', '687', '.nc');

INSERT INTO `country` VALUES(157, 'NZ', 'New Zealand', 'New Zealand', 'NZL', '554', 'yes', '64', '.nz');

INSERT INTO `country` VALUES(158, 'NI', 'Nicaragua', 'Republic of Nicaragua', 'NIC', '558', 'yes', '505', '.ni');

INSERT INTO `country` VALUES(159, 'NE', 'Niger', 'Republic of Niger', 'NER', '562', 'yes', '227', '.ne');

INSERT INTO `country` VALUES(160, 'NG', 'Nigeria', 'Federal Republic of Nigeria', 'NGA', '566', 'yes', '234', '.ng');

INSERT INTO `country` VALUES(161, 'NU', 'Niue', 'Niue', 'NIU', '570', 'some', '683', '.nu');

INSERT INTO `country` VALUES(162, 'NF', 'Norfolk Island', 'Norfolk Island', 'NFK', '574', 'no', '672', '.nf');

INSERT INTO `country` VALUES(163, 'KP', 'North Korea', 'Democratic People''s Republic of Korea', 'PRK', '408', 'yes', '850', '.kp');

INSERT INTO `country` VALUES(164, 'MP', 'Northern Mariana Islands', 'Northern Mariana Islands', 'MNP', '580', 'no', '1+670', '.mp');

INSERT INTO `country` VALUES(165, 'NO', 'Norway', 'Kingdom of Norway', 'NOR', '578', 'yes', '47', '.no');

INSERT INTO `country` VALUES(166, 'OM', 'Oman', 'Sultanate of Oman', 'OMN', '512', 'yes', '968', '.om');

INSERT INTO `country` VALUES(167, 'PK', 'Pakistan', 'Islamic Republic of Pakistan', 'PAK', '586', 'yes', '92', '.pk');

INSERT INTO `country` VALUES(168, 'PW', 'Palau', 'Republic of Palau', 'PLW', '585', 'yes', '680', '.pw');

INSERT INTO `country` VALUES(169, 'PS', 'Palestine', 'State of Palestine (or Occupied Palestinian Territory)', 'PSE', '275', 'some', '970', '.ps');

INSERT INTO `country` VALUES(170, 'PA', 'Panama', 'Republic of Panama', 'PAN', '591', 'yes', '507', '.pa');

INSERT INTO `country` VALUES(171, 'PG', 'Papua New Guinea', 'Independent State of Papua New Guinea', 'PNG', '598', 'yes', '675', '.pg');

INSERT INTO `country` VALUES(172, 'PY', 'Paraguay', 'Republic of Paraguay', 'PRY', '600', 'yes', '595', '.py');

INSERT INTO `country` VALUES(173, 'PE', 'Peru', 'Republic of Peru', 'PER', '604', 'yes', '51', '.pe');

INSERT INTO `country` VALUES(174, 'PH', 'Phillipines', 'Republic of the Philippines', 'PHL', '608', 'yes', '63', '.ph');

INSERT INTO `country` VALUES(175, 'PN', 'Pitcairn', 'Pitcairn', 'PCN', '612', 'no', 'NONE', '.pn');

INSERT INTO `country` VALUES(176, 'PL', 'Poland', 'Republic of Poland', 'POL', '616', 'yes', '48', '.pl');

INSERT INTO `country` VALUES(177, 'PT', 'Portugal', 'Portuguese Republic', 'PRT', '620', 'yes', '351', '.pt');

INSERT INTO `country` VALUES(178, 'PR', 'Puerto Rico', 'Commonwealth of Puerto Rico', 'PRI', '630', 'no', '1+939', '.pr');

INSERT INTO `country` VALUES(179, 'QA', 'Qatar', 'State of Qatar', 'QAT', '634', 'yes', '974', '.qa');

INSERT INTO `country` VALUES(180, 'RE', 'Reunion', 'R&eacute;union', 'REU', '638', 'no', '262', '.re');

INSERT INTO `country` VALUES(181, 'RO', 'Romania', 'Romania', 'ROU', '642', 'yes', '40', '.ro');

INSERT INTO `country` VALUES(182, 'RU', 'Russia', 'Russian Federation', 'RUS', '643', 'yes', '7', '.ru');

INSERT INTO `country` VALUES(183, 'RW', 'Rwanda', 'Republic of Rwanda', 'RWA', '646', 'yes', '250', '.rw');

INSERT INTO `country` VALUES(184, 'BL', 'Saint Barthelemy', 'Saint Barth&eacute;lemy', 'BLM', '652', 'no', '590', '.bl');

INSERT INTO `country` VALUES(185, 'SH', 'Saint Helena', 'Saint Helena, Ascension and Tristan da Cunha', 'SHN', '654', 'no', '290', '.sh');

INSERT INTO `country` VALUES(186, 'KN', 'Saint Kitts and Nevis', 'Federation of Saint Christopher and Nevis', 'KNA', '659', 'yes', '1+869', '.kn');

INSERT INTO `country` VALUES(187, 'LC', 'Saint Lucia', 'Saint Lucia', 'LCA', '662', 'yes', '1+758', '.lc');

INSERT INTO `country` VALUES(188, 'MF', 'Saint Martin', 'Saint Martin', 'MAF', '663', 'no', '590', '.mf');

INSERT INTO `country` VALUES(189, 'PM', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'SPM', '666', 'no', '508', '.pm');

INSERT INTO `country` VALUES(190, 'VC', 'Saint Vincent and the Grenadines', 'Saint Vincent and the Grenadines', 'VCT', '670', 'yes', '1+784', '.vc');

INSERT INTO `country` VALUES(191, 'WS', 'Samoa', 'Independent State of Samoa', 'WSM', '882', 'yes', '685', '.ws');

INSERT INTO `country` VALUES(192, 'SM', 'San Marino', 'Republic of San Marino', 'SMR', '674', 'yes', '378', '.sm');

INSERT INTO `country` VALUES(193, 'ST', 'Sao Tome and Principe', 'Democratic Republic of S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'STP', '678', 'yes', '239', '.st');

INSERT INTO `country` VALUES(194, 'SA', 'Saudi Arabia', 'Kingdom of Saudi Arabia', 'SAU', '682', 'yes', '966', '.sa');

INSERT INTO `country` VALUES(195, 'SN', 'Senegal', 'Republic of Senegal', 'SEN', '686', 'yes', '221', '.sn');

INSERT INTO `country` VALUES(196, 'RS', 'Serbia', 'Republic of Serbia', 'SRB', '688', 'yes', '381', '.rs');

INSERT INTO `country` VALUES(197, 'SC', 'Seychelles', 'Republic of Seychelles', 'SYC', '690', 'yes', '248', '.sc');

INSERT INTO `country` VALUES(198, 'SL', 'Sierra Leone', 'Republic of Sierra Leone', 'SLE', '694', 'yes', '232', '.sl');

INSERT INTO `country` VALUES(199, 'SG', 'Singapore', 'Republic of Singapore', 'SGP', '702', 'yes', '65', '.sg');

INSERT INTO `country` VALUES(200, 'SX', 'Sint Maarten', 'Sint Maarten', 'SXM', '534', 'no', '1+721', '.sx');

INSERT INTO `country` VALUES(201, 'SK', 'Slovakia', 'Slovak Republic', 'SVK', '703', 'yes', '421', '.sk');

INSERT INTO `country` VALUES(202, 'SI', 'Slovenia', 'Republic of Slovenia', 'SVN', '705', 'yes', '386', '.si');

INSERT INTO `country` VALUES(203, 'SB', 'Solomon Islands', 'Solomon Islands', 'SLB', '090', 'yes', '677', '.sb');

INSERT INTO `country` VALUES(204, 'SO', 'Somalia', 'Somali Republic', 'SOM', '706', 'yes', '252', '.so');

INSERT INTO `country` VALUES(205, 'ZA', 'South Africa', 'Republic of South Africa', 'ZAF', '710', 'yes', '27', '.za');

INSERT INTO `country` VALUES(206, 'GS', 'South Georgia and the South Sandwich Islands', 'South Georgia and the South Sandwich Islands', 'SGS', '239', 'no', '500', '.gs');

INSERT INTO `country` VALUES(207, 'KR', 'South Korea', 'Republic of Korea', 'KOR', '410', 'yes', '82', '.kr');

INSERT INTO `country` VALUES(208, 'SS', 'South Sudan', 'Republic of South Sudan', 'SSD', '728', 'yes', '211', '.ss');

INSERT INTO `country` VALUES(209, 'ES', 'Spain', 'Kingdom of Spain', 'ESP', '724', 'yes', '34', '.es');

INSERT INTO `country` VALUES(210, 'LK', 'Sri Lanka', 'Democratic Socialist Republic of Sri Lanka', 'LKA', '144', 'yes', '94', '.lk');

INSERT INTO `country` VALUES(211, 'SD', 'Sudan', 'Republic of the Sudan', 'SDN', '729', 'yes', '249', '.sd');

INSERT INTO `country` VALUES(212, 'SR', 'Suriname', 'Republic of Suriname', 'SUR', '740', 'yes', '597', '.sr');

INSERT INTO `country` VALUES(213, 'SJ', 'Svalbard and Jan Mayen', 'Svalbard and Jan Mayen', 'SJM', '744', 'no', '47', '.sj');

INSERT INTO `country` VALUES(214, 'SZ', 'Swaziland', 'Kingdom of Swaziland', 'SWZ', '748', 'yes', '268', '.sz');

INSERT INTO `country` VALUES(215, 'SE', 'Sweden', 'Kingdom of Sweden', 'SWE', '752', 'yes', '46', '.se');

INSERT INTO `country` VALUES(216, 'CH', 'Switzerland', 'Swiss Confederation', 'CHE', '756', 'yes', '41', '.ch');

INSERT INTO `country` VALUES(217, 'SY', 'Syria', 'Syrian Arab Republic', 'SYR', '760', 'yes', '963', '.sy');

INSERT INTO `country` VALUES(218, 'TW', 'Taiwan', 'Republic of China (Taiwan)', 'TWN', '158', 'former', '886', '.tw');

INSERT INTO `country` VALUES(219, 'TJ', 'Tajikistan', 'Republic of Tajikistan', 'TJK', '762', 'yes', '992', '.tj');

INSERT INTO `country` VALUES(220, 'TZ', 'Tanzania', 'United Republic of Tanzania', 'TZA', '834', 'yes', '255', '.tz');

INSERT INTO `country` VALUES(221, 'TH', 'Thailand', 'Kingdom of Thailand', 'THA', '764', 'yes', '66', '.th');

INSERT INTO `country` VALUES(222, 'TL', 'Timor-Leste (East Timor)', 'Democratic Republic of Timor-Leste', 'TLS', '626', 'yes', '670', '.tl');

INSERT INTO `country` VALUES(223, 'TG', 'Togo', 'Togolese Republic', 'TGO', '768', 'yes', '228', '.tg');

INSERT INTO `country` VALUES(224, 'TK', 'Tokelau', 'Tokelau', 'TKL', '772', 'no', '690', '.tk');

INSERT INTO `country` VALUES(225, 'TO', 'Tonga', 'Kingdom of Tonga', 'TON', '776', 'yes', '676', '.to');

INSERT INTO `country` VALUES(226, 'TT', 'Trinidad and Tobago', 'Republic of Trinidad and Tobago', 'TTO', '780', 'yes', '1+868', '.tt');

INSERT INTO `country` VALUES(227, 'TN', 'Tunisia', 'Republic of Tunisia', 'TUN', '788', 'yes', '216', '.tn');

INSERT INTO `country` VALUES(228, 'TR', 'Turkey', 'Republic of Turkey', 'TUR', '792', 'yes', '90', '.tr');

INSERT INTO `country` VALUES(229, 'TM', 'Turkmenistan', 'Turkmenistan', 'TKM', '795', 'yes', '993', '.tm');

INSERT INTO `country` VALUES(230, 'TC', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'TCA', '796', 'no', '1+649', '.tc');

INSERT INTO `country` VALUES(231, 'TV', 'Tuvalu', 'Tuvalu', 'TUV', '798', 'yes', '688', '.tv');

INSERT INTO `country` VALUES(232, 'UG', 'Uganda', 'Republic of Uganda', 'UGA', '800', 'yes', '256', '.ug');

INSERT INTO `country` VALUES(233, 'UA', 'Ukraine', 'Ukraine', 'UKR', '804', 'yes', '380', '.ua');

INSERT INTO `country` VALUES(234, 'AE', 'United Arab Emirates', 'United Arab Emirates', 'ARE', '784', 'yes', '971', '.ae');

INSERT INTO `country` VALUES(235, 'GB', 'United Kingdom', 'United Kingdom of Great Britain and Nothern Ireland', 'GBR', '826', 'yes', '44', '.uk');

INSERT INTO `country` VALUES(236, 'US', 'United States', 'United States of America', 'USA', '840', 'yes', '1', '.us');

INSERT INTO `country` VALUES(237, 'UM', 'United States Minor Outlying Islands', 'United States Minor Outlying Islands', 'UMI', '581', 'no', 'NONE', 'NONE');

INSERT INTO `country` VALUES(238, 'UY', 'Uruguay', 'Eastern Republic of Uruguay', 'URY', '858', 'yes', '598', '.uy');

INSERT INTO `country` VALUES(239, 'UZ', 'Uzbekistan', 'Republic of Uzbekistan', 'UZB', '860', 'yes', '998', '.uz');

INSERT INTO `country` VALUES(240, 'VU', 'Vanuatu', 'Republic of Vanuatu', 'VUT', '548', 'yes', '678', '.vu');

INSERT INTO `country` VALUES(241, 'VA', 'Vatican City', 'State of the Vatican City', 'VAT', '336', 'no', '39', '.va');

INSERT INTO `country` VALUES(242, 'VE', 'Venezuela', 'Bolivarian Republic of Venezuela', 'VEN', '862', 'yes', '58', '.ve');

INSERT INTO `country` VALUES(243, 'VN', 'Vietnam', 'Socialist Republic of Vietnam', 'VNM', '704', 'yes', '84', '.vn');

INSERT INTO `country` VALUES(244, 'VG', 'Virgin Islands, British', 'British Virgin Islands', 'VGB', '092', 'no', '1+284', '.vg');

INSERT INTO `country` VALUES(245, 'VI', 'Virgin Islands, US', 'Virgin Islands of the United States', 'VIR', '850', 'no', '1+340', '.vi');

INSERT INTO `country` VALUES(246, 'WF', 'Wallis and Futuna', 'Wallis and Futuna', 'WLF', '876', 'no', '681', '.wf');

INSERT INTO `country` VALUES(247, 'EH', 'Western Sahara', 'Western Sahara', 'ESH', '732', 'no', '212', '.eh');

INSERT INTO `country` VALUES(248, 'YE', 'Yemen', 'Republic of Yemen', 'YEM', '887', 'yes', '967', '.ye');

INSERT INTO `country` VALUES(249, 'ZM', 'Zambia', 'Republic of Zambia', 'ZMB', '894', 'yes', '260', '.zm');

INSERT INTO `country` VALUES(250, 'ZW', 'Zimbabwe', 'Republic of Zimbabwe', 'ZWE', '716', 'yes', '263', '.zw');

CREATE TABLE IF NOT EXISTS `course` (
  `courseID` bigint(20) NOT NULL AUTO_INCREMENT,
  `courseNumber` int(6) NOT NULL,
  `courseCode` varchar(25) NOT NULL,
  `subjectCode` varchar(11) NOT NULL,
  `deptCode` varchar(11) NOT NULL,
  `courseDesc` text NOT NULL,
  `creditType` varchar(6) NOT NULL DEFAULT 'I',
  `minCredit` double(4,1) NOT NULL DEFAULT '0.0',
  `maxCredit` double(4,1) NOT NULL DEFAULT '0.0',
  `increCredit` double(4,1) NOT NULL DEFAULT '0.0',
  `courseLevelCode` varchar(5) NOT NULL,
  `acadLevelCode` varchar(4) NOT NULL,
  `courseShortTitle` varchar(25) NOT NULL,
  `courseLongTitle` varchar(60) NOT NULL,
  `preReq` text NOT NULL,
  `allowAudit` enum('1','0') NOT NULL DEFAULT '0',
  `allowWaitlist` enum('1','0') NOT NULL DEFAULT '0',
  `minEnroll` int(3) NOT NULL,
  `seatCap` int(3) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `currStatus` varchar(1) NOT NULL,
  `statusDate` date NOT NULL DEFAULT '0000-00-00',
  `approvedDate` date NOT NULL DEFAULT '0000-00-00',
  `approvedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`courseID`),
  KEY `course_code` (`courseCode`),
  KEY `course_level_code` (`courseLevelCode`),
  KEY `acad_level_code` (`acadLevelCode`),
  KEY `approvedBy` (`approvedBy`),
  KEY `deptCode` (`deptCode`),
  KEY `subjectCode` (`subjectCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `course_sec` (
  `courseSecID` bigint(20) NOT NULL AUTO_INCREMENT,
  `sectionNumber` varchar(5) NOT NULL,
  `courseSecCode` varchar(50) NOT NULL,
  `courseSection` varchar(60) NOT NULL,
  `buildingCode` varchar(11) NOT NULL DEFAULT 'NULL',
  `roomCode` varchar(11) NOT NULL DEFAULT 'NULL',
  `locationCode` varchar(11) NOT NULL,
  `courseLevelCode` varchar(5) NOT NULL,
  `acadLevelCode` varchar(4) NOT NULL,
  `deptCode` varchar(11) NOT NULL,
  `facID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `courseID` bigint(20) NOT NULL,
  `courseCode` varchar(25) NOT NULL,
  `preReqs` text NOT NULL,
  `secShortTitle` varchar(60) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `startTime` varchar(8) NOT NULL,
  `endTime` varchar(8) NOT NULL,
  `dotw` varchar(7) NOT NULL,
  `minCredit` double(4,1) NOT NULL DEFAULT '0.0',
  `maxCredit` double(4,1) NOT NULL DEFAULT '0.0',
  `increCredit` double(4,1) NOT NULL DEFAULT '0.0',
  `ceu` double(4,1) NOT NULL DEFAULT '0.0',
  `instructorMethod` varchar(180) NOT NULL,
  `instructorLoad` double(4,1) NOT NULL DEFAULT '0.0',
  `contactHours` double(4,1) NOT NULL DEFAULT '0.0',
  `webReg` enum('1','0') NOT NULL DEFAULT '1',
  `courseFee` double(10,2) NOT NULL DEFAULT '0.00',
  `labFee` double(10,2) NOT NULL DEFAULT '0.00',
  `materialFee` double(10,2) NOT NULL DEFAULT '0.00',
  `secType` enum('ONL','HB','ONC') NOT NULL DEFAULT 'ONC',
  `currStatus` varchar(1) NOT NULL,
  `statusDate` date NOT NULL,
  `comment` text NOT NULL,
  `approvedDate` date NOT NULL,
  `approvedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`courseSecID`),
  UNIQUE KEY `courseSection` (`courseSection`),
  KEY `course_sec_code` (`courseSecCode`),
  KEY `current_status` (`currStatus`),
  KEY `approvedBy` (`approvedBy`),
  KEY `facID` (`facID`),
  KEY `buildingCode` (`buildingCode`),
  KEY `roomCode` (`roomCode`),
  KEY `locationCode` (`locationCode`),
  KEY `deptCode` (`deptCode`),
  KEY `termCode` (`termCode`),
  KEY `courseCode` (`courseCode`),
  KEY `courseID` (`courseID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `degree` (
  `degreeID` int(11) NOT NULL AUTO_INCREMENT,
  `degreeCode` varchar(11) NOT NULL,
  `degreeName` varchar(180) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`degreeID`),
  UNIQUE KEY `degreeKey` (`degreeCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `degree` VALUES(00000000001, 'NULL', '', '{now}');

CREATE TABLE IF NOT EXISTS `department` (
  `deptID` int(11) NOT NULL AUTO_INCREMENT,
  `deptTypeCode` varchar(6) NOT NULL,
  `deptCode` varchar(11) NOT NULL,
  `deptName` varchar(180) NOT NULL,
  `deptEmail` varchar(180) NOT NULL,
  `deptPhone` varchar(20) NOT NULL,
  `deptDesc` varchar(255) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`deptID`),
  UNIQUE KEY `deptCode` (`deptCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `department` VALUES(1, 'NULL', 'NULL', 'Null', '', '', 'Default', '{now}');

CREATE TABLE IF NOT EXISTS `email_hold` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `personID` bigint(20) NOT NULL,
  `queryID` int(11) NOT NULL,
  `fromName` varchar(118) NOT NULL,
  `fromEmail` varchar(118) NOT NULL,
  `subject` varchar(118) NOT NULL,
  `body` longtext NOT NULL,
  `processed` enum('1','0') NOT NULL DEFAULT '0',
  `dateTime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `email_queue` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `holdID` bigint(20) NOT NULL,
  `personID` bigint(20) NOT NULL,
  `fromName` varchar(118) NOT NULL,
  `fromEmail` varchar(118) NOT NULL,
  `uname` varchar(118) NOT NULL,
  `email` varchar(118) NOT NULL,
  `fname` varchar(118) NOT NULL,
  `lname` varchar(118) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `body` longtext NOT NULL,
  `sent` enum('1','0') NOT NULL DEFAULT '0',
  `sentDate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `email_template` (
  `etID` int(11) NOT NULL AUTO_INCREMENT,
  `deptCode` varchar(11) NOT NULL,
  `email_key` varchar(30) NOT NULL,
  `email_name` varchar(30) NOT NULL,
  `email_value` longtext NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`etID`),
  KEY `deptCode` (`deptCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `error` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` int(4) NOT NULL,
  `time` int(10) NOT NULL,
  `string` varchar(512) NOT NULL,
  `file` varchar(255) NOT NULL,
  `line` int(6) NOT NULL,
  `addDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `event` (
  `eventID` bigint(20) NOT NULL AUTO_INCREMENT,
  `eventType` varchar(255) NOT NULL,
  `catID` int(11) NOT NULL,
  `requestor` bigint(20) NOT NULL,
  `roomCode` varchar(11) DEFAULT NULL,
  `termCode` varchar(11) DEFAULT NULL,
  `title` varchar(120) NOT NULL,
  `description` text,
  `weekday` int(1) DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `startTime` time DEFAULT NULL,
  `endTime` time DEFAULT NULL,
  `repeats` tinyint(1) DEFAULT NULL,
  `repeatFreq` tinyint(1) DEFAULT NULL,
  `status` enum('A','I') NOT NULL DEFAULT 'A',
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`eventID`),
  UNIQUE KEY `event` (`roomCode`,`termCode`,`title`,`weekday`,`startDate`,`startTime`,`endTime`),
  KEY `termCode` (`termCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `event_category` (
  `catID` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(30) NOT NULL,
  `bgcolor` varchar(11) NOT NULL DEFAULT '#000000',
  PRIMARY KEY (`catID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `event_category` VALUES(1, 'Course', '#8C7BC6');

INSERT INTO `event_category` VALUES(2, 'Meeting', '#00CCFF');

INSERT INTO `event_category` VALUES(3, 'Conference', '#E66000');

INSERT INTO `event_category` VALUES(4, 'Event', '#61D0AF');

CREATE TABLE IF NOT EXISTS `event_meta` (
  `eventMetaID` bigint(20) NOT NULL AUTO_INCREMENT,
  `eventID` bigint(20) NOT NULL,
  `roomCode` varchar(11) DEFAULT NULL,
  `requestor` bigint(20) NOT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `title` varchar(120) NOT NULL,
  `description` text,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`eventMetaID`),
  UNIQUE KEY `event_meta` (`eventID`,`roomCode`,`start`,`end`,`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `event_request` (
  `requestID` bigint(20) NOT NULL AUTO_INCREMENT,
  `eventType` varchar(255) NOT NULL,
  `catID` int(11) NOT NULL,
  `requestor` bigint(20) NOT NULL,
  `roomCode` varchar(11) DEFAULT NULL,
  `termCode` varchar(11) DEFAULT NULL,
  `title` varchar(120) NOT NULL,
  `description` text,
  `weekday` int(1) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `repeats` tinyint(1) DEFAULT NULL,
  `repeatFreq` tinyint(1) DEFAULT NULL,
  `addDate` date NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`requestID`),
  UNIQUE KEY `event_request` (`roomCode`,`termCode`,`title`,`weekday`,`startDate`,`startTime`,`endTime`),
  KEY `termCode` (`termCode`),
  KEY `requestor` (`requestor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `gl_account` (
  `glacctID` int(11) NOT NULL AUTO_INCREMENT,
  `gl_acct_number` varchar(200) NOT NULL,
  `gl_acct_name` varchar(200) NOT NULL,
  `gl_acct_type` varchar(200) NOT NULL,
  `gl_acct_memo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`glacctID`),
  UNIQUE KEY `gl_acct_number` (`gl_acct_number`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gl_journal_entry` (
  `jeID` int(11) NOT NULL AUTO_INCREMENT,
  `gl_jentry_date` date NOT NULL,
  `gl_jentry_manual_id` varchar(100) DEFAULT NULL,
  `gl_jentry_title` varchar(100) DEFAULT NULL,
  `gl_jentry_description` varchar(200) DEFAULT NULL,
  `gl_jentry_personID` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`jeID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gl_transaction` (
  `trID` int(11) NOT NULL AUTO_INCREMENT,
  `jeID` int(11) DEFAULT NULL,
  `accountID` int(11) DEFAULT NULL,
  `gl_trans_date` date DEFAULT NULL,
  `gl_trans_memo` varchar(400) DEFAULT NULL,
  `gl_trans_debit` decimal(10,2) DEFAULT NULL,
  `gl_trans_credit` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`trID`),
  KEY `jeID` (`jeID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gradebook` (
  `gbID` bigint(20) NOT NULL AUTO_INCREMENT,
  `assignID` bigint(20) NOT NULL,
  `courseSecID` bigint(20) DEFAULT NULL,
  `facID` bigint(20) NOT NULL,
  `stuID` bigint(20) NOT NULL,
  `grade` varchar(2) NOT NULL,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`gbID`),
  UNIQUE KEY `gradebook_unique_grade` (`assignID`,`courseSecID`,`facID`,`stuID`),
  KEY `courseSecID` (`courseSecID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `grade_scale` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `grade` varchar(2) NOT NULL,
  `percent` varchar(10) NOT NULL,
  `points` decimal(6,2) NOT NULL,
  `count_in_gpa` enum('1','0') NOT NULL DEFAULT '0',
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `grade_scale` VALUES(00000000001, 'A+', '97-100', '4.00', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000002, 'A', '93-96', '4.00', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000003, 'A-', '90-92', '3.70', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000004, 'B+', '87-89', '3.30', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000005, 'B', '83-86', '3.00', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000006, 'B-', '80-82', '2.70', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000007, 'P', '80-82', '2.70', '1', '1', 'Minimum for Pass/Fail courses');

INSERT INTO `grade_scale` VALUES(00000000008, 'C+', '77-79', '2.30', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000009, 'C', '73-76', '2.00', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000010, 'C-', '70-72', '1.70', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000011, 'D+', '67-69', '1.30', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000012, 'D', '65-66', '1.00', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000013, 'F', 'Below 65', '0.00', '1', '1', '');

INSERT INTO `grade_scale` VALUES(00000000014, 'I', '0', '0.00', '0', '1', 'Incomplete grades');

INSERT INTO `grade_scale` VALUES(00000000015, 'AW', '0', '0.00', '0', '1', '"AW" is an administrative grade assigned to students who have attended no more than the first two classes, but who have not officially dropped or withdrawn from the course. Does not count against GPA.');

INSERT INTO `grade_scale` VALUES(00000000016, 'NA', '0', '0.00', '0', '1', '"NA" is an administrative grade assigned to students who are officially registered for the course and whose name appears on the grade roster, but who have never attended class. Does not count against GPA.');

INSERT INTO `grade_scale` VALUES(00000000017, 'W', '0', '0.00', '0', '1', 'Withdrew');

INSERT INTO `grade_scale` VALUES(00000000018, 'IP', '90-98', '4.00', '0', '1', 'Incomplete passing');

CREATE TABLE IF NOT EXISTS `graduation_hold` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `queryID` int(11) NOT NULL,
  `gradDate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `institution` (
  `institutionID` int(11) NOT NULL AUTO_INCREMENT,
  `fice_ceeb` varchar(11) DEFAULT NULL,
  `instType` varchar(4) NOT NULL,
  `instName` varchar(180) NOT NULL,
  `city` varchar(30) NOT NULL,
  `state` varchar(2) NOT NULL,
  `country` varchar(2) NOT NULL,
  PRIMARY KEY (`institutionID`),
  KEY `fice_ceeb` (`fice_ceeb`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `institution_attended` (
  `instAttID` bigint(20) NOT NULL AUTO_INCREMENT,
  `fice_ceeb` varchar(11) NOT NULL,
  `personID` bigint(20) NOT NULL,
  `fromDate` date NOT NULL,
  `toDate` date NOT NULL,
  `major` varchar(255) NOT NULL,
  `degree_awarded` varchar(6) NOT NULL,
  `degree_conferred_date` date NOT NULL,
  `GPA` double(4,1) DEFAULT NULL,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`instAttID`),
  UNIQUE KEY `inst_att` (`fice_ceeb`,`personID`),
  KEY `personID` (`personID`),
  KEY `addedBy` (`addedBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `job` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `pay_grade` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `hourly_wage` decimal(4,2) DEFAULT NULL,
  `weekly_hours` int(4) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `job` VALUES(1, 1, 'IT Support', '34.00', 40, NULL, '{addDate}', 00000001, '{now}');

CREATE TABLE IF NOT EXISTS `job_status` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `typeCode` varchar(6) NOT NULL,
  `type` varchar(180) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `typeCode` (`typeCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `job_status` VALUES(1, 'FT', 'Full Time');

INSERT INTO `job_status` VALUES(2, 'TQ', 'Three Quarter Time');

INSERT INTO `job_status` VALUES(3, 'HT', 'Half Time');

INSERT INTO `job_status` VALUES(4, 'CT', 'Contract');

INSERT INTO `job_status` VALUES(5, 'PD', 'Per Diem');

INSERT INTO `job_status` VALUES(6, 'TFT', 'Temp Full Time');

INSERT INTO `job_status` VALUES(7, 'TTQ', 'Temp Three Quarter Time');

INSERT INTO `job_status` VALUES(8, 'THT', 'Temp Half Time');

CREATE TABLE IF NOT EXISTS `location` (
  `locationID` int(11) NOT NULL AUTO_INCREMENT,
  `locationCode` varchar(11) NOT NULL,
  `locationName` varchar(80) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`locationID`),
  UNIQUE KEY `locationCode` (`locationCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `location` VALUES(00000000001, 'NULL', '', '{now}');

CREATE TABLE IF NOT EXISTS `major` (
  `majorID` int(11) NOT NULL AUTO_INCREMENT,
  `majorCode` varchar(11) NOT NULL,
  `majorName` varchar(180) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`majorID`),
  UNIQUE KEY `majorCode` (`majorCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `major` VALUES(00000000001, 'NULL', '', '{now}');

CREATE TABLE IF NOT EXISTS `met_link` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `link_title` varchar(180) NOT NULL,
  `link_src` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `sort` tinyint(2) NOT NULL,
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
  PRIMARY KEY (`ID`),
  KEY `addedBy` (`addedBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='News and announcements for eduTrac''s frontend.';

CREATE TABLE IF NOT EXISTS `met_page` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `page_title` varchar(255) NOT NULL,
  `page_slug` varchar(255) NOT NULL,
  `page_content` text NOT NULL,
  `status` enum('draft','publish') NOT NULL,
  `sort` tinyint(2) NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `addDate` date NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `addedBy` (`addedBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Pages created by an admin for eduTrac''s frontend.';

CREATE TABLE IF NOT EXISTS `minor` (
  `minorID` int(11) NOT NULL AUTO_INCREMENT,
  `minorCode` varchar(11) NOT NULL,
  `minorName` varchar(180) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`minorID`),
  UNIQUE KEY `minorCode` (`minorCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `minor` VALUES(00000000001, 'NULL', '', '{now}');

CREATE TABLE IF NOT EXISTS `nslc_hold_file` (
  `nslcHoldFileID` bigint(20) NOT NULL AUTO_INCREMENT,
  `stuID` bigint(20) NOT NULL,
  `lname` varchar(150) NOT NULL,
  `fname` varchar(150) NOT NULL,
  `address1` varchar(150) NOT NULL,
  `city` varchar(150) NOT NULL,
  `state` varchar(150) NOT NULL,
  `zip` varchar(150) NOT NULL,
  `country` varchar(150) NOT NULL,
  `ssn` int(20) DEFAULT NULL,
  PRIMARY KEY (`nslcHoldFileID`),
  UNIQUE KEY `userID` (`stuID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nslc_setup` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `branch` varchar(2) NOT NULL,
  `termCode` varchar(8) NOT NULL,
  `termStartDate` date NOT NULL,
  `termEndDate` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `nslc_setup` VALUES(00000000001, '00', '13/FA', '2013-09-01', '2013-12-18');

CREATE TABLE IF NOT EXISTS `options_meta` (
`meta_id` int(11) NOT NULL,
  `meta_key` varchar(60) NOT NULL DEFAULT '',
  `meta_value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `options_meta` ADD PRIMARY KEY (`meta_id`), ADD UNIQUE KEY `option_name` (`meta_key`);

ALTER TABLE `options_meta` MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `pay_grade` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `grade` varchar(10) NOT NULL,
  `minimum_salary` decimal(10,2) NOT NULL,
  `maximum_salary` decimal(10,2) NOT NULL,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `pay_grade` VALUES(1, '24', '40000.00', '44999.00', '{addDate}', 00000001, '{now}');

CREATE TABLE IF NOT EXISTS `payment` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `stuID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `amount` double(10,2) NOT NULL DEFAULT '0.00',
  `checkNum` varchar(8) DEFAULT NULL,
  `paypal_txnID` varchar(255) DEFAULT NULL,
  `paypal_payment_status` varchar(80) DEFAULT NULL,
  `paypal_txn_fee` double(10,2) NOT NULL DEFAULT '0.00',
  `paymentTypeID` int(11) NOT NULL,
  `comment` text NOT NULL,
  `paymentDate` date NOT NULL,
  `postedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `stuID` (`stuID`),
  KEY `termCode` (`termCode`),
  KEY `postedBy` (`postedBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `payment_type` (
  `ptID` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  PRIMARY KEY (`ptID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `payment_type` VALUES(1, 'Cash');

INSERT INTO `payment_type` VALUES(2, 'Check');

INSERT INTO `payment_type` VALUES(3, 'Credit Card');

INSERT INTO `payment_type` VALUES(4, 'Paypal');

INSERT INTO `payment_type` VALUES(5, 'Wire Transfer');

INSERT INTO `payment_type` VALUES(6, 'Money Order');

INSERT INTO `payment_type` VALUES(7, 'Student Loan');

INSERT INTO `payment_type` VALUES(8, 'Grant');

INSERT INTO `payment_type` VALUES(9, 'Financial Aid');

INSERT INTO `payment_type` VALUES(10, 'Scholarship');

INSERT INTO `payment_type` VALUES(11, 'Waiver');

INSERT INTO `payment_type` VALUES(12, 'Other');

CREATE TABLE IF NOT EXISTS `permission` (
  `ID` bigint(20) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `permKey` varchar(30) NOT NULL,
  `permName` varchar(80) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `permKey` (`permKey`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `permission` VALUES(00000000000000000017, 'edit_settings', 'Edit Settings');

INSERT INTO `permission` VALUES(00000000000000000018, 'access_audit_trail_screen', 'Audit Trail Logs');

INSERT INTO `permission` VALUES(00000000000000000019, 'access_sql_interface_screen', 'SQL Interface Screen');

INSERT INTO `permission` VALUES(00000000000000000036, 'access_course_screen', 'Course Screen');

INSERT INTO `permission` VALUES(00000000000000000040, 'access_faculty_screen', 'Faculty Screen');

INSERT INTO `permission` VALUES(00000000000000000044, 'access_parent_screen', 'Parent Screen');

INSERT INTO `permission` VALUES(00000000000000000048, 'access_student_screen', 'Student Screen');

INSERT INTO `permission` VALUES(00000000000000000052, 'access_plugin_screen', 'Plugin Screen');

INSERT INTO `permission` VALUES(00000000000000000057, 'access_role_screen', 'Role Screen');

INSERT INTO `permission` VALUES(00000000000000000061, 'access_permission_screen', 'Permission Screen');

INSERT INTO `permission` VALUES(00000000000000000065, 'access_user_role_screen', 'User Role Screen');

INSERT INTO `permission` VALUES(00000000000000000069, 'access_user_permission_screen', 'User Permission Screen');

INSERT INTO `permission` VALUES(00000000000000000073, 'access_email_template_screen', 'Email Template Screen');

INSERT INTO `permission` VALUES(00000000000000000074, 'access_course_sec_screen', 'Course Section Screen');

INSERT INTO `permission` VALUES(00000000000000000075, 'add_course_sec', 'Add Course Section');

INSERT INTO `permission` VALUES(00000000000000000078, 'course_sec_inquiry_only', 'Course Section Inquiry Only');

INSERT INTO `permission` VALUES(00000000000000000079, 'course_inquiry_only', 'Course Inquiry Only');

INSERT INTO `permission` VALUES(00000000000000000080, 'access_person_screen', 'Person Screen');

INSERT INTO `permission` VALUES(00000000000000000081, 'add_person', 'Add Person');

INSERT INTO `permission` VALUES(00000000000000000085, 'access_acad_prog_screen', 'Academic Program Screen');

INSERT INTO `permission` VALUES(00000000000000000086, 'add_acad_prog', 'Add Academic Program');

INSERT INTO `permission` VALUES(00000000000000000089, 'acad_prog_inquiry_only', 'Academic Program Inquiry Only');

INSERT INTO `permission` VALUES(00000000000000000090, 'access_nslc', 'NSLC');

INSERT INTO `permission` VALUES(00000000000000000091, 'access_error_log_screen', 'Error Log Screen');

INSERT INTO `permission` VALUES(00000000000000000092, 'access_student_portal', 'Student Portal');

INSERT INTO `permission` VALUES(00000000000000000093, 'access_cronjob_screen', 'Cronjob Screen');

INSERT INTO `permission` VALUES(00000000000000000097, 'access_report_screen', 'Report Screen');

INSERT INTO `permission` VALUES(00000000000000000098, 'add_address', 'Add Address');

INSERT INTO `permission` VALUES(00000000000000000100, 'address_inquiry_only', 'Address Inquiry Only');

INSERT INTO `permission` VALUES(00000000000000000101, 'general_inquiry_only', 'General Inquiry Only');

INSERT INTO `permission` VALUES(00000000000000000102, 'faculty_inquiry_only', 'Faculty Inquiry Only');

INSERT INTO `permission` VALUES(00000000000000000103, 'parent_inquiry_only', 'Parent Inquiry Only');

INSERT INTO `permission` VALUES(00000000000000000104, 'student_inquiry_only', 'Student Inquiry Only');

INSERT INTO `permission` VALUES(00000000000000000106, 'access_plugin_admin_page', 'Plugin Admin Page');

INSERT INTO `permission` VALUES(00000000000000000108, 'access_save_query_screens', 'Save Query Screens');

INSERT INTO `permission` VALUES(00000000000000000109, 'access_forms', 'Forms');

INSERT INTO `permission` VALUES(00000000000000000110, 'create_stu_record', 'Create Student Record');

INSERT INTO `permission` VALUES(00000000000000000111, 'create_fac_record', 'Create Faculty Record');

INSERT INTO `permission` VALUES(00000000000000000112, 'create_par_record', 'Create Parent Record');

INSERT INTO `permission` VALUES(00000000000000000113, 'reset_person_password', 'Reset Person Password');

INSERT INTO `permission` VALUES(00000000000000000114, 'register_students', 'Register Students');

INSERT INTO `permission` VALUES(00000000000000000167, 'access_ftp', 'FTP');

INSERT INTO `permission` VALUES(00000000000000000168, 'access_stu_roster_screen', 'Access Student Roster Screen');

INSERT INTO `permission` VALUES(00000000000000000169, 'access_grading_screen', 'Grading Screen');

INSERT INTO `permission` VALUES(00000000000000000170, 'access_bill_tbl_screen', 'Billing Table Screen');

INSERT INTO `permission` VALUES(00000000000000000171, 'add_crse_sec_bill', 'Add Course Sec Billing');

INSERT INTO `permission` VALUES(00000000000000000176, 'access_parent_portal', 'Parent Portal');

INSERT INTO `permission` VALUES(00000000000000000177, 'import_data', 'Import Data');

INSERT INTO `permission` VALUES(00000000000000000178, 'add_course', 'Add Course');

INSERT INTO `permission` VALUES(00000000000000000179, 'person_inquiry_only', 'Person Inquiry Only');

INSERT INTO `permission` VALUES(00000000000000000180, 'room_request', 'Room Request');

INSERT INTO `permission` VALUES(00000000000000000201, 'activate_course_sec', 'Activate Course Section');

INSERT INTO `permission` VALUES(00000000000000000202, 'cancel_course_sec', 'Cancel Course Section');

INSERT INTO `permission` VALUES(00000000000000000203, 'access_institutions_screen', 'Access Institutions Screen');

INSERT INTO `permission` VALUES(00000000000000000204, 'add_institution', 'Add Institution');

INSERT INTO `permission` VALUES(00000000000000000205, 'access_application_screen', 'Access Application Screen');

INSERT INTO `permission` VALUES(00000000000000000206, 'create_application', 'Create Application');

INSERT INTO `permission` VALUES(00000000000000000207, 'access_staff_screen', 'Staff Screen');

INSERT INTO `permission` VALUES(00000000000000000208, 'staff_inquiry_only', 'Staff Inquiry Only');

INSERT INTO `permission` VALUES(00000000000000000209, 'create_staff_record', 'Create Staff Record');

INSERT INTO `permission` VALUES(00000000000000000210, 'graduate_students', 'Graduate Students');

INSERT INTO `permission` VALUES(00000000000000000211, 'generate_transcripts', 'Generate Transcripts');

INSERT INTO `permission` VALUES(00000000000000000212, 'access_student_accounts', 'Access Student Accounts');

INSERT INTO `permission` VALUES(00000000000000000213, 'student_account_inquiry_only', 'Student Account Inquiry Only');

INSERT INTO `permission` VALUES(00000000000000000214, 'restrict_edit_profile', 'Restrict Edit Profile');

INSERT INTO `permission` VALUES(00000000000000000215, 'access_general_ledger', 'Access General Ledger');

INSERT INTO `permission` VALUES(00000000000000000216, 'login_as_user', 'Login as User');

INSERT INTO `permission` VALUES(00000000000000000217, 'access_academics', 'Access Academics');

INSERT INTO `permission` VALUES(00000000000000000218, 'access_financials', 'Access Financials');

INSERT INTO `permission` VALUES(00000000000000000219, 'access_human_resources', 'Access Human Resources');

INSERT INTO `permission` VALUES(00000000000000000220, 'submit_timesheets', 'Submit Timesheets');

INSERT INTO `permission` VALUES(00000000000000000221, 'access_sql', 'Access SQL');

INSERT INTO `permission` VALUES(00000000000000000222, 'access_person_mgmt', 'Access Person Management');

INSERT INTO `permission` VALUES(00000000000000000223, 'create_campus_site', 'Create Campus Site');

INSERT INTO `permission` VALUES(00000000000000000224, 'access_dashboard', 'Access Dashboard');

INSERT INTO `permission` VALUES(00000000000000000225, 'access_myet_admin', 'Access myeduTrac Admin');

INSERT INTO `permission` VALUES(00000000000000000226, 'manage_myet_pages', 'Manage myeduTrac Pages');

INSERT INTO `permission` VALUES(00000000000000000227, 'manage_myet_links', 'Manage myeduTrac Links');

INSERT INTO `permission` VALUES(00000000000000000228, 'manage_myet_news', 'Manage myeduTrac News');

INSERT INTO `permission` VALUES(00000000000000000229, 'add_myet_page', 'Add myeduTrac Page');

INSERT INTO `permission` VALUES(00000000000000000230, 'edit_myet_page', 'Edit myeduTrac Page');

INSERT INTO `permission` VALUES(00000000000000000231, 'delete_myet_page', 'Delete myeduTrac Page');

INSERT INTO `permission` VALUES(00000000000000000232, 'add_myet_link', 'Add myeduTrac Link');

INSERT INTO `permission` VALUES(00000000000000000233, 'edit_myet_link', 'Edit myeduTrac Link');

INSERT INTO `permission` VALUES(00000000000000000234, 'delete_myet_link', 'Delete myeduTrac Link');

INSERT INTO `permission` VALUES(00000000000000000235, 'add_myet_news', 'Add myeduTrac News');

INSERT INTO `permission` VALUES(00000000000000000236, 'edit_myet_news', 'Edit myeduTrac News');

INSERT INTO `permission` VALUES(00000000000000000237, 'delete_myet_news', 'Delete myeduTrac News');

INSERT INTO `permission` VALUES(00000000000000000238, 'clear_screen_cache', 'Clear Screen Cache');

INSERT INTO `permission` VALUES(00000000000000000239, 'clear_database_cache', 'Clear Database Cache');

INSERT INTO `permission` VALUES(00000000000000000240, 'access_myet_appl_form', 'Access myeduTrac Application Form');

INSERT INTO `permission` VALUES(00000000000000000241, 'edit_myet_css', 'Edit myeduTrac CSS');

INSERT INTO `permission` VALUES(00000000000000000242, 'edit_myet_welcome_message', 'Edit myeduTrac Welcome Message');

INSERT INTO `permission` VALUES(00000000000000000243, 'access_communication_mgmt', 'Access Communication Management');

INSERT INTO `permission` VALUES(00000000000000000244, 'delete_student', 'Delete Student');

INSERT INTO `permission` VALUES(00000000000000000245, 'access_payment_gateway', 'Access Payment Gateway');

INSERT INTO `permission` VALUES(00000000000000000246, 'access_ea', 'Access eduTrac Analytics');

INSERT INTO `permission` VALUES(00000000000000000247, 'execute_saved_query', 'Execute Saved Query');

CREATE TABLE IF NOT EXISTS `person` (
  `personID` bigint(20) NOT NULL AUTO_INCREMENT,
  `altID` varchar(255) DEFAULT NULL,
  `uname` varchar(80) NOT NULL,
  `prefix` varchar(6) NOT NULL,
  `personType` varchar(3) NOT NULL,
  `fname` varchar(150) NOT NULL,
  `lname` varchar(150) NOT NULL,
  `mname` varchar(2) NOT NULL,
  `email` varchar(150) NOT NULL,
  `ssn` int(9) NOT NULL,
  `dob` date NOT NULL,
  `veteran` enum('1','0') NOT NULL,
  `ethnicity` varchar(30) NOT NULL,
  `gender` enum('M','F') NOT NULL,
  `emergency_contact` varchar(150) NOT NULL,
  `emergency_contact_phone` varchar(50) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('A','I') NOT NULL DEFAULT 'A',
  `auth_token` varchar(255) DEFAULT NULL,
  `approvedDate` datetime NOT NULL,
  `approvedBy` bigint(20) NOT NULL,
  `LastLogin` datetime NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`personID`),
  UNIQUE KEY `uname` (`uname`),
  KEY `person_type` (`personType`),
  KEY `approvedBy` (`approvedBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `person_perms` (
  `ID` bigint(20) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `personID` bigint(20) NOT NULL,
  `permission` text NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `personID` (`personID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `person_roles` (
  `rID` int(11) NOT NULL AUTO_INCREMENT,
  `personID` bigint(20) NOT NULL,
  `roleID` bigint(20) NOT NULL,
  `addDate` datetime NOT NULL,
  PRIMARY KEY (`rID`),
  UNIQUE KEY `userID` (`personID`,`roleID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `refund` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `stuID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `amount` double(10,2) NOT NULL DEFAULT '0.00',
  `comment` text NOT NULL,
  `refundDate` date NOT NULL,
  `postedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `stuID` (`stuID`),
  KEY `termCode` (`termCode`),
  KEY `postedBy` (`postedBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `restriction` (
  `rstrID` bigint(20) NOT NULL AUTO_INCREMENT,
  `stuID` bigint(20) NOT NULL,
  `rstrCode` varchar(6) NOT NULL,
  `severity` int(2) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `comment` text NOT NULL,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rstrID`),
  KEY `rstrCode` (`rstrCode`),
  KEY `stuID` (`stuID`),
  KEY `addedBy` (`addedBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `restriction_code` (
  `rstrCodeID` int(11) NOT NULL AUTO_INCREMENT,
  `rstrCode` varchar(6) NOT NULL,
  `description` varchar(255) NOT NULL,
  `deptCode` varchar(11) NOT NULL,
  PRIMARY KEY (`rstrCodeID`),
  UNIQUE KEY `rstrCode` (`rstrCode`),
  KEY `deptCode` (`deptCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `role` (
  `ID` bigint(20) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `roleName` varchar(20) NOT NULL,
  `permission` text NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `roleName` (`roleName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `role` VALUES(00000000000000000008, 'Super Administrator', 'a:82:{i:0;s:13:"edit_settings";i:1;s:25:"access_audit_trail_screen";i:2;s:27:"access_sql_interface_screen";i:3;s:20:"access_course_screen";i:4;s:20:"access_parent_screen";i:5;s:21:"access_student_screen";i:6;s:20:"access_plugin_screen";i:7;s:18:"access_role_screen";i:8;s:24:"access_permission_screen";i:9;s:23:"access_user_role_screen";i:10;s:29:"access_user_permission_screen";i:11;s:28:"access_email_template_screen";i:12;s:24:"access_course_sec_screen";i:13;s:14:"add_course_sec";i:14;s:20:"access_person_screen";i:15;s:10:"add_person";i:16;s:23:"access_acad_prog_screen";i:17;s:13:"add_acad_prog";i:18;s:11:"access_nslc";i:19;s:23:"access_error_log_screen";i:20;s:21:"access_student_portal";i:21;s:21:"access_cronjob_screen";i:22;s:20:"access_report_screen";i:23;s:11:"add_address";i:24;s:24:"access_plugin_admin_page";i:25;s:25:"access_save_query_screens";i:26;s:12:"access_forms";i:27;s:17:"create_stu_record";i:28;s:17:"create_fac_record";i:29;s:17:"create_par_record";i:30;s:21:"reset_person_password";i:31;s:17:"register_students";i:32;s:10:"access_ftp";i:33;s:24:"access_stu_roster_screen";i:34;s:21:"access_grading_screen";i:35;s:22:"access_bill_tbl_screen";i:36;s:17:"add_crse_sec_bill";i:37;s:20:"access_parent_portal";i:38;s:11:"import_data";i:39;s:10:"add_course";i:40;s:12:"room_request";i:41;s:19:"activate_course_sec";i:42;s:17:"cancel_course_sec";i:43;s:26:"access_institutions_screen";i:44;s:15:"add_institution";i:45;s:25:"access_application_screen";i:46;s:18:"create_application";i:47;s:19:"access_staff_screen";i:48;s:19:"create_staff_record";i:49;s:16:"access_dashboard";i:50;s:17:"graduate_students";i:51;s:20:"generate_transcripts";i:52;s:23:"access_student_accounts";i:53;s:21:"access_general_ledger";i:54;s:13:"login_as_user";i:55;s:16:"access_academics";i:56;s:17:"access_financials";i:57;s:22:"access_human_resources";i:58;s:17:"submit_timesheets";i:59;s:10:"access_sql";i:60;s:18:"access_person_mgmt";i:61;s:22:"access_payment_gateway";i:62;s:18:"create_campus_site";i:63;s:17:"access_myet_admin";i:64;s:17:"manage_myet_pages";i:65;s:17:"manage_myet_links";i:66;s:16:"manage_myet_news";i:67;s:13:"add_myet_page";i:68;s:14:"edit_myet_page";i:69;s:16:"delete_myet_page";i:70;s:13:"add_myet_link";i:71;s:14:"edit_myet_link";i:72;s:16:"delete_myet_link";i:73;s:13:"add_myet_news";i:74;s:14:"edit_myet_news";i:75;s:16:"delete_myet_news";i:76;s:18:"clear_screen_cache";i:77;s:20:"clear_database_cache";i:78;s:21:"access_myet_appl_form";i:79;s:13:"edit_myet_css";i:80;s:25:"edit_myet_welcome_message";i:81;s:25:"access_communication_mgmt";}');

INSERT INTO `role` VALUES(00000000000000000009, 'Faculty', 'a:18:{i:0;s:21:"access_student_screen";i:1;s:24:"access_course_sec_screen";i:2;s:23:"course_sec_inquiry_only";i:3;s:19:"course_inquiry_only";i:4;s:23:"access_acad_prog_screen";i:5;s:22:"acad_prog_inquiry_only";i:6;s:20:"address_inquiry_only";i:7;s:20:"general_inquiry_only";i:8;s:20:"student_inquiry_only";i:9;s:24:"access_stu_roster_screen";i:10;s:21:"access_grading_screen";i:11;s:19:"person_inquiry_only";i:12;s:19:"access_staff_screen";i:13;s:18:"staff_inquiry_only";i:14;s:16:"access_dashboard";i:15;s:21:"restrict_edit_profile";i:16;s:16:"access_academics";i:17;s:18:"access_person_mgmt";}');

INSERT INTO `role` VALUES(00000000000000000010, 'Parent', '');

INSERT INTO `role` VALUES(00000000000000000011, 'Student', 'a:1:{i:0;s:21:"access_student_portal";}');

INSERT INTO `role` VALUES(00000000000000000012, 'Staff', 'a:32:{i:0;s:27:"access_sql_interface_screen";i:1;s:20:"access_course_screen";i:2;s:21:"access_student_screen";i:3;s:28:"access_email_template_screen";i:4;s:24:"access_course_sec_screen";i:5;s:23:"course_sec_inquiry_only";i:6;s:19:"course_inquiry_only";i:7;s:20:"access_person_screen";i:8;s:23:"access_acad_prog_screen";i:9;s:22:"acad_prog_inquiry_only";i:10;s:23:"access_error_log_screen";i:11;s:20:"access_report_screen";i:12;s:20:"address_inquiry_only";i:13;s:25:"access_save_query_screens";i:14;s:12:"access_forms";i:15;s:17:"create_fac_record";i:16;s:24:"access_stu_roster_screen";i:17;s:22:"access_bill_tbl_screen";i:18;s:17:"add_crse_sec_bill";i:19;s:11:"import_data";i:20;s:19:"person_inquiry_only";i:21;s:19:"access_staff_screen";i:22;s:18:"staff_inquiry_only";i:23;s:19:"create_staff_record";i:24;s:16:"access_dashboard";i:25;s:23:"access_student_accounts";i:26;s:16:"access_academics";i:27;s:22:"access_human_resources";i:28;s:17:"submit_timesheets";i:29;s:10:"access_sql";i:30;s:18:"access_person_mgmt";i:31;s:22:"access_payment_gateway";}');

CREATE TABLE IF NOT EXISTS `role_perms` (
  `ID` bigint(20) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `roleID` bigint(20) NOT NULL,
  `permID` bigint(20) NOT NULL,
  `value` tinyint(1) NOT NULL DEFAULT '0',
  `addDate` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `roleID_2` (`roleID`,`permID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `role_perms` VALUES(00000000000000000156, 11, 92, 1, '2013-09-03 11:30:43');

INSERT INTO `role_perms` VALUES(00000000000000000201, 8, 21, 1, '2013-09-03 12:03:29');

INSERT INTO `role_perms` VALUES(00000000000000000238, 8, 23, 1, '2013-09-03 12:03:29');

INSERT INTO `role_perms` VALUES(00000000000000000268, 8, 22, 1, '2013-09-03 12:04:18');

INSERT INTO `role_perms` VALUES(00000000000000000292, 8, 20, 1, '2013-09-03 12:04:18');

INSERT INTO `role_perms` VALUES(00000000000000000309, 9, 84, 1, '2013-09-03 12:05:33');

INSERT INTO `role_perms` VALUES(00000000000000000310, 9, 107, 1, '2013-09-03 12:05:33');

INSERT INTO `role_perms` VALUES(00000000000000000462, 10, 176, 1, '2013-09-03 12:36:35');

INSERT INTO `role_perms` VALUES(00000000000000000470, 12, 84, 1, '2013-09-03 12:37:49');

INSERT INTO `role_perms` VALUES(00000000000000000471, 12, 107, 1, '2013-09-03 12:37:49');

INSERT INTO `role_perms` VALUES(00000000000000000712, 13, 24, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000713, 13, 25, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000714, 13, 156, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000715, 13, 140, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000716, 13, 144, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000717, 13, 164, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000718, 13, 124, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000719, 13, 128, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000720, 13, 116, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000721, 13, 152, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000722, 13, 132, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000723, 13, 136, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000724, 13, 160, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000725, 13, 173, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000726, 13, 29, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000727, 13, 148, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000728, 13, 120, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000729, 13, 33, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000730, 13, 155, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000731, 13, 139, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000732, 13, 143, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000733, 13, 163, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000734, 13, 123, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000735, 13, 127, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000736, 13, 27, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000737, 13, 158, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000738, 13, 142, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000739, 13, 146, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000740, 13, 166, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000741, 13, 126, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000742, 13, 130, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000743, 13, 118, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000744, 13, 154, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000745, 13, 134, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000746, 13, 138, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000747, 13, 162, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000748, 13, 175, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000749, 13, 31, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000750, 13, 150, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000751, 13, 122, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000752, 13, 35, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000753, 13, 115, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000754, 13, 26, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000755, 13, 99, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000756, 13, 157, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000757, 13, 141, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000758, 13, 145, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000759, 13, 165, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000760, 13, 125, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000761, 13, 129, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000762, 13, 117, 1, '2013-09-03 22:37:31');

INSERT INTO `role_perms` VALUES(00000000000000000763, 13, 153, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000764, 13, 133, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000765, 13, 137, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000766, 13, 161, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000767, 13, 174, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000768, 13, 30, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000769, 13, 149, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000770, 13, 121, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000771, 13, 34, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000772, 13, 109, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000773, 13, 151, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000774, 13, 131, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000775, 13, 135, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000776, 13, 159, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000777, 13, 172, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000778, 13, 28, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000779, 13, 147, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000780, 13, 119, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000781, 13, 32, 1, '2013-09-03 22:40:18');

INSERT INTO `role_perms` VALUES(00000000000000000971, 11, 180, 1, '2013-09-04 04:51:52');

INSERT INTO `role_perms` VALUES(00000000000000000993, 9, 89, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000000994, 9, 85, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000000995, 9, 218, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000000996, 9, 223, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000000997, 9, 168, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000000998, 9, 100, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000000999, 9, 79, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001000, 9, 36, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001001, 9, 78, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001002, 9, 74, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001003, 9, 102, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001004, 9, 40, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001005, 9, 101, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001006, 9, 169, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001007, 9, 103, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001008, 9, 44, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001009, 9, 179, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001010, 9, 80, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001011, 9, 180, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001012, 9, 208, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001013, 9, 104, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001014, 9, 48, 1, '2014-02-13 09:56:10');

INSERT INTO `role_perms` VALUES(00000000000000001015, 12, 89, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001016, 12, 85, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001017, 12, 218, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001018, 12, 223, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001019, 12, 100, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001020, 12, 79, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001021, 12, 36, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001022, 12, 78, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001023, 12, 74, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001024, 12, 102, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001025, 12, 40, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001026, 12, 101, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001027, 12, 103, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001028, 12, 44, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001029, 12, 179, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001030, 12, 80, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001031, 12, 180, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001032, 12, 208, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001033, 12, 104, 1, '2014-02-13 09:56:35');

INSERT INTO `role_perms` VALUES(00000000000000001034, 12, 48, 1, '2014-02-13 09:56:35');

CREATE TABLE IF NOT EXISTS `room` (
  `roomID` int(11) NOT NULL AUTO_INCREMENT,
  `roomCode` varchar(11) NOT NULL,
  `buildingCode` varchar(11) NOT NULL,
  `roomNumber` varchar(11) NOT NULL,
  `roomCap` int(4) NOT NULL,
  PRIMARY KEY (`roomID`),
  UNIQUE KEY `roomKey` (`roomCode`),
  KEY `buildingCode` (`buildingCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `room` VALUES(00000000001, 'NULL', 'NULL', '', 0);

CREATE TABLE IF NOT EXISTS `saved_query` (
  `savedQueryID` bigint(20) NOT NULL AUTO_INCREMENT,
  `personID` bigint(20) NOT NULL,
  `savedQueryName` varchar(80) NOT NULL,
  `savedQuery` text NOT NULL,
  `purgeQuery` enum('0','1') NOT NULL DEFAULT '0',
  `shared` text,
  `createdDate` date NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`savedQueryID`),
  KEY `personID` (`personID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `school` (
  `schoolID` int(11) NOT NULL AUTO_INCREMENT,
  `schoolCode` varchar(11) NOT NULL,
  `schoolName` varchar(180) NOT NULL,
  `buildingCode` varchar(11) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`schoolID`),
  UNIQUE KEY `schoolCode` (`schoolCode`),
  KEY `buildingCode` (`buildingCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `school` VALUES(00000000001, 'NULL', 'NULL', 'NULL', '{now}');

CREATE TABLE IF NOT EXISTS `screen` (
`id` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `relativeURL` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `screen` VALUES(1, 'SYSS', 'System Settings', 'setting/');

INSERT INTO `screen` VALUES(2, 'MPRM', 'Manage Permissions', 'permission/');

INSERT INTO `screen` VALUES(3, 'APRM', 'Add Permission', 'permission/add/');

INSERT INTO `screen` VALUES(4, 'MRLE', 'Manage Roles', 'role/');

INSERT INTO `screen` VALUES(5, 'AUDT', 'Audit Trail', 'audit-trail/');

INSERT INTO `screen` VALUES(6, 'SQL', 'SQL Interface', 'sql/');

INSERT INTO `screen` VALUES(7, 'ARLE', 'Add Role', 'role/add/');

INSERT INTO `screen` VALUES(8, 'SCH', 'School Form', 'form/school/');

INSERT INTO `screen` VALUES(9, 'SEM', 'Semester Form', 'form/semester/');

INSERT INTO `screen` VALUES(10, 'TERM', 'Term Form', 'form/term/');

INSERT INTO `screen` VALUES(11, 'AYR', 'Acad Year Form', 'form/acad-year/');

INSERT INTO `screen` VALUES(12, 'CRSE', 'Course', 'crse/');

INSERT INTO `screen` VALUES(13, 'DEPT', 'Department Form', 'form/department/');

INSERT INTO `screen` VALUES(14, 'CRL', 'Credit Load Form', 'form/credit-load/');

INSERT INTO `screen` VALUES(15, 'DEG', 'Degree Form', 'form/degree/');

INSERT INTO `screen` VALUES(16, 'MAJR', 'Major Form', 'form/major/');

INSERT INTO `screen` VALUES(17, 'MINR', 'Minor Form', 'form/minor/');

INSERT INTO `screen` VALUES(18, 'PROG', 'Academic Program', 'program/');

INSERT INTO `screen` VALUES(19, 'CCD', 'CCD Form', 'form/ccd/');

INSERT INTO `screen` VALUES(20, 'CIP', 'CIP Form', 'form/cip/');

INSERT INTO `screen` VALUES(21, 'LOC', 'Location Form', 'form/location/');

INSERT INTO `screen` VALUES(22, 'BLDG', 'Building Form', 'form/building/');

INSERT INTO `screen` VALUES(23, 'ROOM', 'Room Form', 'form/room/');

INSERT INTO `screen` VALUES(24, 'SPEC', 'Specialization From', 'form/specialization/');

INSERT INTO `screen` VALUES(25, 'SUBJ', 'Subject Form', 'form/subject/');

INSERT INTO `screen` VALUES(26, 'CLYR', 'Class Year Form', 'form/class-year/');

INSERT INTO `screen` VALUES(27, 'APRG', 'Add Acad Program', 'program/add/');

INSERT INTO `screen` VALUES(28, 'ACRS', 'Add Course', 'crse/add/');

INSERT INTO `screen` VALUES(29, 'SECT', 'Course Section', 'sect/');

INSERT INTO `screen` VALUES(30, 'RGN', 'Course Registration', 'sect/rgn/');

INSERT INTO `screen` VALUES(31, 'NSCP', 'NSLC Purge', 'nslc/purge/');

INSERT INTO `screen` VALUES(32, 'NSCS', 'NSLC Setup', 'nslc/setup/');

INSERT INTO `screen` VALUES(33, 'NSCX', 'NSLC Extraction', 'nslc/extraction/');

INSERT INTO `screen` VALUES(34, 'NSCE', 'NSLC Verification', 'nslc/verification/');

INSERT INTO `screen` VALUES(35, 'NSCC', 'NSLC Correction', 'nslc/');

INSERT INTO `screen` VALUES(36, 'NSCT', 'NSLC File', 'nslc/file/');

INSERT INTO `screen` VALUES(37, 'NAE', 'Name & Address', 'nae/');

INSERT INTO `screen` VALUES(38, 'APER', 'Add Person', 'nae/add/');

INSERT INTO `screen` VALUES(39, 'SPRO', 'Student Profile', 'stu/');

INSERT INTO `screen` VALUES(40, 'FAC', 'Faculty Profile', 'faculty/');

INSERT INTO `screen` VALUES(41, 'INST', 'Institution', 'appl/inst/');

INSERT INTO `screen` VALUES(42, 'AINST', 'New Institution', 'appl/inst/add/');

INSERT INTO `screen` VALUES(43, 'APPL', 'Application', 'appl/');

INSERT INTO `screen` VALUES(44, 'BRGN', 'Batch Course Registration', 'sect/brgn/');

INSERT INTO `screen` VALUES(45, 'STAF', 'Staff', 'staff/');

INSERT INTO `screen` VALUES(46, 'TRAN', 'Transcript', 'stu/tran/');

INSERT INTO `screen` VALUES(47, 'SLR', 'Student Load Rules', 'form/student-load-rule/');

INSERT INTO `screen` VALUES(48, 'RSTR', 'Restriction Codes', 'form/rstr-code/');

INSERT INTO `screen` VALUES(49, 'GRSC', 'Grade Scale', 'form/grade-scale/');

INSERT INTO `screen` VALUES(50, 'SROS', 'Student Roster', 'sect/sros/');

INSERT INTO `screen` VALUES(51, 'EXTR', 'External Course', 'crse/extr/');

INSERT INTO `screen` VALUES(52, 'ATCEQ', 'New Transfer Course Equivalency', 'crse/atceq/');

INSERT INTO `screen` VALUES(53, 'TCEQ', 'Transfer Course Equivalency', 'crse/tceq/');

INSERT INTO `screen` VALUES(54, 'TCRE', 'Transfer Credit', 'crse/tcre/');

ALTER TABLE `screen` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `code` (`code`);

ALTER TABLE `screen` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `semester` (
  `semesterID` bigint(20) NOT NULL AUTO_INCREMENT,
  `acadYearCode` varchar(11) NOT NULL,
  `semCode` varchar(11) NOT NULL,
  `semName` varchar(80) NOT NULL,
  `semStartDate` date NOT NULL DEFAULT '0000-00-00',
  `semEndDate` date NOT NULL DEFAULT '0000-00-00',
  `active` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`semesterID`),
  UNIQUE KEY `semCode` (`semCode`),
  KEY `acadYearCode` (`acadYearCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `semester` VALUES(00000000001, 'NULL', 'NULL', '', '2014-02-26', '2014-02-26', '1');

CREATE TABLE IF NOT EXISTS `specialization` (
  `specID` int(11) NOT NULL AUTO_INCREMENT,
  `specCode` varchar(11) NOT NULL,
  `specName` varchar(80) NOT NULL,
  PRIMARY KEY (`specID`),
  UNIQUE KEY `specCode` (`specCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `specialization` VALUES(00000000001, 'NULL', '');

CREATE TABLE IF NOT EXISTS `staff` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `staffID` bigint(20) NOT NULL,
  `schoolCode` varchar(11) DEFAULT NULL,
  `buildingCode` varchar(11) DEFAULT NULL,
  `officeCode` varchar(11) DEFAULT NULL,
  `office_phone` varchar(15) NOT NULL,
  `deptCode` varchar(11) NOT NULL,
  `status` enum('A','I') NOT NULL DEFAULT 'A',
  `addDate` date NOT NULL,
  `approvedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staffID` (`staffID`),
  KEY `approvedBy` (`approvedBy`),
  KEY `schoolCode` (`schoolCode`),
  KEY `buildingCode` (`buildingCode`),
  KEY `officeCode` (`officeCode`),
  KEY `deptCode` (`deptCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `staff` VALUES(00000000001, 00000001, 'NULL', 'NULL', 'NULL', '', 'NULL', 'A', '{addDate}', 00000001, '{now}');

CREATE TABLE IF NOT EXISTS `staff_meta` (
  `sMetaID` bigint(20) NOT NULL AUTO_INCREMENT,
  `jobStatusCode` varchar(3) NOT NULL,
  `jobID` int(11) NOT NULL,
  `staffID` bigint(20) NOT NULL,
  `supervisorID` bigint(20) NOT NULL,
  `staffType` varchar(3) NOT NULL,
  `hireDate` date NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `addDate` date NOT NULL,
  `approvedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sMetaID`),
  KEY `staffID` (`staffID`),
  KEY `supervisorID` (`supervisorID`),
  KEY `approvedBy` (`approvedBy`),
  KEY `jobStatusCode` (`jobStatusCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `staff_meta` VALUES(2, 'FT', 1, 00000001, 00000001, 'STA', '2013-11-04', '2013-11-18', '0000-00-00', '{addDate}', 00000001, '{now}');

CREATE TABLE IF NOT EXISTS `state` (
  `id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL,
  `name` varchar(180) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `state` VALUES(00000000001, 'AL', 'Alabama');

INSERT INTO `state` VALUES(00000000002, 'AK', 'Alaska');

INSERT INTO `state` VALUES(00000000003, 'AZ', 'Arizona');

INSERT INTO `state` VALUES(00000000004, 'AR', 'Arkansas');

INSERT INTO `state` VALUES(00000000005, 'CA', 'California');

INSERT INTO `state` VALUES(00000000006, 'CO', 'Colorado');

INSERT INTO `state` VALUES(00000000007, 'CT', 'Connecticut');

INSERT INTO `state` VALUES(00000000008, 'DE', 'Delaware');

INSERT INTO `state` VALUES(00000000009, 'DC', 'District of Columbia');

INSERT INTO `state` VALUES(00000000010, 'FL', 'Florida');

INSERT INTO `state` VALUES(00000000011, 'GA', 'Georgia');

INSERT INTO `state` VALUES(00000000012, 'HI', 'Hawaii');

INSERT INTO `state` VALUES(00000000013, 'ID', 'Idaho');

INSERT INTO `state` VALUES(00000000014, 'IL', 'Illinois');

INSERT INTO `state` VALUES(00000000015, 'IN', 'Indiana');

INSERT INTO `state` VALUES(00000000016, 'IA', 'Iowa');

INSERT INTO `state` VALUES(00000000017, 'KS', 'Kansas');

INSERT INTO `state` VALUES(00000000018, 'KY', 'Kentucky');

INSERT INTO `state` VALUES(00000000019, 'LA', 'Louisiana');

INSERT INTO `state` VALUES(00000000020, 'ME', 'Maine');

INSERT INTO `state` VALUES(00000000021, 'MD', 'Maryland');

INSERT INTO `state` VALUES(00000000022, 'MA', 'Massachusetts');

INSERT INTO `state` VALUES(00000000023, 'MI', 'Michigan');

INSERT INTO `state` VALUES(00000000024, 'MN', 'Minnesota');

INSERT INTO `state` VALUES(00000000025, 'MS', 'Mississippi');

INSERT INTO `state` VALUES(00000000026, 'MO', 'Missouri');

INSERT INTO `state` VALUES(00000000027, 'MT', 'Montana');

INSERT INTO `state` VALUES(00000000028, 'NE', 'Nebraska');

INSERT INTO `state` VALUES(00000000029, 'NV', 'Nevada');

INSERT INTO `state` VALUES(00000000030, 'NH', 'New Hampshire');

INSERT INTO `state` VALUES(00000000031, 'NJ', 'New Jersey');

INSERT INTO `state` VALUES(00000000032, 'NM', 'New Mexico');

INSERT INTO `state` VALUES(00000000033, 'NY', 'New York');

INSERT INTO `state` VALUES(00000000034, 'NC', 'North Carolina');

INSERT INTO `state` VALUES(00000000035, 'ND', 'North Dakota');

INSERT INTO `state` VALUES(00000000036, 'OH', 'Ohio');

INSERT INTO `state` VALUES(00000000037, 'OK', 'Oklahoma');

INSERT INTO `state` VALUES(00000000038, 'OR', 'Oregon');

INSERT INTO `state` VALUES(00000000039, 'PA', 'Pennsylvania');

INSERT INTO `state` VALUES(00000000040, 'RI', 'Rhode Island');

INSERT INTO `state` VALUES(00000000041, 'SC', 'South Carolina');

INSERT INTO `state` VALUES(00000000042, 'SD', 'South Dakota');

INSERT INTO `state` VALUES(00000000043, 'TN', 'Tennessee');

INSERT INTO `state` VALUES(00000000044, 'TX', 'Texas');

INSERT INTO `state` VALUES(00000000045, 'UT', 'Utah');

INSERT INTO `state` VALUES(00000000046, 'VT', 'Vermont');

INSERT INTO `state` VALUES(00000000047, 'VA', 'Virginia');

INSERT INTO `state` VALUES(00000000048, 'WA', 'Washington');

INSERT INTO `state` VALUES(00000000049, 'WV', 'West Virginia');

INSERT INTO `state` VALUES(00000000050, 'WI', 'Wisconsin');

INSERT INTO `state` VALUES(00000000051, 'WY', 'Wyoming');

INSERT INTO `state` VALUES(00000000052, 'AB', 'Alberta');

INSERT INTO `state` VALUES(00000000053, 'BC', 'British Columbia');

INSERT INTO `state` VALUES(00000000054, 'MB', 'Manitoba');

INSERT INTO `state` VALUES(00000000055, 'NL', 'Newfoundland');

INSERT INTO `state` VALUES(00000000056, 'NB', 'New Brunswick');

INSERT INTO `state` VALUES(00000000057, 'NS', 'Nova Scotia');

INSERT INTO `state` VALUES(00000000058, 'NT', 'Northwest Territories');

INSERT INTO `state` VALUES(00000000059, 'NU', 'Nunavut');

INSERT INTO `state` VALUES(00000000060, 'ON', 'Ontario');

INSERT INTO `state` VALUES(00000000061, 'PE', 'Prince Edward Island');

INSERT INTO `state` VALUES(00000000062, 'QC', 'Quebec');

INSERT INTO `state` VALUES(00000000063, 'SK', 'Saskatchewan');

INSERT INTO `state` VALUES(00000000064, 'YT', 'Yukon Territory');

CREATE TABLE IF NOT EXISTS `student` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `stuID` bigint(20) NOT NULL,
  `status` enum('A','I') NOT NULL DEFAULT 'A',
  `tags` varchar(255) NOT NULL,
  `addDate` date NOT NULL,
  `approvedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `stuID` (`stuID`),
  KEY `approvedBy` (`approvedBy`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `student_load_rule` (
  `slrID` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(1) NOT NULL,
  `min_cred` double(4,1) NOT NULL,
  `max_cred` double(4,1) NOT NULL,
  `term` varchar(255) NOT NULL,
  `acadLevelCode` varchar(255) NOT NULL,
  `active` enum('1','0') NOT NULL,
  PRIMARY KEY (`slrID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `student_load_rule` VALUES(00000000001, 'F', 12.0, 24.0, 'FA\\SP\\SU', 'CE\\UG\\GR\\PhD', '1');

INSERT INTO `student_load_rule` VALUES(00000000002, 'Q', 9.0, 11.0, 'FA\\SP\\SU', 'CE\\UG\\GR\\PhD', '1');

INSERT INTO `student_load_rule` VALUES(00000000003, 'H', 6.0, 8.0, 'FA\\SP\\SU', 'CE\\UG\\GR\\PhD', '1');

INSERT INTO `student_load_rule` VALUES(00000000004, 'L', 0.0, 5.0, 'FA\\SP\\SU', 'CE\\UG\\GR\\PhD', '1');

CREATE TABLE IF NOT EXISTS `stu_acad_cred` (
  `stuAcadCredID` bigint(20) NOT NULL AUTO_INCREMENT,
  `stuID` bigint(20) NOT NULL,
  `courseID` bigint(20) NOT NULL,
  `courseSecID` bigint(20) DEFAULT NULL,
  `courseCode` varchar(25) NOT NULL,
  `courseSecCode` varchar(50) DEFAULT NULL,
  `sectionNumber` varchar(5) DEFAULT NULL,
  `courseSection` varchar(60) DEFAULT NULL,
  `termCode` varchar(11) NOT NULL,
  `reportingTerm` varchar(5) DEFAULT NULL,
  `subjectCode` varchar(11) NOT NULL,
  `deptCode` varchar(11) NOT NULL,
  `shortTitle` varchar(25) NOT NULL,
  `longTitle` varchar(60) NOT NULL,
  `compCred` double(4,1) NOT NULL DEFAULT '0.0',
  `gradePoints` double(4,2) NOT NULL DEFAULT '0.00',
  `attCred` double(4,1) NOT NULL DEFAULT '0.0',
  `ceu` double(4,1) NOT NULL DEFAULT '0.0',
  `status` enum('A','N','D','W','C','TR') NOT NULL DEFAULT 'A',
  `statusDate` date NOT NULL,
  `statusTime` varchar(10) NOT NULL,
  `acadLevelCode` varchar(4) NOT NULL,
  `courseLevelCode` varchar(5) NOT NULL,
  `grade` varchar(2) DEFAULT NULL,
  `creditType` varchar(6) NOT NULL DEFAULT 'I',
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `addedBy` bigint(20) NOT NULL,
  `addDate` date DEFAULT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stuAcadCredID`),
  UNIQUE KEY `stuAcadCred` (`stuID`,`courseSecID`),
  KEY `courseSecCode` (`courseSecCode`),
  KEY `termCode` (`termCode`),
  KEY `stu_acad_cred_status` (`status`),
  KEY `courseID` (`courseID`),
  KEY `courseSecID` (`courseSecID`),
  KEY `courseCode` (`courseCode`),
  KEY `courseSection` (`courseSection`),
  KEY `subjectCode` (`subjectCode`),
  KEY `deptCode` (`deptCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `stu_acad_level` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `stuID` bigint(20) NOT NULL,
  `acadProgCode` varchar(20) NOT NULL,
  `acadLevelCode` varchar(4) NOT NULL,
  `addDate` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_acad_level` (`stuID`,`acadProgCode`),
  KEY `acadProgCode` (`acadProgCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `stu_acct_bill` (
`ID` bigint(20) NOT NULL,
  `billID` varchar(11) NOT NULL,
  `stuID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `authCode` varchar(23) NOT NULL,
  `stu_comments` text NOT NULL,
  `staff_comments` text NOT NULL,
  `balanceDue` enum('1','0') NOT NULL DEFAULT '1',
  `postedBy` bigint(20) NOT NULL,
  `billingDate` date NOT NULL,
  `billTimeStamp` datetime NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `stu_acct_bill` ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `billID` (`billID`), ADD KEY `stuID` (`stuID`), ADD KEY `termCode` (`termCode`), ADD KEY `postedBy` (`postedBy`);

ALTER TABLE `stu_acct_bill` MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `stu_acct_fee` (
`ID` bigint(20) NOT NULL,
  `billID` varchar(11) NOT NULL,
  `stuID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `type` varchar(11) NOT NULL,
  `description` varchar(125) NOT NULL,
  `amount` double(6,2) NOT NULL,
  `feeDate` date NOT NULL,
  `feeTimeStamp` datetime NOT NULL,
  `postedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `stu_acct_fee` ADD PRIMARY KEY (`ID`), ADD KEY `billID` (`billID`), ADD KEY `stuID` (`stuID`), ADD KEY `termCode` (`termCode`), ADD KEY `postedBy` (`postedBy`);

ALTER TABLE `stu_acct_fee` MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `stu_acct_pp` (
`ID` bigint(20) NOT NULL,
  `stuID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `payFrequency` enum('1','7','14','30','365') NOT NULL,
  `amount` double(6,2) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `comments` text NOT NULL,
  `addDate` date NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `stu_acct_pp` ADD PRIMARY KEY (`ID`);

ALTER TABLE `stu_acct_pp` MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `stu_acct_tuition` (
`ID` bigint(20) NOT NULL,
  `stuID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `total` double(6,2) NOT NULL,
  `postedBy` bigint(20) NOT NULL,
  `tuitionTimeStamp` datetime NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `stu_acct_tuition` ADD PRIMARY KEY (`ID`), ADD KEY `termCode` (`termCode`);

ALTER TABLE `stu_acct_tuition` MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `stu_course_sec` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `stuID` bigint(20) NOT NULL,
  `courseSecID` bigint(20) DEFAULT NULL,
  `courseSecCode` varchar(50) NOT NULL,
  `courseSection` varchar(60) DEFAULT NULL,
  `termCode` varchar(11) NOT NULL,
  `courseCredits` double(4,1) NOT NULL DEFAULT '0.0',
  `ceu` double(4,1) NOT NULL DEFAULT '0.0',
  `regDate` date DEFAULT NULL,
  `regTime` varchar(10) DEFAULT NULL,
  `status` enum('A','N','D','W','C') NOT NULL DEFAULT 'A',
  `statusDate` date NOT NULL,
  `statusTime` varchar(10) NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stuCourseSec` (`stuID`,`courseSecID`),
  KEY `courseSecCode` (`courseSecCode`),
  KEY `termCode` (`termCode`),
  KEY `addedBy` (`addedBy`),
  KEY `stu_course_sec_status` (`status`),
  KEY `courseSecID` (`courseSecID`),
  KEY `courseSection` (`courseSection`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `stu_program` (
  `stuProgID` bigint(20) NOT NULL AUTO_INCREMENT,
  `stuID` bigint(20) NOT NULL,
  `advisorID` bigint(20) NOT NULL,
  `catYearCode` varchar(11) NOT NULL,
  `acadProgCode` varchar(20) NOT NULL,
  `currStatus` varchar(1) NOT NULL,
  `eligible_to_graduate` enum('1','0') NOT NULL DEFAULT '0',
  `antGradDate` varchar(8) NOT NULL,
  `graduationDate` date NOT NULL,
  `statusDate` date NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `comments` text NOT NULL,
  `approvedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stuProgID`),
  UNIQUE KEY `student_program` (`stuID`,`acadProgCode`),
  KEY `approvedBy` (`approvedBy`),
  KEY `progID` (`acadProgCode`),
  KEY `stu_program_status` (`currStatus`),
  KEY `advisorID` (`advisorID`),
  KEY `catYearCode` (`catYearCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `stu_rgn_cart` (
  `stuID` bigint(20) NOT NULL,
  `courseSecID` bigint(20) NOT NULL,
  `deleteDate` date NOT NULL,
  UNIQUE KEY `stu_rgn` (`stuID`,`courseSecID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `stu_term` (
  `stuID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `termCredits` double(6,1) NOT NULL DEFAULT '0.0',
  `addDateTime` datetime NOT NULL,
  `acadLevelCode` varchar(4) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `stuTerm` (`stuID`,`termCode`,`acadLevelCode`),
  KEY `termCode` (`termCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `stu_term_gpa` (
  `stuID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `acadLevelCode` varchar(4) NOT NULL,
  `attCred` double(4,1) NOT NULL DEFAULT '0.0',
  `compCred` double(4,1) NOT NULL DEFAULT '0.0',
  `gradePoints` double(4,1) NOT NULL DEFAULT '0.0',
  `termGPA` double(4,2) NOT NULL DEFAULT '0.00',
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `stu_term_gpa_unique` (`stuID`,`termCode`,`acadLevelCode`),
  KEY `termCode` (`termCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `stu_term_load` (
  `stuID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `stuLoad` varchar(2) NOT NULL,
  `acadLevelCode` varchar(4) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `stuTermLoad` (`stuID`,`termCode`,`acadLevelCode`),
  KEY `student_load` (`stuLoad`),
  KEY `termID` (`termCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `subject` (
  `subjectID` int(11) NOT NULL AUTO_INCREMENT,
  `subjectCode` varchar(11) NOT NULL,
  `subjectName` varchar(180) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`subjectID`),
  UNIQUE KEY `subjCode` (`subjectCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `subject` VALUES(00000000001, 'NULL', '', '{now}');

CREATE TABLE IF NOT EXISTS `term` (
  `termID` bigint(20) NOT NULL AUTO_INCREMENT,
  `semCode` varchar(11) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `termName` varchar(180) NOT NULL DEFAULT '',
  `reportingTerm` varchar(5) NOT NULL,
  `dropAddEndDate` date NOT NULL DEFAULT '0000-00-00',
  `termStartDate` date NOT NULL DEFAULT '0000-00-00',
  `termEndDate` date NOT NULL DEFAULT '0000-00-00',
  `active` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`termID`),
  UNIQUE KEY `termCode` (`termCode`),
  KEY `semesterID` (`semCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `term` VALUES(00000000001, 'NULL', 'NULL', '', '', '0000-00-00', '0000-00-00', '0000-00-00', '1');

CREATE TABLE IF NOT EXISTS `timesheet` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `employeeID` bigint(20) NOT NULL,
  `jobID` int(11) NOT NULL,
  `workWeek` date NOT NULL,
  `startDateTime` datetime NOT NULL,
  `endDateTime` datetime NOT NULL,
  `note` text NOT NULL,
  `status` enum('P','R','A') NOT NULL DEFAULT 'P',
  `addDate` varchar(20) NOT NULL,
  `addedBy` bigint(20) NOT NULL,
  `approvedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `employeeID` (`employeeID`),
  KEY `addedBy` (`addedBy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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

INSERT INTO `person` (`personID`, `uname`, `password`, `fname`, `lname`, `email`,`personType`,`approvedDate`,`approvedBy`) VALUES ('', '{uname}', '{pass}', '{fname}', '{lname}', '{aemail}', 'STA', '{now}', '1');

INSERT INTO `person_roles` VALUES(1, 1, 8, '{now}');

ALTER TABLE `acad_program` ADD FOREIGN KEY (`deptCode`) REFERENCES `department` (`deptCode`) ON UPDATE CASCADE;

ALTER TABLE `acad_program` ADD FOREIGN KEY (`schoolCode`) REFERENCES `school` (`schoolCode`) ON UPDATE CASCADE;

ALTER TABLE `acad_program` ADD FOREIGN KEY (`acadYearCode`) REFERENCES `acad_year` (`acadYearCode`) ON UPDATE CASCADE;

ALTER TABLE `acad_program` ADD FOREIGN KEY (`degreeCode`) REFERENCES `degree` (`degreeCode`) ON UPDATE CASCADE;

ALTER TABLE `acad_program` ADD FOREIGN KEY (`ccdCode`) REFERENCES `ccd` (`ccdCode`) ON UPDATE CASCADE;

ALTER TABLE `acad_program` ADD FOREIGN KEY (`majorCode`) REFERENCES `major` (`majorCode`) ON UPDATE CASCADE;

ALTER TABLE `acad_program` ADD FOREIGN KEY (`minorCode`) REFERENCES `minor` (`minorCode`) ON UPDATE CASCADE;

ALTER TABLE `acad_program` ADD FOREIGN KEY (`specCode`) REFERENCES `specialization` (`specCode`) ON UPDATE CASCADE;

ALTER TABLE `acad_program` ADD FOREIGN KEY (`cipCode`) REFERENCES `cip` (`cipCode`) ON UPDATE CASCADE;

ALTER TABLE `acad_program` ADD FOREIGN KEY (`locationCode`) REFERENCES `location` (`locationCode`) ON UPDATE CASCADE;

ALTER TABLE `address` ADD FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `address` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `application` ADD FOREIGN KEY (`acadProgCode`) REFERENCES `acad_program` (`acadProgCode`) ON UPDATE CASCADE;

ALTER TABLE `application` ADD FOREIGN KEY (`startTerm`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `application` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `application` ADD FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `assignment` ADD FOREIGN KEY (`courseSecID`) REFERENCES `course_sec` (`courseSecID`) ON UPDATE CASCADE;

ALTER TABLE `attendance` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `attendance` ADD FOREIGN KEY (`courseSecID`) REFERENCES `course_sec` (`courseSecID`) ON UPDATE CASCADE;

ALTER TABLE `building` ADD FOREIGN KEY (`locationCode`) REFERENCES `location` (`locationCode`) ON UPDATE CASCADE;

ALTER TABLE `course` ADD FOREIGN KEY (`subjectCode`) REFERENCES `subject` (`subjectCode`) ON UPDATE CASCADE;

ALTER TABLE `course` ADD FOREIGN KEY (`deptCode`) REFERENCES `department` (`deptCode`) ON UPDATE CASCADE;

ALTER TABLE `course` ADD FOREIGN KEY (`approvedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `course_sec` ADD FOREIGN KEY (`buildingCode`) REFERENCES `building` (`buildingCode`) ON UPDATE CASCADE;

ALTER TABLE `course_sec` ADD FOREIGN KEY (`roomCode`) REFERENCES `room` (`roomCode`) ON UPDATE CASCADE;

ALTER TABLE `course_sec` ADD FOREIGN KEY (`locationCode`) REFERENCES `location` (`locationCode`) ON UPDATE CASCADE;

ALTER TABLE `course_sec` ADD FOREIGN KEY (`deptCode`) REFERENCES `department` (`deptCode`) ON UPDATE CASCADE;

ALTER TABLE `course_sec` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `course_sec` ADD FOREIGN KEY (`courseID`) REFERENCES `course` (`courseID`) ON UPDATE CASCADE;

ALTER TABLE `course_sec` ADD FOREIGN KEY (`approvedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `email_template` ADD FOREIGN KEY (`deptCode`) REFERENCES `department` (`deptCode`) ON UPDATE CASCADE;

ALTER TABLE `event` ADD FOREIGN KEY (`catID`) REFERENCES `event_category` (`catID`) ON UPDATE CASCADE;

ALTER TABLE `event` ADD FOREIGN KEY (`requestor`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `event` ADD FOREIGN KEY (`roomCode`) REFERENCES `room` (`roomCode`) ON UPDATE CASCADE;

ALTER TABLE `event` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `event` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `event_meta` ADD FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `event_meta` ADD FOREIGN KEY (`roomCode`) REFERENCES `room` (`roomCode`) ON UPDATE CASCADE;

ALTER TABLE `event_meta` ADD FOREIGN KEY (`requestor`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `event_meta` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `event_request` ADD FOREIGN KEY (`requestor`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `event_request` ADD FOREIGN KEY (`roomCode`) REFERENCES `room` (`roomCode`) ON UPDATE CASCADE;

ALTER TABLE `event_request` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `external_course` ADD FOREIGN KEY (`instCode`) REFERENCES `institution` (`fice_ceeb`) ON UPDATE CASCADE;

ALTER TABLE `external_course` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `gl_transaction` ADD FOREIGN KEY (`jeID`) REFERENCES `gl_journal_entry` (`jeID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `gradebook` ADD FOREIGN KEY (`courseSecID`) REFERENCES `course_sec` (`courseSecID`) ON UPDATE CASCADE;

ALTER TABLE `hiatus` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `institution_attended` ADD FOREIGN KEY (`fice_ceeb`) REFERENCES `institution` (`fice_ceeb`) ON UPDATE CASCADE;

ALTER TABLE `institution_attended` ADD FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `institution_attended` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `met_news` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `met_page` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `payment` ADD CONSTRAINT pay_fk_stuID FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `payment` ADD CONSTRAINT pay_fk_termCode FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `payment` ADD CONSTRAINT pay_fk_postedBy FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `person` ADD FOREIGN KEY (`approvedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `person_perms` ADD FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `person_roles` ADD FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `refund` ADD CONSTRAINT ref_fk_stuID FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `refund` ADD CONSTRAINT ref_fk_termCode FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `refund` ADD CONSTRAINT ref_fk_postedBy FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `restriction_code` ADD FOREIGN KEY (`deptCode`) REFERENCES `department` (`deptCode`) ON UPDATE CASCADE;

ALTER TABLE `restriction` ADD FOREIGN KEY (`rstrCode`) REFERENCES `restriction_code` (`rstrCode`) ON UPDATE CASCADE;

ALTER TABLE `restriction` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `restriction` ADD FOREIGN KEY (`addedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE;

ALTER TABLE `room` ADD FOREIGN KEY (`buildingCode`) REFERENCES `building` (`buildingCode`) ON UPDATE CASCADE;

ALTER TABLE `saved_query` ADD FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `school` ADD FOREIGN KEY (`buildingCode`) REFERENCES `building` (`buildingCode`) ON UPDATE CASCADE;

ALTER TABLE `semester` ADD FOREIGN KEY (`acadYearCode`) REFERENCES `acad_year` (`acadYearCode`) ON UPDATE CASCADE;

ALTER TABLE `staff` ADD FOREIGN KEY (`schoolCode`) REFERENCES `school` (`schoolCode`) ON UPDATE CASCADE;

ALTER TABLE `staff` ADD FOREIGN KEY (`buildingCode`) REFERENCES `building` (`buildingCode`) ON UPDATE CASCADE;

ALTER TABLE `staff` ADD FOREIGN KEY (`officeCode`) REFERENCES `room` (`roomCode`) ON UPDATE CASCADE;

ALTER TABLE `staff` ADD FOREIGN KEY (`deptCode`) REFERENCES `department` (`deptCode`) ON UPDATE CASCADE;

ALTER TABLE `staff` ADD FOREIGN KEY (`staffID`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `staff` ADD FOREIGN KEY (`approvedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `staff_meta` ADD FOREIGN KEY (`staffID`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE;

ALTER TABLE `staff_meta` ADD FOREIGN KEY (`supervisorID`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE;

ALTER TABLE `staff_meta` ADD FOREIGN KEY (`jobStatusCode`) REFERENCES `job_status` (`typeCode`) ON UPDATE CASCADE;

ALTER TABLE `staff_meta` ADD FOREIGN KEY (`approvedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE;

ALTER TABLE `student` ADD FOREIGN KEY (`stuID`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `student` ADD FOREIGN KEY (`approvedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acad_cred` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acad_cred` ADD FOREIGN KEY (`courseID`) REFERENCES `course` (`courseID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acad_cred` ADD FOREIGN KEY (`courseSecID`) REFERENCES `course_sec` (`courseSecID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acad_cred` ADD FOREIGN KEY (`courseSecCode`) REFERENCES `course_sec` (`courseSecCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_acad_cred` ADD FOREIGN KEY (`courseSection`) REFERENCES `course_sec` (`courseSection`) ON UPDATE CASCADE;

ALTER TABLE `stu_acad_cred` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_acad_cred` ADD FOREIGN KEY (`subjectCode`) REFERENCES `subject` (`subjectCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_acad_cred` ADD FOREIGN KEY (`deptCode`) REFERENCES `department` (`deptCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_acad_cred` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acad_level` ADD FOREIGN KEY (`acadProgCode`) REFERENCES `acad_program` (`acadProgCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_acad_level` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_bill` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_bill` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_bill` ADD FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_fee` ADD CONSTRAINT saf_fk_billID FOREIGN KEY (`billID`) REFERENCES `stu_acct_bill` (`billID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_fee` ADD CONSTRAINT saf_fk_stuID FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_fee` ADD CONSTRAINT saf_fk_termCode FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_fee` ADD CONSTRAINT saf_fk_postedBy FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_pp` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_pp` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_pp` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_tuition` ADD CONSTRAINT sat_fk_stuID FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_tuition` ADD CONSTRAINT sat_fk_termCode FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_tuition` ADD CONSTRAINT sat_fk_postedBy FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `stu_course_sec` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_course_sec` ADD FOREIGN KEY (`courseSecID`) REFERENCES `course_sec` (`courseSecID`) ON UPDATE CASCADE;

ALTER TABLE `stu_course_sec` ADD FOREIGN KEY (`courseSecCode`) REFERENCES `course_sec` (`courseSecCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_course_sec` ADD FOREIGN KEY (`courseSection`) REFERENCES `course_sec` (`courseSection`) ON UPDATE CASCADE;

ALTER TABLE `stu_course_sec` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_course_sec` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `stu_program` ADD FOREIGN KEY (`acadProgCode`) REFERENCES `acad_program` (`acadProgCode`) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `stu_program` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_program` ADD FOREIGN KEY (`approvedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `stu_program` ADD FOREIGN KEY (`advisorID`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE;

ALTER TABLE `stu_program` ADD FOREIGN KEY (`catYearCode`) REFERENCES `acad_year` (`acadYearCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_term` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_term` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `stu_term_gpa` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `stu_term_gpa` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `stu_term_load` ADD FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_term_load` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `term` ADD FOREIGN KEY (`semCode`) REFERENCES `semester` (`semCode`) ON UPDATE CASCADE;

ALTER TABLE `timesheet` ADD FOREIGN KEY (`employeeID`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE;

ALTER TABLE `timesheet` ADD FOREIGN KEY (`addedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_credit` ADD FOREIGN KEY (`equivID`) REFERENCES `transfer_equivalent` (`equivID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_credit` ADD FOREIGN KEY (`stuAcadCredID`) REFERENCES `stu_acad_cred` (`stuAcadCredID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_credit` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_equivalent` ADD FOREIGN KEY (`extrID`) REFERENCES `external_course` (`extrID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_equivalent` ADD FOREIGN KEY (`courseID`) REFERENCES `course` (`courseID`) ON UPDATE CASCADE;

ALTER TABLE `transfer_equivalent` ADD FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;
                  
INSERT INTO `options_meta` VALUES(1, 'dbversion', '00048');
        
INSERT INTO `options_meta` VALUES(2, 'system_email', '{email}');
        
INSERT INTO `options_meta` VALUES(3, 'enable_ssl', '0');
        
INSERT INTO `options_meta` VALUES(4, 'institution_name', '{institutionname}');
        
INSERT INTO `options_meta` VALUES(5, 'cookieexpire', '604800');
        
INSERT INTO `options_meta` VALUES(6, 'cookiepath', '/');
        
INSERT INTO `options_meta` VALUES(7, 'enable_benchmark', '0');
        
INSERT INTO `options_meta` VALUES(8, 'maintenance_mode', '0');
        
INSERT INTO `options_meta` VALUES(9, 'current_term_code', '');
        
INSERT INTO `options_meta` VALUES(10, 'open_registration', '1');
        
INSERT INTO `options_meta` VALUES(11, 'help_desk', 'http://www.edutracsis.com/');
        
INSERT INTO `options_meta` VALUES(12, 'reset_password_text', '<b>eduTrac Password Reset</b><br>Password &amp; Login Information<br><br>You or someone else requested a new password to the eduTrac online system. If you did not request this change, please contact the administrator as soon as possible @ #adminemail#.&nbsp; To log into the eduTrac system, please visit #url# and login with your username and password.<br><br>FULL NAME:&nbsp; #fname# #lname#<br>USERNAME:&nbsp; #uname#<br>PASSWORD:&nbsp; #password#<br><br>If you need further assistance, please read the documentation at #helpdesk#.<br><br>KEEP THIS IN A SAFE AND SECURE LOCATION.<br><br>Thank You,<br>eduTrac Web Team<br>');
        
INSERT INTO `options_meta` VALUES(13, 'api_key', '');

INSERT INTO `options_meta` VALUES(14, 'room_request_email', 'request@myschool.edu');

INSERT INTO `options_meta` VALUES(15, 'room_request_text', '<p>&nbsp;</p>\r\n<table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#F4F3F4">\r\n<tbody>\r\n<tr>\r\n<td style="padding: 15px;"><center>\r\n<table width="550" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff">\r\n<tbody>\r\n<tr>\r\n<td align="left">\r\n<div style="border: solid 1px #d9d9d9;">\r\n<table id="header" style="line-height: 1.6; font-size: 12px; font-family: Helvetica, Arial, sans-serif; border: solid 1px #FFFFFF; color: #444;" border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#ffffff">\r\n<tbody>\r\n<tr>\r\n<td style="color: #ffffff;" colspan="2" valign="bottom" height="30">.</td>\r\n</tr>\r\n<tr>\r\n<td style="line-height: 32px; padding-left: 30px;" valign="baseline"><span style="font-size: 32px;">eduTrac SIS</span></td>\r\n<td style="padding-right: 30px;" align="right" valign="baseline"><span style="font-size: 14px; color: #777777;">Room/Event Reservation Request</span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id="content" style="margin-top: 15px; margin-right: 30px; margin-left: 30px; color: #444; line-height: 1.6; font-size: 12px; font-family: Arial, sans-serif;" border="0" width="490" cellspacing="0" cellpadding="0" bgcolor="#ffffff">\r\n<tbody>\r\n<tr>\r\n<td style="border-top: solid 1px #d9d9d9;" colspan="2">\r\n<div style="padding: 15px 0;">Below are the details of a new room request.</div>\r\n<div style="padding: 15px 0;"><strong>Name:</strong> #name#<br /><br /><strong>Email:</strong> #email#<br /><br /><strong>Event Title:</strong> #title#<br /><strong>Description:</strong> #description#<br /><strong>Request Type:</strong> #request_type#<br /><strong>Category:</strong> #category#<br /><strong>Room#:</strong> #room#<br /><strong>Start Date:</strong> #firstday#<br /><strong>End Date:</strong> #lastday#<br /><strong>Start Time:</strong> #sTime#<br /><strong>End Time:</strong> #eTime#<br /><strong>Repeat?:</strong> #repeat#<br /><strong>Occurrence:</strong> #occurrence#<br /><br /><br />\r\n<h3>Legend</h3>\r\n<ul>\r\n<li>Repeat - 1 means yes it is an event that is repeated</li>\r\n<li>Occurrence - 1 = repeats everyday, 7 = repeats weekly, 14 = repeats biweekly</li>\r\n</ul>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id="footer" style="line-height: 1.5; font-size: 12px; font-family: Arial, sans-serif; margin-right: 30px; margin-left: 30px;" border="0" width="490" cellspacing="0" cellpadding="0" bgcolor="#ffffff">\r\n<tbody>\r\n<tr style="font-size: 11px; color: #999999;">\r\n<td style="border-top: solid 1px #d9d9d9;" colspan="2">\r\n<div style="padding-top: 15px; padding-bottom: 1px;">Powered by eduTrac SIS</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="color: #ffffff;" colspan="2" height="15">.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</center></td>\r\n</tr>\r\n</tbody>\r\n</table>');

INSERT INTO `options_meta` VALUES(16, 'room_booking_confirmation_text', '<p>&nbsp;</p>\r\n<table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#F4F3F4">\r\n<tbody>\r\n<tr>\r\n<td style="padding: 15px;"><center>\r\n<table width="550" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff">\r\n<tbody>\r\n<tr>\r\n<td align="left">\r\n<div style="border: solid 1px #d9d9d9;">\r\n<table id="header" style="line-height: 1.6; font-size: 12px; font-family: Helvetica, Arial, sans-serif; border: solid 1px #FFFFFF; color: #444;" border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#ffffff">\r\n<tbody>\r\n<tr>\r\n<td style="color: #ffffff;" colspan="2" valign="bottom" height="30">.</td>\r\n</tr>\r\n<tr>\r\n<td style="line-height: 32px; padding-left: 30px;" valign="baseline"><span style="font-size: 32px;">eduTrac SIS</span></td>\r\n<td style="padding-right: 30px;" align="right" valign="baseline"><span style="font-size: 14px; color: #777777;">Room/Event&nbsp;Booking&nbsp;Confirmation</span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id="content" style="margin-top: 15px; margin-right: 30px; margin-left: 30px; color: #444; line-height: 1.6; font-size: 12px; font-family: Arial, sans-serif;" border="0" width="490" cellspacing="0" cellpadding="0" bgcolor="#ffffff">\r\n<tbody>\r\n<tr>\r\n<td style="border-top: solid 1px #d9d9d9;" colspan="2">\r\n<div style="padding: 15px 0;">Your room request or event request entitled <strong>#title#</strong> has been booked. If you have any questions or concerns, please email our office at <a href="mailto:request@bdci.edu">request@bdci.edu</a></div>\r\n<div style="padding: 15px 0;">Sincerely,<br />Room Scheduler</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id="footer" style="line-height: 1.5; font-size: 12px; font-family: Arial, sans-serif; margin-right: 30px; margin-left: 30px;" border="0" width="490" cellspacing="0" cellpadding="0" bgcolor="#ffffff">\r\n<tbody>\r\n<tr style="font-size: 11px; color: #999999;">\r\n<td style="border-top: solid 1px #d9d9d9;" colspan="2">\r\n<div style="padding-top: 15px; padding-bottom: 1px;">Powered by eduTrac SIS</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="color: #ffffff;" colspan="2" height="15">.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</center></td>\r\n</tr>\r\n</tbody>\r\n</table>');

INSERT INTO `options_meta` VALUES(17, 'myet_welcome_message', '<p>Welcome to the <em>my</em>eduTrac campus portal. The <em>my</em>eduTrac campus portal&nbsp;is your personalized campus web site at Eastbound University.</p>\r\n<p>If you are a prospective student who is interested in applying to the college, checkout the <a href="pages/?pg=admissions">admissions</a>&nbsp;page for more information.</p>');

INSERT INTO `options_meta` VALUES(18, 'contact_phone', '888.888.8888');

INSERT INTO `options_meta` VALUES(19, 'contact_email', 'contact@colegio.edu');

INSERT INTO `options_meta` VALUES(20, 'mailing_address', '10 Eliot Street, Suite 2\r\nSomerville, MA 02140');

INSERT INTO `options_meta` VALUES(21, 'enable_myet_portal', '0');

INSERT INTO `options_meta` VALUES(22, 'screen_caching', '1');

INSERT INTO `options_meta` VALUES(23, 'db_caching', '1');

INSERT INTO `options_meta` VALUES(24, 'admissions_email', 'admissions@colegio.edu');

INSERT INTO `options_meta` VALUES(25, 'coa_form_text', '<p>Dear Admin,</p>\r\n<p>#name# has submitted a change of address. Please see below for details.</p>\r\n<p><strong>ID:</strong> #id#</p>\r\n<p><strong>Address1:</strong> #address1#</p>\r\n<p><strong>Address2:</strong> #address2#</p>\r\n<p><strong>City:</strong> #city#</p>\r\n<p><strong>State:</strong> #state#</p>\r\n<p><strong>Zip:</strong> #zip#</p>\r\n<p><strong>Country:</strong> #country#</p>\r\n<p><strong>Phone:</strong> #phone#</p>\r\n<p><strong>Email:</strong> #email#</p>\r\n<p>&nbsp;</p>\r\n<p>----<br /><em>This is a system generated email.</em></p>');

INSERT INTO `options_meta` VALUES(26, 'enable_myet_appl_form', '0');

INSERT INTO `options_meta` VALUES(27, 'myet_offline_message', 'Please excuse the dust. We are giving the portal a new facelift. Please try back again in an hour.\r\n\r\nSincerely,\r\nIT Department');

INSERT INTO `options_meta` VALUES(28, 'curl', '1');

INSERT INTO `options_meta` VALUES(29, 'system_timezone', 'America/New_York');

INSERT INTO `options_meta` VALUES(30, 'number_of_courses', '3');

INSERT INTO `options_meta` VALUES(31, 'account_balance', '');

INSERT INTO `options_meta` VALUES(32, 'reg_instructions', '');

INSERT INTO `options_meta` VALUES(33, 'et_core_locale', 'en_US');

INSERT INTO `options_meta` VALUES(34, 'send_acceptance_email', '0');

INSERT INTO `options_meta` VALUES(35, 'person_login_details', '<p>Dear #fname#:</p>\r\n<p>An account has just been created for you. Below are your login details.</p>\r\n<p>Username: #uname#</p>\r\n<p>Password: #password#</p>\r\n<p>ID: #id#</p>\r\n<p>Alternate ID:&nbsp;#altID#</p>\r\n<p>You may log into your account at the url below:</p>\r\n<p><a href="#url#">#url#</a></p>');

INSERT INTO `options_meta` VALUES(36, 'myet_layout', 'default');

INSERT INTO `options_meta` VALUES(37, 'open_terms', '"15/FA"');

INSERT INTO `options_meta` VALUES(38, 'elfinder_driver', 'elf_local_driver');
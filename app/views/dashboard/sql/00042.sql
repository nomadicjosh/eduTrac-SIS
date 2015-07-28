DROP TABLE IF EXISTS `cronjob`;

DROP TABLE IF EXISTS `cronlog`;

DROP TABLE IF EXISTS `et_option`;

DROP TABLE IF EXISTS `stu_comment`;

CREATE TABLE IF NOT EXISTS `stu_acct_bill` (
`ID` bigint(20) NOT NULL,
  `billID` varchar(11) NOT NULL,
  `stuID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `authCode` varchar(23) NOT NULL,
  `stu_comments` text NOT NULL,
  `staff_comments` text NOT NULL,
  `postedBy` bigint(20) NOT NULL,
  `billTimeStamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `stu_acct_bill` ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `billID` (`billID`), ADD KEY `stuID` (`stuID`), ADD KEY `termCode` (`termCode`), ADD KEY `postedBy` (`postedBy`);

ALTER TABLE `stu_acct_bill` MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `stu_acct_bill` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `stu_acct_fee` (
`ID` bigint(20) NOT NULL,
  `billID` varchar(11) NOT NULL,
  `stuID` bigint(20) NOT NULL,
  `termCode` varchar(11) NOT NULL,
  `type` varchar(11) NOT NULL,
  `description` varchar(125) NOT NULL,
  `amount` double(6,2) NOT NULL,
  `feeTimeStamp` datetime NOT NULL,
  `postedBy` bigint(20) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `stu_acct_fee` ADD PRIMARY KEY (`ID`), ADD KEY `billID` (`billID`), ADD KEY `stuID` (`stuID`), ADD KEY `termCode` (`termCode`), ADD KEY `postedBy` (`postedBy`);

ALTER TABLE `stu_acct_fee` MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `stu_acct_fee` ADD FOREIGN KEY (`billID`) REFERENCES `stu_acct_bill` (`billID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_fee` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

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

ALTER TABLE `stu_acct_pp` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

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

ALTER TABLE `stu_acct_tuition` ADD FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `department` ADD COLUMN `deptEmail` VARCHAR(180) NOT NULL AFTER `deptName`;

ALTER TABLE `department` ADD COLUMN `deptPhone` VARCHAR(20) NOT NULL AFTER `deptEmail`;

ALTER TABLE `person` ADD COLUMN `altID` VARCHAR(255) DEFAULT NULL AFTER `personID`;

ALTER TABLE `person` ADD COLUMN `LastLogin` datetime NOT NULL AFTER `approvedBy`;

ALTER TABLE `stu_course_sec` ADD COLUMN `regDate` date DEFAULT NULL AFTER `ceu`;

ALTER TABLE `stu_course_sec` ADD COLUMN `regTime` VARCHAR(10) DEFAULT NULL AFTER `regDate`;

UPDATE `stu_course_sec` SET regDate = statusDate, regTime = statusTime WHERE status IN('A','N');

ALTER TABLE `stu_program` MODIFY COLUMN `antGradDate` VARCHAR(8);

INSERT INTO stu_acct_bill (billID,stuID,termCode,stu_comments,staff_comments,postedBy,billTimeStamp)
SELECT ID,stuID,termCode,stu_comments,staff_comments,postedBy,dateTime
FROM bill
GROUP BY stuID,termCode
ORDER BY ID ASC;

ALTER TABLE `stu_acct_bill` AUTO_INCREMENT = 1;

INSERT INTO stu_acct_fee (billID,stuID,termCode,description,amount,postedBy,feeTimeStamp)
SELECT b.billID,a.stuID,a.termCode,c.courseSection,c.courseFee+c.materialFee+c.labFee,a.addedBy,a.addDate
FROM stu_acad_cred a
LEFT JOIN stu_acct_bill b ON a.termCode = b.termCode AND a.stuID = b.stuID
LEFT JOIN course_sec c ON a.courseSecID = c.courseSecID
GROUP BY a.stuID,a.courseSecID;

UPDATE `stu_acct_fee` SET `type` = 'Tuition';

ALTER TABLE `stu_acct_fee` AUTO_INCREMENT = 1;

INSERT INTO stu_acct_fee (billID,stuID,termCode,description,amount,postedBy,feeTimeStamp)
SELECT a.billID,a.stuID,a.termCode,b.name,b.amount,a.postedBy,a.dateTime
FROM student_fee a
LEFT JOIN billing_table b ON a.feeID = b.ID
GROUP BY a.stuID,a.termCode,a.billID,a.feeID;

UPDATE `stu_acct_fee` SET `type` = 'Fee' WHERE `type` = '';

ALTER TABLE `stu_acct_fee` AUTO_INCREMENT = 1;

INSERT INTO stu_acct_tuition (stuID,termCode,total,postedBy,tuitionTimeStamp)
SELECT a.stuID,a.termCode,SUM(b.courseFee+b.materialFee+b.labFee),1,NOW()
FROM stu_acad_cred a
LEFT JOIN course_sec b ON a.courseSecID = b.courseSecID
GROUP BY a.stuID,termCode;

ALTER TABLE `stu_acct_tuition` AUTO_INCREMENT = 1;

UPDATE `stu_acct_bill` SET `billID` = CONCAT(CHAR( FLOOR(65 + (RAND() * 25))),CHAR( FLOOR(65 + (RAND() * 25))),CHAR( FLOOR(65 + (RAND() * 25))),LPAD(billID,8,'0'));

ALTER TABLE `stu_acct_bill` AUTO_INCREMENT = 1;

UPDATE `options_meta` SET meta_value = '00043' WHERE meta_key = 'dbversion';
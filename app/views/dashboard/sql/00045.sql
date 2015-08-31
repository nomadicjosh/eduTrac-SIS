ALTER TABLE `stu_acct_bill` ADD COLUMN `billingDate` date NOT NULL AFTER `postedBy`;

ALTER TABLE `stu_acct_bill` ADD COLUMN `balanceDue` enum('1','0') NOT NULL DEFAULT '1' AFTER `staff_comments`;

ALTER TABLE `stu_acct_fee` ADD COLUMN `feeDate` date NOT NULL AFTER `amount`;

UPDATE `stu_acct_bill` SET `billingDate` = `billTimeStamp`;

UPDATE `stu_acct_fee` SET `feeDate` = `feeTimeStamp`;

ALTER TABLE `payment` DROP FOREIGN KEY payment_ibfk_1;

ALTER TABLE `payment` DROP FOREIGN KEY payment_ibfk_2;

ALTER TABLE `payment` DROP FOREIGN KEY payment_ibfk_3;

ALTER TABLE `payment` AUTO_INCREMENT = 1;

ALTER TABLE `refund` DROP FOREIGN KEY refund_ibfk_1;

ALTER TABLE `refund` DROP FOREIGN KEY refund_ibfk_2;

ALTER TABLE `refund` DROP FOREIGN KEY refund_ibfk_3;

ALTER TABLE `refund` AUTO_INCREMENT = 1;

ALTER TABLE `stu_acct_fee` DROP FOREIGN KEY stu_acct_fee_ibfk_1;

ALTER TABLE `stu_acct_fee` DROP FOREIGN KEY stu_acct_fee_ibfk_2;

ALTER TABLE `stu_acct_fee` DROP FOREIGN KEY stu_acct_fee_ibfk_3;

ALTER TABLE `stu_acct_fee` DROP FOREIGN KEY stu_acct_fee_ibfk_4;

ALTER TABLE `stu_acct_fee` AUTO_INCREMENT = 1;

ALTER TABLE `stu_acct_tuition` DROP FOREIGN KEY stu_acct_tuition_ibfk_1;

ALTER TABLE `stu_acct_tuition` DROP FOREIGN KEY stu_acct_tuition_ibfk_2;

ALTER TABLE `stu_acct_tuition` DROP FOREIGN KEY stu_acct_tuition_ibfk_3;

ALTER TABLE `stu_acct_tuition` AUTO_INCREMENT = 1;

ALTER TABLE `payment` ADD CONSTRAINT pay_fk_stuID FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `payment` ADD CONSTRAINT pay_fk_termCode FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `payment` ADD CONSTRAINT pay_fk_postedBy FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `refund` ADD CONSTRAINT ref_fk_stuID FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `refund` ADD CONSTRAINT ref_fk_termCode FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `refund` ADD CONSTRAINT ref_fk_postedBy FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_fee` ADD CONSTRAINT saf_fk_billID FOREIGN KEY (`billID`) REFERENCES `stu_acct_bill` (`billID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_fee` ADD CONSTRAINT saf_fk_stuID FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_fee` ADD CONSTRAINT saf_fk_termCode FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_fee` ADD CONSTRAINT saf_fk_postedBy FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_tuition` ADD CONSTRAINT sat_fk_stuID FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_tuition` ADD CONSTRAINT sat_fk_termCode FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON UPDATE CASCADE;

ALTER TABLE `stu_acct_tuition` ADD CONSTRAINT sat_fk_postedBy FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE;

INSERT INTO `permission` VALUES('', 'execute_saved_query', 'Execute Saved Query');

INSERT INTO `options_meta` VALUES('', 'elfinder_driver', 'elf_local_driver');

UPDATE `options_meta` SET meta_value = '00046' WHERE meta_key = 'dbversion';
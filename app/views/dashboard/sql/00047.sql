ALTER TABLE `stu_acct_bill` CHANGE COLUMN `LastUpdate` `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

UPDATE `options_meta` SET meta_value = '00048' WHERE meta_key = 'dbversion';
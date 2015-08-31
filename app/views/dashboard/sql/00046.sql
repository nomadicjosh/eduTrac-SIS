ALTER TABLE `saved_query` ADD COLUMN `shared` text AFTER `purgeQuery`;

ALTER TABLE `stu_acct_bill` ADD COLUMN `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `billTimeStamp`;

UPDATE `options_meta` SET meta_value = '00047' WHERE meta_key = 'dbversion';
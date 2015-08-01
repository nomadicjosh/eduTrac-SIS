ALTER TABLE `payment` ADD COLUMN `paymentDate` date NOT NULL AFTER `comment`;

ALTER TABLE `refund` ADD COLUMN `refundDate` date NOT NULL AFTER `comment`;

UPDATE `payment` SET `paymentDate` = `dateTime`;

UPDATE `refund` SET `refundDate` = `dateTime`;

ALTER TABLE `payment` CHANGE `dateTime` `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `refund` CHANGE `dateTime` `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `payment` AUTO_INCREMENT = 1;

ALTER TABLE `refund` AUTO_INCREMENT = 1;

UPDATE `options_meta` SET meta_value = '00044' WHERE meta_key = 'dbversion';
ALTER TABLE `student` ADD COLUMN `tags` VARCHAR(255) NOT NULL AFTER `status`;

UPDATE `options_meta` SET meta_value = '00045' WHERE meta_key = 'dbversion';
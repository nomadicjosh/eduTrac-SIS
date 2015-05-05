ALTER TABLE `assignment` ADD COLUMN `courseSecID` bigint(20) DEFAULT NULL AFTER `assignID`;

UPDATE assignment a 
        INNER JOIN course_sec b 
             ON a.courseSecCode = b.courseSecCode AND a.termCode = b.termCode
SET a.courseSecID = b.courseSecID 
WHERE a.courseSecID IS NULL;

ALTER TABLE `attendance` ADD COLUMN `courseSecID` bigint(20) DEFAULT NULL AFTER `id`;

UPDATE attendance a 
        INNER JOIN course_sec b 
             ON a.courseSecCode = b.courseSecCode AND a.termCode = b.termCode
SET a.courseSecID = b.courseSecID 
WHERE a.courseSecID IS NULL;

ALTER TABLE `gradebook` ADD COLUMN `courseSecID` bigint(20) DEFAULT NULL AFTER `gbID`;

UPDATE gradebook a 
        INNER JOIN course_sec b 
             ON a.courseSecCode = b.courseSecCode AND a.termCode = b.termCode
SET a.courseSecID = b.courseSecID 
WHERE a.courseSecID IS NULL;

ALTER TABLE `institution_attended` CHANGE `GPA` `GPA` double(6,4) DEFAULT NULL;

INSERT INTO `permission` VALUES('', 'access_gradebook', 'Access Gradebook');

UPDATE `options_meta` SET meta_value = '00040' WHERE meta_key = 'dbversion';
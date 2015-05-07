DROP TABLE IF EXISTS `screen`;

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

INSERT INTO `screen` VALUES(41, 'INST', 'Institution', 'inst/');

INSERT INTO `screen` VALUES(42, 'AINST', 'New Institution', 'inst/add/');

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

UPDATE `options_meta` SET meta_value = '00041' WHERE meta_key = 'dbversion';
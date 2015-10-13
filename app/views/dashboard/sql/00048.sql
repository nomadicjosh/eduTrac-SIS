DROP TABLE IF EXISTS bill;

DROP TABLE IF EXISTS helpdesk_emailtemp;

DROP TABLE IF EXISTS helpdesk_message;

DROP TABLE IF EXISTS helpdesk_ticket;

DROP TABLE IF EXISTS nslc_hold_file;

DROP TABLE IF EXISTS nslc_setup;

DROP TABLE IF EXISTS parent;

DROP TABLE IF EXISTS parent_child;

DROP TABLE IF EXISTS progress_report;

DROP TABLE IF EXISTS reservation;

DROP TABLE IF EXISTS reservation_category;

DROP TABLE IF EXISTS student_fee;

UPDATE `options_meta` SET meta_value = '00049' WHERE meta_key = 'dbversion';
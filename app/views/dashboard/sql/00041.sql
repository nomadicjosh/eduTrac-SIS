UPDATE `options_meta` SET meta_key = 'api_key' WHERE meta_key = 'auth_token';

UPDATE `options_meta` SET meta_value = 'http://www.edutracsis.com/' WHERE meta_value = 'http://www.edutracerp.com/';

UPDATE `options_meta` SET meta_value = '00042' WHERE meta_key = 'dbversion';
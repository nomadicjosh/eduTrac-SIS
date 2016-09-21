<?php
/**
 * Phinx config file.
 *
 * @license GPLv3
 *         
 * @since 6.2.10
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */

date_default_timezone_set('UTC');

defined('DB_HOST') or define('DB_HOST', '');
defined('DB_NAME') or define('DB_NAME', '');
defined('DB_USER') or define('DB_USER', '');
defined('DB_PASS') or define('DB_PASS', '');
defined('DB_PORT') or define('DB_PORT', 3306);
    
return array(
    "paths" => array(
        "migrations" => "app/src/vendor/Phinx/migrations"
    ),
    "environments" => array(
        "default_migration_table" => "migrations",
        "default_database" => "production",
        "production" => array(
            "adapter" => "mysql",
            "host" => DB_HOST,
            "name" => DB_NAME,
            "user" => DB_USER,
            "pass" => DB_PASS,
            "charset" => 'utf8',
            //"port" => DB_PORT
        )
    )
);
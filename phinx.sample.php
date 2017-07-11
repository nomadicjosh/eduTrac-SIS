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

/**
 * If you are installing on a development server such
 * as WAMP, MAMP, XAMPP or AMPPS, you might need to
 * set DB_HOST to 127.0.0.1 instead of localhost.
 */
defined('DB_HOST') or define('DB_HOST', ''); // MySQL server host.
defined('DB_NAME') or define('DB_NAME', ''); // Database name.
defined('DB_USER') or define('DB_USER', ''); // Database username.
defined('DB_PASS') or define('DB_PASS', ''); // Database password.
defined('DB_PORT') or define('DB_PORT', 3306); // Database port.
    
return [
    "paths" => [
        "migrations" => "app/migrations"
    ],
    "environments" => [
        "default_migration_table" => "migrations",
        "default_database" => "production",
        "production" => [
            "adapter" => "mysql",
            "host" => DB_HOST,
            "name" => DB_NAME,
            "user" => DB_USER,
            "pass" => DB_PASS,
            "charset" => 'utf8mb4',
            "collation" => 'utf8mb4_unicode_ci',
            "port" => DB_PORT
        ]
    ]
];
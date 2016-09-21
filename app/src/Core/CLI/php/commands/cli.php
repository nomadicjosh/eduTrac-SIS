<?php
/**
 * Get information about ETSIS-CLI itself.
 *
 * ## EXAMPLES
 *
 *     # Display cli version number.
 *     $ ./etsis cli version
 * 
 *     # Display CLI environment.
 *     $ ./etsis cli info 
 *
 */
ETSIS_CLI::add_command('cli', 'CLI_Command');

class CLI_Command extends ETSIS_CLI_Command
{

    /**
     * Displays CLI version.
     * 
     * ## EXAMPLES
     *
     *     # Display cli version number.
     *     $ ./etsis cli version 
     */
    function version()
    {
        ETSIS_CLI::line('ETSIS-CLI ' . ETSIS_CLI_VERSION);
    }

    /**
     * Print various data about the CLI environment.
     *
     * ## EXAMPLES
     *
     *     # Display CLI environment.
     *     $ ./etsis cli info 
     */
    function info()
    {
        $php_bin = defined('PHP_BINARY') ? PHP_BINARY : getenv('ETSIS_CLI_PHP_USED');

        ETSIS_CLI::line("PHP binary:\t" . $php_bin);
        ETSIS_CLI::line("PHP version:\t" . PHP_VERSION);
        ETSIS_CLI::line("php.ini used:\t" . get_cfg_var('cfg_file_path'));
        ETSIS_CLI::line("PHP Modules:\t" . shell_exec('php -m'));
        ETSIS_CLI::line("MySQL Version:\t" . exec('mysql -V'));
        ETSIS_CLI::line("ETSIS-CLI root dir:\t" . ETSIS_CLI_ROOT);
        ETSIS_CLI::line("ETSIS-CLI version:\t" . ETSIS_CLI_VERSION);
    }
}

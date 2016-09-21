<?php
/**
 * Install, Update, Manage eduTrac SIS.
 *
 * ## EXAMPLES
 *
 *     # Display release and copyright.
 *     $ ./etsis core info 
 * 
 *     # Installs eduTrac SIS.
 *     $ ./etsis core install
 *
 *     # Rollback to previous migration.
 *     $ ./etsis core rollback
 * 
 *     # Prints list of all migrations with current status.
 *     $ ./etsis core status  
 * 
 */
ETSIS_CLI::add_command('core', 'Core_Command');

class Core_Command extends ETSIS_CLI_Command
{

    /**
     * Display various info about eduTrac SIS.
     * 
     * ## EXAMPLES
     *
     *     # Display release and copyright.
     *     $ ./etsis core info
     */
    public function info()
    {
        ETSIS_CLI::line('eduTrac SIS ' . file_get_contents('RELEASE'));
        ETSIS_CLI::line('Copyright (c) 2013-2016 Joshua Parker <joshmac3@icloud.com>');
    }

    /**
     * Populate database and create config file.
     * 
     * ## EXAMPLES
     *
     *     # Populate eduTrac SIS database.
     *     $ ./etsis core install
     */
    public function install()
    {
        ETSIS_CLI::line(shell_exec('./phinx migrate -e production'));
    }

    /**
     * Rollback is used to undo a previous migration.
     * 
     * ## EXAMPLES
     *
     *     # Rollback to previous migration.
     *     $ ./etsis core rollback
     */
    public function rollback()
    {
        ETSIS_CLI::line(shell_exec('./phinx rollback -e production'));
    }

    /**
     * Use this command to determine which migrations have been run.
     * 
     * ## EXAMPLES
     *
     *     # Prints list of all migrations with current status.
     *     $ ./etsis core status
     */
    public function status()
    {
        ETSIS_CLI::line(shell_exec('./phinx status -e production'));
    }
}

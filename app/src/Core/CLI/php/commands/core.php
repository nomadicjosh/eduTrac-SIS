<?php
/**
 * Install, Update, Manage eduTrac SIS.
 *
 * ## EXAMPLES
 *
 *     # Display release and copyright.
 *     $ ./etsis core info
 *
 *     # Installs / Updates eduTrac SIS.
 *     $ ./etsis core migrate
 *
 *     # Rollback to previous migration.
 *     $ ./etsis core rollback
 *
 *     # Prints list of all migrations with current status.
 *     $ ./etsis core status
 * 
 *     # Checks if an update is available.
 *     $ ./etsis core check_update
 * 
 *     # Updates your system to the latest release.
 *     $ ./etsis core update
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
        ETSIS_CLI::line('Copyright (c) 2013-2017 Joshua Parker <joshmac3@icloud.com>');
    }

    /**
     * Populate database and create config file.
     * 
     * ## EXAMPLES
     *
     *     # Populate eduTrac SIS database.
     *     $ ./etsis core install
     * 
     * @deprecated since release 6.2.11
     */
    public function install()
    {
        ETSIS_CLI::line('This command has been removed and replaced with: %G./etsis core migrate%n');
    }

    /**
     * Populate database and create config file on first
     * run. All others will be updates.
     *
     * ## EXAMPLES
     *
     *     # Populate eduTrac SIS database.
     *     $ ./etsis core migrate
     */
    public function migrate()
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

    /**
     * Use this command to check for a new update.
     * 
     * ## EXAMPLES  
     * 
     *      #Displays need for update or not.
     *      $ ./etsis core check_update
     */
    public function check_update()
    {
        if (version_compare(PHP_VERSION, '5.6', '<')) {
            ETSIS_CLI::line('To upgrade to release 6.3+, your server must be using a minimum of %GPHP 5.6%n.');
            return false;
        }

        $update = new \VisualAppeal\AutoUpdate('app/tmp', 'app/tmp', 1800);
        $update->setCurrentVersion(trim(file_get_contents('RELEASE')));
        $update->setUpdateUrl('http://etsis.s3.amazonaws.com/core/1.1/update-check');

        // Optional:
        $update->addLogHandler(new Monolog\Handler\StreamHandler('app/tmp/logs/core-update.' . date('m-d-Y') . '.txt'));
        $update->setCache(new Desarrolla2\Cache\Adapter\File('app/tmp/cache'), 3600);
        if ($update->checkUpdate() !== false) {
            if ($update->newVersionAvailable()) {
                ETSIS_CLI::line('Release %G' . $update->getLatestVersion() . '%n is available.');
                ETSIS_CLI::line('Do a backup before upgrading: https://www.edutracsis.com/manual/edutrac-sis-backups/');
            } else {
                ETSIS_CLI::line('%GSuccess:%n eduTrac SIS is at the latest release.');
            }
        }
    }

    /**
     * Use this command to update installation.
     * 
     * ## EXAMPLES  
     * 
     *      #Updates a current installation.
     *      $ ./etsis core update
     */
    public function update($args, $assoc_args)
    {
        if (version_compare(PHP_VERSION, '5.6', '<')) {
            ETSIS_CLI::line('To upgrade to release 6.3+, your server must be using a minimum of %GPHP 5.6%n.');
            return false;
        }

        if (!isset($assoc_args['release'])) {
            $release = ETSIS_CLI::getCurrentRelease();
        } else {
            $release = $assoc_args['release'];
        }

        $zip = new ZipArchive;
        $file = 'http://etsis.s3.amazonaws.com/core/1.1/release/' . $release . '.zip';
        if (version_compare(trim(file_get_contents('RELEASE')), $release, '<')) {
            if (ETSIS_CLI::checkExternalFile($file) == 200) {
                //Download file to the server
                opt_notify(new \cli\progress\Bar('Downloading ', 1000000));
                ETSIS_CLI::getDownload($release . '.zip', $file);
                //Unzip the file to update
                opt_notify(new \cli\progress\Bar('Unzipping ', 1000000));
                $x = $zip->open($release . '.zip');
                if ($x === true) {
                    //Extract file in root.
                    $zip->extractTo(realpath(__DIR__ . '/../../../../../../'));
                    $zip->close();
                    //Remove download after completion.
                    unlink($release . '.zip');
                }
                ETSIS_CLI::line('Core upgrade complete.');
                ETSIS_CLI::line('Run the command %G./etsis db migrate%n to check for database updates.');
            } else {
                ETSIS_CLI::line('Update server cannot be reached. Please try again later.');
            }
        } else {
            ETSIS_CLI::line('No Update');
        }
    }
}

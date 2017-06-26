<?php namespace app\src\Core;

use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Event Logger for Errors and Activity.
 *
 * @license GPLv3
 *         
 * @since 6.2.11
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Logger
{

    /**
     * Application object.
     * @var type 
     */
    public $app;

    public function __construct()
    {
        $this->app = \Liten\Liten::getInstance();
    }

    /**
     * Writes a log to the log table in the database.
     * 
     * @since 1.0.0
     */
    public function writeLog($action, $process, $record, $uname)
    {
        $create = date("Y-m-d H:i:s", time());
        $current_date = strtotime($create);
        /* 20 days after creation date */
        $expire = date("Y-m-d H:i:s", $current_date+=1728000);

        $expires_at = $this->app->hook->apply_filter('activity_log_expires', $expire);
        try {
            $log = $this->app->db->activity_log();
            $log->action = $action;
            $log->process = $process;
            $log->record = $record;
            $log->uname = $uname;
            $log->created_at = $create;
            $log->expires_at = $expires_at;

            $log->save();
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }
    }

    /**
     * Purges audit trail logs that are older than 30 days old.
     * 
     * @since 1.0.0
     */
    public function purgeActivityLog()
    {
        try {
            $this->app->db->query("DELETE FROM activity_log WHERE expires_at <= ?", [ date('Y-m-d H:i:s', time())]);
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }
    }

    /**
     * Purges system error logs that are older than 30 days old.
     * 
     * @since 1.0.0
     */
    public function purgeErrorLog()
    {
        $logs = glob(APP_PATH . 'tmp/logs/*.txt');
        if (is_array($logs)) {
            foreach ($logs as $log) {
                $filelastmodified = file_mod_time($log);
                if ((time() - $filelastmodified) >= 30 * 24 * 3600 && is_file($log)) {
                    unlink($log);
                }
            }
        }
    }

    public function logError($type, $string, $file, $line)
    {
        $date = new \DateTime();
        try {
            $log = $this->app->db->error();
            $log->time = $date->getTimestamp();
            $log->type = (int) $type;
            $log->string = (string) $string;
            $log->file = (string) $file;
            $log->line = (int) $line;

            $log->save();
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }
    }

    public function error_constant_to_name($value)
    {
        $values = array(
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            E_ALL => 'E_ALL'
        );

        return $values[$value];
    }
}

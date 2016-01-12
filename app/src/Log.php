<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Event Logger Library
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
class Log
{

    protected $_app;

    public function __construct()
    {
        $this->_app = \Liten\Liten::getInstance();
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

        $log = $this->_app->db->activity_log();
        $log->action = $action;
        $log->process = $process;
        $log->record = $record;
        $log->uname = $uname;
        $log->created_at = $create;
        $log->expires_at = $expire;

        $log->save();
    }

    /**
     * Purges audit trail logs that are older than 30 days old.
     * 
     * @since 1.0.0
     */
    public function purgeLog()
    {
        $this->_app->db->query("DELETE FROM activity_log WHERE expires_at <= ?", [ date('Y-m-d H:i:s', time()) ]);
    }

    /**
     * Purges system error logs that are older than 30 days old.
     * 
     * @since 1.0.0
     */
    public function purgeErrLog()
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

        $log = $this->_app->db->error();
        $log->time = $date->getTimestamp();
        $log->type = (int) $type;
        $log->string = (string) $string;
        $log->file = (string) $file;
        $log->line = (int) $line;

        $log->save();
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

    public function setLog($action, $process, $record, $uname)
    {
        return $this->writeLog($action, $process, $record, $uname);
    }

    public function setError($type, $string, $file, $line)
    {
        return $this->logError($type, $string, $file, $line);
    }
}

<?php namespace app\src\Core\NodeQ\Helpers;

use app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use Cascade\Cascade;

/**
 * Data managing class
 * 
 * @since 6.2.11
 */
class Migrations
{

    public static function dispense($table)
    {
        if (!Validate::table($table)->exists()) {
            return self::$table();
        }
        return true;
    }

    public static function reset_password()
    {
        try {
            Node::create('reset_password', [
                'personid' => 'integer',
                'uname' => 'string',
                'email' => 'string',
                'name' => 'string',
                'fname' => 'string',
                'lname' => 'string',
                'password' => 'string',
                'sent' => 'integer'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'reset_password']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'reset_password']);
        }
    }

    public static function csv_email()
    {
        try {
            Node::create('csv_email', [
                'recipient' => 'string',
                'message' => 'string',
                'subject' => 'string',
                'filename' => 'string',
                'sent' => 'integer'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'csv_email']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'csv_email']);
        }
    }

    public static function login_details()
    {
        try {
            Node::create('login_details', [
                'personid' => 'integer',
                'uname' => 'string',
                'email' => 'string',
                'fname' => 'string',
                'lname' => 'string',
                'password' => 'string',
                'altid' => 'string',
                'sent' => 'integer'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'login_details']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'login_details']);
        }
    }

    public static function change_address()
    {
        try {
            Node::create('change_address', [
                'personid' => 'integer',
                'uname' => 'string',
                'fname' => 'string',
                'lname' => 'string',
                'email' => 'string',
                'address1' => 'string',
                'address2' => 'string',
                'city' => 'string',
                'state' => 'string',
                'zip' => 'string',
                'country' => 'string',
                'phone' => 'string',
                'sent' => 'integer'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'change_address']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'change_address']);
        }
    }

    public static function cronjob_setting()
    {
        try {
            Node::create('cronjob_setting', [
                'cronjobpassword' => 'string',
                'timeout' => 'integer'
            ]);

            $q = Node::table('cronjob_setting');
            $q->cronjobpassword = (string) 'changeme';
            $q->timeout = (int) 30;
            $q->save();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'cronjob_setting']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'cronjob_setting']);
        }
    }

    public static function cronjob_handler()
    {
        try {
            $url = get_base_url();
            Node::create('cronjob_handler', [
                'name' => 'string',
                'url' => 'string',
                'time' => 'string',
                'each' => 'integer',
                'eachtime' => 'string',
                'lastrun' => 'string',
                'running' => 'boolean',
                'runned' => 'integer',
                'status' => 'integer'
            ]);

            $q = Node::table('cronjob_handler');
            $q->name = (string) 'Purge Activity Log';
            $q->url = (string) $url . 'cron/purgeActivityLog/';
            $q->time = (string) '';
            $q->each = (int) 3600;
            $q->eachtime = (string) '';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Update Student Terms';
            $q->url = (string) $url . 'cron/updateSTTR/';
            $q->time = (string) '';
            $q->each = (int) 604800;
            $q->eachtime = (string) '00:00';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Update Student Academic Levels';
            $q->url = (string) $url . 'cron/updateSTAL/';
            $q->time = (string) '';
            $q->each = (int) 604800;
            $q->eachtime = (string) '00:00';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Run Email Queue';
            $q->url = (string) $url . 'cron/runEmailQueue/';
            $q->time = (string) '';
            $q->each = (int) 300;
            $q->eachtime = (string) '';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Run Bounce Handler';
            $q->url = (string) $url . 'cron/runBounceHandler/';
            $q->time = (string) '';
            $q->each = (int) 86400;
            $q->eachtime = (string) '';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Run Graduation';
            $q->url = (string) $url . 'cron/runGraduation/';
            $q->time = (string) '';
            $q->each = (int) 3600;
            $q->eachtime = (string) '';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Purge Error Log';
            $q->url = (string) $url . 'cron/purgeErrorLog/';
            $q->time = (string) '';
            $q->each = (int) 1800;
            $q->eachtime = (string) '';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Purge Saved Queries';
            $q->url = (string) $url . 'cron/purgeSavedQuery/';
            $q->time = (string) '';
            $q->each = (int) 2629743;
            $q->eachtime = (string) '';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Check Student Balance';
            $q->url = (string) $url . 'cron/checkStuBalance/';
            $q->time = (string) '';
            $q->each = (int) 300;
            $q->eachtime = (string) '';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Run NodeQ';
            $q->url = (string) $url . 'cron/runNodeQ/';
            $q->time = (string) '';
            $q->each = (int) 300;
            $q->eachtime = (string) '';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Backup Database';
            $q->url = (string) $url . 'cron/runDBBackup/';
            $q->time = (string) '';
            $q->each = (int) 86400;
            $q->eachtime = (string) '';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Backup System';
            $q->url = (string) $url . 'cron/runSiteBackup/';
            $q->time = (string) '';
            $q->each = (int) 86400;
            $q->eachtime = (string) '';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();

            $q->name = 'Academic Level Standing';
            $q->url = (string) $url . 'cron/runALST/';
            $q->time = (string) '';
            $q->each = (int) 604800;
            $q->eachtime = (string) '';
            $q->lastrun = (string) '';
            $q->running = (boolean) false;
            $q->runned = (int) 0;
            $q->status = (int) 1;
            $q->save();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'cronjob_handler']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'cronjob_handler']);
        }
    }

    public static function rlde()
    {
        try {
            Node::create('rlde', [
                'id' => 'integer',
                'description' => 'string',
                'code' => 'string',
                'dept' => 'string',
                'file' => 'string',
                'comment' => 'string',
                'rule' => 'string'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'rlde']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'rlde']);
        }
    }

    public static function alst()
    {
        try {
            Node::create('alst', [
                'id' => 'integer',
                "rid" => "integer",
                "aid" => "integer",
                "rule" => "string",
                "value" => "string",
                "level" => "string"
            ]);
            \app\src\Core\NodeQ\Relation::table('alst')->belongsTo('rlde')->localKey('rule')->foreignKey('code')->setRelation();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'alst']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'alst']);
        }
    }

    public static function stld()
    {
        try {
            Node::create('stld', [
                'id' => 'integer',
                "rid" => "integer",
                "aid" => "integer",
                "rule" => "string",
                "value" => "string",
                "level" => "string"
            ]);
            \app\src\Core\NodeQ\Relation::table('stld')->belongsTo('rlde')->localKey('rule')->foreignKey('code')->setRelation();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'stld']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'stld']);
        }
    }

    public static function clvr()
    {
        try {
            Node::create('clvr', [
                'id' => 'integer',
                "rid" => "integer",
                "aid" => "integer",
                "rule" => "string",
                "value" => "string",
                "level" => "string"
            ]);
            \app\src\Core\NodeQ\Relation::table('clvr')->belongsTo('rlde')->localKey('rule')->foreignKey('code')->setRelation();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'clvr']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'clvr']);
        }
    }

    public static function rrsr()
    {
        try {
            Node::create('rrsr', [
                "rid" => "integer",
                "rule" => "string",
                "value" => "string"
            ]);
            \app\src\Core\NodeQ\Relation::table('rrsr')->belongsTo('rlde')->localKey('rule')->foreignKey('code')->setRelation();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'rrsr']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'rrsr']);
        }
    }

    public static function acceptance_letter()
    {
        try {
            Node::create('acceptance_letter', [
                'personid' => 'integer',
                'uname' => 'string',
                'fname' => 'string',
                'lname' => 'string',
                'name' => 'string',
                'email' => 'string',
                'sacp' => 'string',
                'acadlevel' => 'string',
                'degree' => 'string',
                'startterm' => 'string',
                'sent' => 'integer'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'acceptance_letter']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'acceptance_letter']);
        }
    }

    public static function sms()
    {
        try {
            Node::create('sms', [
                'number' => 'string',
                'text' => 'string',
                'sent' => 'integer'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'sms']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'sms']);
        }
    }

    public static function php_encryption()
    {
        $key = \Defuse\Crypto\Key::createNewRandomKey();
        try {
            Node::create('php_encryption', [
                'key' => 'string',
                'created_at' => 'string'
            ]);

            $q = Node::table('php_encryption');
            $q->key = (string) $key->saveToAsciiSafeString();
            $q->created_at = (string) \Jenssegers\Date\Date::now();
            $q->save();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'php_encryption']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'php_encryption']);
        }
    }

    public static function crse_rgn()
    {
        try {
            Node::create('crse_rgn', [
                'stuid' => 'integer',
                'sections' => 'string',
                'timestamp' => 'string',
                'sent' => 'integer'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'crse_rgn']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'crse_rgn']);
        }
    }

    public static function campaign_queue()
    {
        try {
            Node::create('campaign_queue', [
                'lid' => 'integer',
                'cid' => 'integer',
                'sid' => 'integer',
                'to_email' => 'string',
                'to_name' => 'string',
                'timestamp_created' => 'string',
                'timestamp_to_send' => 'string',
                'timestamp_sent' => 'string',
                'is_unsubscribed' => 'integer',
                'timestamp_unsubscribed' => 'string',
                'is_sent' => 'string'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'campaign_queue']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'campaign_queue']);
        }
    }

    public static function campaign_bounce()
    {
        try {
            Node::create('campaign_bounce', [
                'lid' => 'integer',
                'cid' => 'integer',
                'sid' => 'integer',
                'email' => 'string',
                'msgnum' => 'integer',
                'type' => 'string',
                'rule_no' => 'string',
                'rule_cat' => 'string',
                'date_added' => 'string'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'campaign_bounce']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'campaign_bounce']);
        }
    }

    public static function student_email()
    {
        try {
            Node::create('student_email', [
                'from' => 'string',
                'to' => 'integer',
                'subject' => 'string',
                'message' => 'string',
                'attachment' => 'string',
                'sent' => 'integer'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'student_email']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'student_email']);
        }
    }
    
    public static function tasks()
    {
        try {
            Node::create('tasks', [
                'pid' => 'integer',
                'name' => 'string',
                'task_callback' => 'string',
                'action_hook' => 'string',
                'schedule' => 'string',
                'debug' => 'boolean',
                'max_runtime' => 'integer',
                'priority' => 'integer',
                'params' => 'integer',
                'executions' => 'integer',
                'lastrun' => 'string',
                'last_runtime' => 'double',
                'enabled' => 'boolean'
            ]);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'etsis_queue']);
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['NodeQ' => 'etsis_queue']);
        }
    }
}

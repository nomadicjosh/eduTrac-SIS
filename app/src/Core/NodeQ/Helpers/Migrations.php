<?php namespace app\src\Core\NodeQ\Helpers;

use \app\src\Core\NodeQ\etsis_NodeQ as Node;

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
    }

    public static function csv_email()
    {
        Node::create('csv_email', [
            'recipient' => 'string',
            'message' => 'string',
            'subject' => 'string',
            'filename' => 'string',
            'sent' => 'integer'
        ]);
    }

    public static function login_details()
    {
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
    }

    public static function change_address()
    {
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
    }

    public static function cronjob_setting()
    {
        Node::create('cronjob_setting', [
            'cronjobpassword' => 'string',
            'timeout' => 'integer'
        ]);

        $q = Node::table('cronjob_setting');
        $q->cronjobpassword = (string) 'changeme';
        $q->timeout = (int) 30;
        $q->save();
    }

    public static function cronjob_handler()
    {
        $url = get_base_url();
        Node::create('cronjob_handler', [
            'name' => 'string',
            'url' => 'string',
            'time' => 'string',
            'each' => 'integer',
            'eachtime' => 'string',
            'lastrun' => 'string',
            'running' => 'boolean',
            'runned' => 'integer'
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
        $q->save();

        $q->name = 'Create Student Terms Record';
        $q->url = (string) $url . 'cron/runStuTerms/';
        $q->time = (string) '';
        $q->each = (int) 300;
        $q->eachtime = (string) '';
        $q->lastrun = (string) '';
        $q->running = (boolean) false;
        $q->runned = (int) 0;
        $q->save();

        $q->name = 'Update Student Terms';
        $q->url = (string) $url . 'cron/updateStuTerms/';
        $q->time = (string) '';
        $q->each = (int) 300;
        $q->eachtime = (string) '';
        $q->lastrun = (string) '';
        $q->running = (boolean) false;
        $q->runned = (int) 0;
        $q->save();

        $q->name = 'Process Email Hold';
        $q->url = (string) $url . 'cron/runEmailHold/';
        $q->time = (string) '';
        $q->each = (int) 1800;
        $q->eachtime = (string) '';
        $q->lastrun = (string) '';
        $q->running = (boolean) false;
        $q->runned = (int) 0;
        $q->save();

        $q->name = 'Process Email Queue';
        $q->url = (string) $url . 'cron/runEmailQueue/';
        $q->time = (string) '';
        $q->each = (int) 1800;
        $q->eachtime = (string) '';
        $q->lastrun = (string) '';
        $q->running = (boolean) false;
        $q->runned = (int) 0;
        $q->save();

        $q->name = 'Purge Email Hold';
        $q->url = (string) $url . 'cron/purgeEmailHold/';
        $q->time = (string) '';
        $q->each = (int) 2629743;
        $q->eachtime = (string) '';
        $q->lastrun = (string) '';
        $q->running = (boolean) false;
        $q->runned = (int) 0;
        $q->save();

        $q->name = 'Purge Email Queue';
        $q->url = (string) $url . 'cron/purgeEmailQueue/';
        $q->time = (string) '';
        $q->each = (int) 2629743;
        $q->eachtime = (string) '';
        $q->lastrun = (string) '';
        $q->running = (boolean) false;
        $q->runned = (int) 0;
        $q->save();

        $q->name = 'Run Graduation';
        $q->url = (string) $url . 'cron/runGraduation/';
        $q->time = (string) '';
        $q->each = (int) 3600;
        $q->eachtime = (string) '';
        $q->lastrun = (string) '';
        $q->running = (boolean) false;
        $q->runned = (int) 0;
        $q->save();

        $q->name = 'Purge Error Log';
        $q->url = (string) $url . 'cron/purgeErrorLog/';
        $q->time = (string) '';
        $q->each = (int) 1800;
        $q->eachtime = (string) '';
        $q->lastrun = (string) '';
        $q->running = (boolean) false;
        $q->runned = (int) 0;
        $q->save();

        $q->name = 'Purge Saved Queries';
        $q->url = (string) $url . 'cron/purgeSavedQuery/';
        $q->time = (string) '';
        $q->each = (int) 2629743;
        $q->eachtime = (string) '';
        $q->lastrun = (string) '';
        $q->running = (boolean) false;
        $q->runned = (int) 0;
        $q->save();

        $q->name = 'Check Student Balance';
        $q->url = (string) $url . 'cron/checkStuBalance/';
        $q->time = (string) '';
        $q->each = (int) 300;
        $q->eachtime = (string) '';
        $q->lastrun = (string) '';
        $q->running = (boolean) false;
        $q->runned = (int) 0;
        $q->save();

        $q->name = 'Run Queued Jobs';
        $q->url = (string) $url . 'cron/runNodeQ/';
        $q->time = (string) '';
        $q->each = (int) 300;
        $q->eachtime = (string) '';
        $q->lastrun = (string) '';
        $q->running = (boolean) false;
        $q->runned = (int) 0;
        $q->save();
    }

    public static function rlde()
    {
        Node::create('rlde', [
            'id' => 'integer',
            'description' => 'string',
            'code' => 'string',
            'dept' => 'string',
            'file' => 'string',
            'comment' => 'string',
            'rule' => 'string'
        ]);
    }
    
    public static function stld()
    {
        Node::create('stld', [
            'id' => 'integer',
            "rid" => "integer",
            "aid" => "integer",
            "rule" => "string",
            "value" => "string",
            "level" => "string"
        ]);
        \app\src\Core\NodeQ\Relation::table('stld')->belongsTo('rlde')->localKey('rule')->foreignKey('code')->setRelation();
    }
    
    public static function clvr()
    {
        Node::create('clvr', [
            'id' => 'integer',
            "rid" => "integer",
            "aid" => "integer",
            "rule" => "string",
            "value" => "string",
            "level" => "string"
        ]);
        \app\src\Core\NodeQ\Relation::table('clvr')->belongsTo('rlde')->localKey('rule')->foreignKey('code')->setRelation();
    }
    
    public static function rrsr()
    {
        Node::create('rrsr', [
            "rid" => "integer",
            "rule" => "string",
            "value" => "string"
        ]);
        \app\src\Core\NodeQ\Relation::table('rrsr')->belongsTo('rlde')->localKey('rule')->foreignKey('code')->setRelation();
    }
}

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
}

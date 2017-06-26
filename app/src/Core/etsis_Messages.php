<?php namespace app\src\Core;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Flash Messages Library
 *  
 * @license GPLv3
 *         
 * @since 6.2.4
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Messages
{

    public $app;

    public function __construct()
    {
        $this->app = \Liten\Liten::getInstance();
    }

    public function init($name, $value)
    {
        /** Set the session values */
        $this->app->cookies->set($name, $value);
    }

    public function showMessage()
    {
        // get the message (they are arrays, to make multiple positive/negative messages possible)
        $success_message[] = $this->app->req->cookie['success_message'];
        $error_message[] = $this->app->req->cookie['error_message'];
        $plugin_success_message[] = $this->app->req->cookie['plugin_success_message'];
        $plugin_error_message[] = $this->app->req->cookie['plugin_error_message'];
        $pnotify[] = $this->app->req->cookie['pnotify'];

        // echo out positive messages
        if (isset($this->app->req->cookie['success_message'])) {
            foreach ($success_message as $message) {
                $this->app->cookies->remove('success_message');
                return '<section style="margin-bottom:1.5em;" class="flash_message success-panel"><div class="alerts alerts-success center"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $message . '</div></section>';
            }
        }

        // echo out negative messages
        if (isset($this->app->req->cookie['error_message'])) {
            foreach ($error_message as $message) {
                $this->app->cookies->remove('error_message');
                return '<section style="margin-bottom:1.5em;" class="flash_message error-panel"><div class="alerts alerts-error center"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $message . '</div></section>';
            }
        }

        // echo out positive plugin messages
        if (isset($this->app->req->cookie['plugin_success_message'])) {
            foreach ($plugin_success_message as $message) {
                $this->app->cookies->remove('plugin_success_message');
                return '<section style="margin-bottom:1.5em;" class="flash_message success-panel"><div class="alerts alerts-success center"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $message . '</div></section>';
            }
        }

        // echo out negative plugin messages
        if (isset($this->app->req->cookie['plugin_error_message'])) {
            foreach ($plugin_error_message as $message) {
                $this->app->cookies->remove('plugin_error_message');
                return '<section style="margin-bottom:1.5em;" class="flash_message error-panel"><div class="alerts alerts-error center"><button type="button" class="close" data-dismiss="alert">&times;</button>' . sprintf(_t('Plugin could not be activated because it triggered a <strong>fatal error</strong>. <br /><br /> %s</div></section>'), $message);
            }
        }
        
        // return a browser notification if set
        if (isset($this->app->req->cookie['pnotify'])) {
            foreach ($pnotify as $message) {
                $this->app->cookies->remove('pnotify');
                return $message;
            }
        }
    }

    public function notice($num)
    {
        $msg[200] = _t('200 - Success: Ok');
        $msg[201] = _t('201 - Success: Created');
        $msg[204] = _t('204 - Error: No Content');
        $msg[409] = _t('409 - Error: Conflict');
        return $msg[$num];
    }
}

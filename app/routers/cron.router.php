<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\Helpers\Validate as Validate;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

/**
 * Cron Router
 *
 * @license GPLv3
 *         
 * @since 5.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
$options = [
    30 => '30 seconds',
    60 => 'Minute',
    120 => '2 minutes',
    300 => '5 minutes',
    600 => '10 minutes',
    900 => '15 minutes',
    1800 => 'Half hour',
    2700 => '45 minutes',
    3600 => 'Hour',
    7200 => '2 hours',
    14400 => '4 hours',
    43200 => '12 hours',
    86400 => 'Day',
    172800 => '2 days',
    259200 => '3 days',
    604800 => 'Week',
    209600 => '2 weeks',
    2629743 => 'Month'
];

// From: https://gist.github.com/Xeoncross/1204255
$regions = [
    'Africa' => DateTimeZone::AFRICA,
    'America' => DateTimeZone::AMERICA,
    'Antarctica' => DateTimeZone::ANTARCTICA,
    'Aisa' => DateTimeZone::ASIA,
    'Atlantic' => DateTimeZone::ATLANTIC,
    'Europe' => DateTimeZone::EUROPE,
    'Indian' => DateTimeZone::INDIAN,
    'Pacific' => DateTimeZone::PACIFIC
];

$timezones = [];
foreach ($regions as $name => $mask) {
    $zones = DateTimeZone::listIdentifiers($mask);
    foreach ($zones as $timezone) {
        // Lets sample the time there right now
        $time = new \DateTime(NULL, new \DateTimeZone($timezone));

        // Us dumb Americans can't handle millitary time
        $ampm = $time->format('H') > 12 ? ' (' . $time->format('g:i a') . ')' : '';

        // Remove region name and add a sample time
        $timezones[$name][$timezone] = substr($timezone, strlen($name) + 1) . ' - ' . $time->format('H:i') . $ampm;
    }
}

$email = _etsis_email();
$emailer = _etsis_phpmailer();

$app->group('/cron', function () use($app, $emailer, $email) {

    $app->before('GET', '/', function () {
        if (!hasPermission('access_cronjob_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    try {
        if (!Validate::table('cronjob_setting')->exists()) {
            Node::dispense('cronjob_setting');
        }

        if (!Validate::table('cronjob_handler')->exists()) {
            Node::dispense('cronjob_handler');
        }
    } catch (NodeQException $e) {
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }

    $app->match('GET|POST', '/', function () use($app) {

        if ($app->req->isPost()) {
            foreach ($app->req->post['cronjobs'] as $job) {
                try {
                    Node::table('cronjob_handler')->find($job)->delete();
                } catch (NodeQException $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                } catch (Exception $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        try {

            $set = Node::table('cronjob_setting')->findAll();
            $job = Node::table('cronjob_handler')->findAll();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('cron/index', [
            'title' => 'Cronjob Handlers',
            'cron' => $job,
            'set' => $set
        ]);
    });
    
    $app->before('GET|POST', '/new/', function () {
        if (!hasPermission('access_cronjob_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/new/', function () use($app) {
        if ($app->req->isPost()) {
            if (filter_var($app->req->post['url'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                try {
                    $url = Node::table('cronjob_handler')
                        ->where('url', '=', $app->req->_post('url'))
                        ->find();
                } catch (NodeQException $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                } catch (Exception $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }

                $found = false;
                if ($url->count() > 0) {
                    $found = true;
                }

                if ($found == false) {
                    if ($app->req->_post('each') == '') {
                        _etsis_flash()->error(_t('Time setting missing, please add time settings.'));
                    } else {
                        try {
                            $cron = Node::table('cronjob_handler');
                            $cron->name = (string) $app->req->_post('name');
                            $cron->url = (string) $app->req->_post('url');
                            $cron->each = (int) $app->req->_post('each');
                            $cron->eachtime = ((isset($app->req->post['eachtime']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $app->req->post['eachtime'])) ? $app->req->post['eachtime'] : '');
                            $cron->status = (int) $app->req->post['status'];
                            $cron->save();

                            if ($cron) {
                                _etsis_flash()->success(_etsis_flash()->notice(200));
                            } else {
                                _etsis_flash()->error(_etsis_flash()->notice(409));
                            }
                        } catch (NodeQException $e) {
                            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                            _etsis_flash()->error(_etsis_flash()->notice(409));
                        } catch (Exception $e) {
                            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                            _etsis_flash()->error(_etsis_flash()->notice(409));
                        }
                    }
                } else {
                    _etsis_flash()->error(_t('Cronjob handler already exists in the system.'));
                }
            } else {
                _etsis_flash()->error(_t('Cronjob URL is wrong.'));
            }
            redirect(get_base_url() . 'cron/');
        }

        etsis_register_style('form');
        etsis_register_script('select');
        etsis_register_script('select2');

        $app->view->display('cron/new', [
            'title' => 'New Cronjob Handler',
        ]);
    });

    /**
     * Before route checks to make sure the logged in user
     * us allowed to manage options/settings.
     */
    $app->before('GET', '/(\d+)/reset/', function () {
        if (!hasPermission('access_cronjob_screen')) {
            _etsis_flash()->error(_t("You don't have permission to view the Cronjob Handler screen."), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->get('/(\d+)/reset/', function ($id) {
        try {
            $reset = Node::table('cronjob_handler')->find($id);
            $reset->runned = (int) 0;
            $reset->save();
            _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'cron' . '/');
        } catch (NodeQException $e) {
            _etsis_flash()->error($e->getMessage(), get_base_url() . 'cron' . '/');
        }
    });
    
    $app->before('GET|POST', '/setting/', function () {
        if (!hasPermission('access_cronjob_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/setting/', function () use($app) {

        if ($app->req->isPost()) {
            $good = true;

            if (strlen(trim($app->req->post['cronjobpassword'])) < 2) {
                _etsis_flash()->error(_t('Cronjobs cannot run without a password. Your cronjob password contains wrong characters, minimum of 4 letters and numbers.'));
                $good = false;
            }

            if ($good == true) {
                try {
                    $cron = Node::table('cronjob_setting')->find(1);
                    $cron->cronjobpassword = (string) $app->req->_post('cronjobpassword');
                    $cron->timeout = (isset($app->req->post['timeout']) && is_numeric($app->req->post['timeout']) ? (int) $app->req->_post('timeout') : 30);
                    $cron->save();

                    if ($cron) {
                        _etsis_flash()->success(_etsis_flash()->notice(200));
                    } else {
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    }
                } catch (NodeQException $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                } catch (Exception $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }
            }
            redirect($app->req->server['HTTP_REFERER']);
        }

        try {
            $set = Node::table('cronjob_setting')->find(1);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        etsis_register_style('form');
        etsis_register_script('select');
        etsis_register_script('select2');

        $app->view->display('cron/setting', [
            'title' => 'Cronjob Handler Settings',
            'data' => $set
        ]);
    });

    /**
     * Before route checks to make sure the logged in user
     * us allowed to manage options/settings.
     */
    $app->before('GET|POST', '/(\d+)/', function () {
        if (!hasPermission('access_cronjob_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            if (filter_var($app->req->post['url'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {

                try {
                    $cron = Node::table('cronjob_handler')->find($id);
                    $cron->name = (string) $app->req->_post('name');
                    $cron->url = (string) $app->req->_post('url');
                    $cron->each = (int) $app->req->_post('each');
                    $cron->eachtime = ((isset($app->req->post['eachtime']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $app->req->post['eachtime'])) ? $app->req->post['eachtime'] : '');
                    $cron->status = (int) $app->req->post['status'];
                    $cron->save();

                    _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                } catch (NodeQException $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                } catch (Exception $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }
            } else {
                _etsis_flash()->error(_t('Current URL is not correct; must begin with http(s):// and followed with a path.'));
            }
        }

        try {
            $sql = Node::table('cronjob_handler')->find($id);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($sql == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($sql) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_h($sql->id) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('cron/view', [
                'title' => 'View Cronjob Handler',
                'cron' => $sql
                ]
            );
        }
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/cronjob/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 404);
        exit();
    });

    $app->get('/cronjob/', function () use($app) {

        try {
            $setting = Node::table('cronjob_setting')->find(1);
            $cron = Node::table('cronjob_handler')->where('status', '=', (int) 1)->findAll();
        } catch (NodeQException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }

        if (!isset($app->req->get['password']) && !isset($argv[1])) {
            Cascade::getLogger('system_email')->alert(sprintf('CRONSTATE[401]: Unauthorized: %s', _t('No cronjob password found, use cronjob?password=<yourpassword>.')));
            exit(_t('No cronjob handler password found, use cronjob?password=<yourpassword>.'));
        } elseif (isset($app->req->get['password']) && $app->req->get['password'] != _h($setting->cronjobpassword)) {
            Cascade::getLogger('system_email')->alert(sprintf('CRONSTATE[401]: Unauthorized: %s', _t('Invalid $app->req->get password')));
            exit(_t('Invalid $app->req->get password'));
        } elseif (_h($setting->cronjobpassword) == 'changeme') {
            Cascade::getLogger('system_email')->alert(sprintf('CRONSTATE[401]: Unauthorized: %s', _t('Cronjob handler password needs to be changed.')));
            exit(_t('Cronjob handler password needs to be changed.'));
        } elseif (isset($argv[0]) && (substr($argv[1], 0, 8) != 'password' or substr($argv[1], 9) != _h($setting->cronjobpassword))) {
            Cascade::getLogger('system_email')->alert(sprintf('CRONSTATE[401]: Unauthorized: %s', _t('Invalid argument password (password=yourpassword)')));
            exit(_t('Invalid argument password (password=yourpassword)'));
        }

        if (isset($run) && $run == true) {
            exit(_t('Cronjob already running'));
        }

        $run = true;

        if (is_object($cron) && count($cron) > 0) {
            $d = Jenssegers\Date\Date::now();
            // execute only one job and then exit
            foreach ($cron as $job) {

                if (isset($app->req->get['id']) && _h($job->id) == $app->req->get['id']) {
                    $run = true;
                } else {
                    $run = false;
                    if ($job->time != '') {
                        if (substr(_h($job->lastrun), 0, 10) != $d) {
                            if (strtotime($d->format('Y-m-d H:i')) > strtotime($d->format('Y-m-d ') . _h($job->time))) {
                                $run = true;
                            }
                        }
                    } elseif ($job->each > 0) {
                        if (strtotime(_h($job->lastrun)) + _h($job->each) < strtotime($d)) {
                            $run = true;
                            // if time set, daily after time...
                            if (_h($job->each) > (60 * 60 * 24) && strlen(_h($job->eachtime)) == 5 && strtotime($d->format('Y-m-d H:i')) < strtotime($d->format('Y-m-d') . _h($job->eachtime))) {
                                // only run 'today' at or after give time.
                                $run = false;
                            }
                        }
                    } elseif (substr(_h($job->lastrun), 0, 10) != $d->format('Y-m-d')) {
                        $run = true;
                    }
                }

                if ($run == true) {
                    // save as executed
                    echo _t('Running: ') . _h($job->url) . PHP_EOL . PHP_EOL;

                    try {
                        $upd = Node::table('cronjob_handler')->find(_h($job->id));
                        $upd->lastrun = $d->format('Y-m-d H:i:s');
                        $upd->runned ++;
                        $upd->save();
                    } catch (NodeQException $e) {
                        Cascade::getLogger('error')->error(sprintf('CRONSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                    } catch (Exception $e) {
                        Cascade::getLogger('error')->error(sprintf('CRONSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    }

                    echo _t('Connecting to cronjob') . PHP_EOL . PHP_EOL;

                    // execute cronjob
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, _h($job->url));
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, (!empty($setting->timeout) ? $setting->timeout : 5));

                    curl_exec($ch);

                    if (curl_errno($ch)) {
                        Cascade::getLogger('system_email')->alert(sprintf('CRONSTATE[400]: Bad request: %s', curl_error($ch)));
                        echo _t('Cronjob error: ') . curl_error($ch) . PHP_EOL;
                    } else {
                        echo _t('Cronjob data loaded') . PHP_EOL;
                    }

                    curl_close($ch);
                }
            }
        }
    });

    $app->before('POST|PUT|DELETE|OPTIONS', '/purgeActivityLog/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });
    
    $app->get('/purgeActivityLog/', function () {
        etsis_logger_activity_log_purge();
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/runEmailHold/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/runEmailHold/', function () use($app) {
        try {
            session_start();
            /**
             * SELECT all records from the email_hold table
             * and join with the saved_query table to retrieve
             * the savedQuery for $q2.
             */
            $email = $app->db->email_hold()
                ->select('email_hold.*, b.savedQuery')
                ->_join('saved_query', ' email_hold.queryID = b.savedQueryID', 'b')
                ->where('email_hold.processed = "0"');
            $q = $email->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            $array = [];
            foreach ($q as $r) {
                $array[] = $r;
            }

            $hold2 = $app->db->email_queue()
                ->where('holdID = ?', _h($r['id']))->_and_()
                ->where('email <> ""')->_and_()
                ->where('sent = "0"');
            $q2 = $hold2->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $sess = _h($r['id']);

            /**
             * Use the savedQuery from $q1 to retrieve results
             * to input into the email_queue table for processing.
             */
            if (count(_h($r['fromEmail'])) > 0) {
                if (count($q2) <= 0) {
                    $query = _h($r['savedQuery']);
                    $hold1 = $app->db->query($query);
                    $q = $hold1->find(function ($data) {
                        $array = [];
                        foreach ($data as $d) {
                            $array[] = $d;
                        }
                        return $array;
                    });
                    foreach ($q as $v) {
                        $body = _escape($r['body']);
                        $body = str_replace('#uname#', _h($v['uname']), $body);
                        $body = str_replace('#email#', _h($v['email']), $body);
                        $body = str_replace('#fname#', _h($v['fname']), $body);
                        $body = str_replace('#lname#', _h($v['lname']), $body);
                        $body = str_replace('#personID#', _h($v['personID']), $body);

                        $queue = $app->db->email_queue();
                        $queue->personID = _h($r['personID']);
                        $queue->uname = _h($v['uname']);
                        $queue->lname = _h($v['lname']);
                        $queue->email = _h($v['email']);
                        $queue->fname = _h($v['fname']);
                        $queue->fromName = _h($r['fromName']);
                        $queue->fromEmail = _h($r['fromEmail']);
                        $queue->subject = _h($r['subject']);
                        $queue->holdID = _h($r['id']);
                        $queue->body = $body;
                        if ($queue->save()) {
                            $_SESSION["email_hold$sess"] = $sess;
                        }
                    }
                    if (isset($_SESSION["email_hold$sess"])) {
                        $update = $app->db->email_hold();
                        $update->processed = "1";
                        $update->dateTime = $app->db->NOW();
                        $update->where('id = ?', _h($r['id']))
                            ->update();
                        $app->cookies->remove('email_hold' . _h($r['id']));
                        session_destroy();
                    }
                }
            }
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/runEmailQueue/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/runEmailQueue/', function () use($app, $emailer) {
        try {
            $data = $app->db->email_queue()
                ->where('sent = "0"')->_and_()
                ->where('email <> ""')
                ->find();
            foreach ($data as $d) {
                if ($app->hook->has_action('etsisMailer_init', 'etsis_smtp')) {
                    try {
                        $emailer->IsSMTP();
                        $emailer->CharSet = 'UTF-8';
                        $emailer->Mailer = "smtp";
                        $emailer->Host = _h(get_option('etsis_smtp_host'));
                        $emailer->SMTPSecure = _h(get_option('etsis_smtp_smtpsecure'));
                        $emailer->Port = _h(get_option('etsis_smtp_port'));
                        $emailer->SMTPAuth = (_h(get_option("etsis_smtp_smtpauth")) == "yes") ? TRUE : FALSE;
                        if ($emailer->SMTPAuth) {
                            $emailer->Username = _h(get_option('etsis_smtp_username'));
                            $emailer->Password = _h(get_option('etsis_smtp_password'));
                        }
                        $emailer->AddAddress($d->email, $d->lname . ', ' . $d->fname);
                        $emailer->From = $d->fromEmail;
                        $emailer->FromName = $d->fromName;
                        $emailer->Sender = $emailer->From; // Return-Path
                        $emailer->AddReplyTo($emailer->From, $emailer->FromName); // Reply-To
                        $emailer->IsHTML(true);
                        $emailer->Subject = $d->subject;
                        $emailer->Body = $d->body;
                        $emailer->Send();
                        $emailer->ClearAddresses();
                        $emailer->ClearAttachments();
                    } catch (phpmailerException $e) {
                        Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    }
                } else {
                    try {
                        $emailer->CharSet = 'UTF-8';
                        $emailer->AddAddress($d->email, $d->lname . ', ' . $d->fname);
                        $emailer->From = $d->fromEmail;
                        $emailer->FromName = $d->fromName;
                        $emailer->Sender = $emailer->From; // Return-Path
                        $emailer->AddReplyTo($emailer->From, $emailer->FromName); // Reply-To
                        $emailer->IsHTML(true);
                        $emailer->Subject = $d->subject;
                        $emailer->Body = $d->body;
                        $emailer->Send();
                        $emailer->ClearAddresses();
                        $emailer->ClearAttachments();
                    } catch (phpmailerException $e) {
                        Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    }
                }
                $u = $app->db->email_queue();
                $u->sent = 1;
                $u->sentDate = $app->db->NOW();
                $u->where('id = ?', _h($d->id));
                $u->update();
            }
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/purgeEmailHold/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/purgeEmailHold/', function () use($app) {
        $now = date('Y-m-d');
        try {
            $app->db->email_hold()
                ->where('email_hold.processed = "1"')->_and_()
                ->where('DATE_ADD(email_hold.dateTime, INTERVAL 15 DAY) <= ?', $now)
                ->delete();
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/purgeEmailHold/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/purgeEmailQueue/', function () use($app) {

        try {
            $app->db->email_queue()
                ->where('sent = "1"')->_or_()
                ->where('email = ""')
                ->delete();
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/runStuTerms/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/runStuTerms/', function () use($app) {
        try {
            /**
             * Select all records from the stu_acad_cred table.
             */
            $terms = $app->db->stu_acad_cred()
                ->setTableAlias('stac')
                ->select('stac.stuID,stac.courseSecCode,stac.termCode,stac.acadLevelCode,SUM(stac.attCred)')
                ->groupBy('stac.stuID,stac.termCode,stac.acadLevelCode');
            $q = $terms->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            if (count($q) > 0) {
                /**
                 * If a student ID exists in the stu_acad_cred table,
                 * but does not exist in the sttr table, then insert
                 * that new record into the sttr table.
                 */
                $app->db->query("INSERT IGNORE INTO sttr (stuID,termCode,attCred,created,acadLevelCode) 
				SELECT stuID,termCode,SUM(attCred),NOW(),acadLevelCode FROM stu_acad_cred 
				GROUP BY stuID,termCode,acadLevelCode");
            }
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/updateStuTerms/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/updateStuTerms/', function () use($app) {
        try {
            $terms = $app->db->query("SELECT 
                    SUM(stac.attCred) AS stacAttCreds,SUM(stac.compCred) AS stacCompCreds,
                    stac.stuID,stac.termCode,stac.acadLevelCode,SUM(stac.gradePoints) AS stacPoints,
                    sttr.attCred AS sttrAttCreds,sttr.gradePoints AS sttrPoints,
                    sttr.gpa 
                FROM stu_acad_cred stac 
                LEFT JOIN sttr ON stac.stuID = sttr.stuID 
                WHERE stac.termCode = sttr.termCode 
                AND stac.acadLevelCode = sttr.acadLevelCode 
                GROUP BY stac.stuID,stac.termCode,stac.acadLevelCode");
            $q = $terms->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            foreach ($q as $r) {
                $GPA = _h($r['stacPoints']) / _h($r['stacAttCreds']);
                if (_h($r['stacAttCreds']) != _h($r['sttrAttCreds']) || _h($r['sttrPoints']) != _h($r['stacPoints']) || _h($r['gpa']) != $GPA) {
                    $q2 = $app->db->sttr();
                    $q2->attCred = _h($r['stacAttCreds']);
                    $q2->compCred = _h($r['stacCompCreds']);
                    $q2->gradePoints = _h($r['stacPoints']);
                    $q2->stuLoad = getstudentload(_h($r['termCode']), _h($r['stacAttCreds']), _h($r['acadLevelCode']));
                    $q2->gpa = $GPA;
                    $q2->where('stuID = ?', _h($r['stuID']))->_and_()
                        ->where('termCode = ?', _h($r['termCode']))->_and_()
                        ->where('acadLevelCode = ?', _h($r['acadLevelCode']))
                        ->update();
                }
            }
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/runGraduation/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/runGraduation/', function () use($app) {
        try {
            $hold = $app->db->graduation_hold()
                ->select('graduation_hold.id,graduation_hold.queryID,graduation_hold.gradDate,b.savedQuery')
                ->_join('saved_query', 'graduation_hold.queryID = b.savedQueryID', 'b');
            $q1 = $hold->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $q1Array = [];
            foreach ($q1 as $r1) {
                $q1Array[] = $r1;
            }

            /**
             * If the above query has at least one row,
             * then process the savedQuery.
             */
            if (count($q1Array) > 0) {
                $sq = $app->db->query(_h($r1['savedQuery']));
                $q2 = $sq->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
            }
            /**
             * If the savedQuery above is successful,
             * then graduate the students from the query.
             */
            if ($q2) {
                foreach ($q2 as $r2) {
                    $prog = $app->db->stu_program();
                    $prog->graduationDate = _h($r1['gradDate']);
                    $prog->currStatus = 'G';
                    $prog->statusDate = $app->db->NOW();
                    $prog->endDate = $app->db->NOW();
                    $prog->where('stuID = ?', _h($r2['stuID']))
                        ->update();
                }
            }
            /* Delete records from graduation_hold after above queries have been processed. */
            $app->db->query("TRUNCATE graduation_hold");
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/purgeErrorLog/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/purgeErrorLog/', function () use($app) {
        $now = date('Y-m-d');
        try {
            $app->db->error()
                ->where('DATE_ADD(error.addDate, INTERVAL 5 DAY) <= ?', $now)
                ->delete();
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }

        etsis_logger_error_log_purge();
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/purgeSavedQuery/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/purgeSavedQuery/', function () use($app) {
        $now = date('Y-m-d');
        try {
            $app->db->saved_query()
                ->where('DATE_ADD(saved_query.createdDate, INTERVAL 30 DAY) <= ?', $now)->_and_()
                ->where('saved_query.purgeQuery = "1"')
                ->delete();
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/checkStuBalance/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/checkStuBalance/', function () use($app) {
        try {
            $bal = $app->db->query("SELECT pay.stuID,pay.termCode,COALESCE(Fees,0)+COALESCE(Tuition,0) as Fees,COALESCE(SUM(pay.amount),0) AS Payments,COALESCE(Fees,0)+COALESCE(Tuition,0)+COALESCE(SUM(pay.amount),0) AS Balance
            FROM payment AS pay LEFT JOIN
            (SELECT COALESCE(SUM(y.amount),0)*-1 AS Fees,y.stuID,y.termCode
             FROM stu_acct_fee y
             WHERE y.type = 'Fee'
             GROUP BY y.stuID,y.termCode) saf 
             ON pay.stuID = saf.stuID AND pay.termCode = saf.termCode
             LEFT JOIN
             (SELECT COALESCE(SUM(a.total),0)*-1 AS Tuition,a.stuID,a.termCode
             FROM stu_acct_tuition a
             GROUP BY a.stuID, a.termCode) stu_tuition
             ON pay.stuID = stu_tuition.stuID AND pay.termCode = stu_tuition.termCode
             GROUP BY pay.stuID,pay.termCode");
            $q = $bal->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            foreach ($q as $r) {
                if (_h($r['Balance']) >= 0) {
                    $result = $app->db->stu_acct_bill();
                    $result->balanceDue = '0';
                    $result->where('stuID = ?', _h($r['stuID']))->_and_()
                        ->where('termCode = ?', _h($r['termCode']))
                        ->update();
                } elseif (_h($r['Balance']) < 0) {
                    $result = $app->db->stu_acct_bill();
                    $result->balanceDue = '1';
                    $result->where('stuID = ?', _h($r['stuID']))->_and_()
                        ->where('termCode = ?', _h($r['termCode']))
                        ->update();
                }
            }
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('SQLSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/runDBBackup/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/runDBBackup/', function () use($app) {
        $dbhost = DB_HOST;
        $dbuser = DB_USER;
        $dbpass = DB_PASS;
        $dbname = DB_NAME;

        try {
            $backupDir = $app->config('file.savepath') . 'backups' . DS;
            if (!etsis_file_exists($backupDir, false)) {
                _mkdir($backupDir);
            }
        } catch (\app\src\Core\Exception\IOException $e) {
            Cascade\Cascade::getLogger('system_email')->alert(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()));
        }

        $backupFile = $backupDir . $dbname . '-' . date("Y-m-d-H-i-s") . '.gz';
        if (!etsis_file_exists($backupFile, false)) {
            $command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $dbname | gzip > $backupFile";
            system($command);
        }
        $files = glob($backupDir . "*.gz");
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file) && time() - filemtime($file) >= 20 * 24 * 3600) { // 20 days
                    unlink($file);
                }
            }
        }
    });
    
    $app->before('POST|PUT|DELETE|OPTIONS', '/runNodeQ/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/runNodeQ/', function () {
        etsis_nodeq_login_details();
        etsis_nodeq_reset_password();
        etsis_nodeq_csv_email();
        etsis_nodeq_change_address();
        etsis_nodeq_send_sms();
    });
});

$app->setError(function () use($app) {

    $app->view->display('error/404', [
        'title' => '404 Error'
    ]);
});

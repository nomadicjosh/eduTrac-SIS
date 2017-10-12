<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\Queue\etsis_NodeqQueue as Queue;
use app\src\Core\NodeQ\Helpers\Validate as Validate;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;
use app\src\Core\etsis_Mysqldump as Mysqldump;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

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

        if (!Validate::table('tasks')->exists()) {
            Node::dispense('tasks');
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
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/']);
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                } catch (Exception $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/']);
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }
            }
            etsis_redirect($app->req->server['HTTP_REFERER']);
        }

        try {

            $set = Node::table('cronjob_setting')->findAll();
            $job = Node::table('cronjob_handler')->findAll();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/']);
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/']);
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
                        ->where('url', '=', $app->req->post['url'])
                        ->find();
                } catch (NodeQException $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/new/']);
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                } catch (Exception $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/new/']);
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }

                $found = false;
                if ($url->count() > 0) {
                    $found = true;
                }

                if ($found == false) {
                    if ($app->req->post['each'] == '') {
                        _etsis_flash()->error(_t('Time setting missing, please add time settings.'));
                    } else {
                        try {
                            $cron = Node::table('cronjob_handler');
                            $cron->name = (string) $app->req->post['name'];
                            $cron->url = (string) $app->req->post['url'];
                            $cron->each = (int) $app->req->post['each'];
                            $cron->eachtime = ((isset($app->req->post['eachtime']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $app->req->post['eachtime'])) ? $app->req->post['eachtime'] : '');
                            $cron->status = (int) $app->req->post['status'];
                            $cron->save();

                            _etsis_flash()->success(_etsis_flash()->notice(200));
                        } catch (NodeQException $e) {
                            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/new/']);
                            _etsis_flash()->error(_etsis_flash()->notice(409));
                        } catch (Exception $e) {
                            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/new/']);
                            _etsis_flash()->error(_etsis_flash()->notice(409));
                        }
                    }
                } else {
                    _etsis_flash()->error(_t('Cronjob handler already exists in the system.'));
                }
            } else {
                _etsis_flash()->error(_t('Cronjob URL is wrong.'));
            }
            etsis_redirect(get_base_url() . 'cron/');
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
                    $cron->cronjobpassword = (string) $app->req->post['cronjobpassword'];
                    $cron->timeout = (isset($app->req->post['timeout']) && is_numeric($app->req->post['timeout']) ? (int) $app->req->post['timeout'] : 30);
                    $cron->save();

                    _etsis_flash()->success(_etsis_flash()->notice(200));
                } catch (NodeQException $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/setting/']);
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                } catch (Exception $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/setting/']);
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }
            }
            etsis_redirect($app->req->server['HTTP_REFERER']);
        }

        try {
            $set = Node::table('cronjob_setting')->find(1);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/setting/']);
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/setting/']);
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
                    $cron->name = (string) $app->req->post['name'];
                    $cron->url = (string) $app->req->post['url'];
                    $cron->each = (int) $app->req->post['each'];
                    $cron->eachtime = ((isset($app->req->post['eachtime']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $app->req->post['eachtime'])) ? $app->req->post['eachtime'] : '');
                    $cron->status = (int) $app->req->post['status'];
                    $cron->save();

                    _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                } catch (NodeQException $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/' . $id . '/']);
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                } catch (Exception $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/' . $id . '/']);
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }
            } else {
                _etsis_flash()->error(_t('Current URL is not correct; must begin with http(s):// and followed with a path.'));
            }
        }

        try {
            $sql = Node::table('cronjob_handler')->find($id);
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/' . $id . '/']);
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/' . $id . '/']);
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
         */ elseif (_escape($sql->id) <= 0) {

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

            if (!isset($app->req->get['password']) && !isset($argv[1])) {
                Cascade::getLogger('system_email')->alert(sprintf('CRONSTATE[401]: Unauthorized: %s', _t('No cronjob password found, use cronjob?password=<yourpassword>.')));
                exit(_t('No cronjob handler password found, use cronjob?password=<yourpassword>.'));
            } elseif (isset($app->req->get['password']) && $app->req->get['password'] != _escape($setting->cronjobpassword)) {
                Cascade::getLogger('system_email')->alert(sprintf('CRONSTATE[401]: Unauthorized: %s', _t('Invalid $app->req->get password')));
                exit(_t('Invalid $app->req->get password'));
            } elseif (_escape($setting->cronjobpassword) == 'changeme') {
                Cascade::getLogger('system_email')->alert(sprintf('CRONSTATE[401]: Unauthorized: %s', _t('Cronjob handler password needs to be changed.')));
                exit(_t('Cronjob handler password needs to be changed.'));
            } elseif (isset($argv[0]) && (substr($argv[1], 0, 8) != 'password' or substr($argv[1], 9) != _escape($setting->cronjobpassword))) {
                Cascade::getLogger('system_email')->alert(sprintf('CRONSTATE[401]: Unauthorized: %s', _t('Invalid argument password (password=yourpassword)')));
                exit(_t('Invalid argument password (password=yourpassword)'));
            }

            if (isset($run) && $run == true) {
                exit(_t('Cronjob already running . . .'));
            }

            $run = true;

            if (is_object($cron) && count($cron) > 0) {
                $d = Jenssegers\Date\Date::now();
                // execute only one job and then exit
                foreach ($cron as $job) {

                    if (isset($app->req->get['id']) && _escape($job->id) == $app->req->get['id']) {
                        $run = true;
                    } else {
                        $run = false;
                        if ($job->time != '') {
                            if (substr(_escape($job->lastrun), 0, 10) != $d) {
                                if (strtotime($d->format('Y-m-d H:i')) > strtotime($d->format('Y-m-d ') . _escape($job->time))) {
                                    $run = true;
                                }
                            }
                        } elseif ($job->each > 0) {
                            if (strtotime(_escape($job->lastrun)) + _escape($job->each) < strtotime($d)) {
                                $run = true;
                                // if time set, daily after time...
                                if (_escape($job->each) > (60 * 60 * 24) && strlen(_escape($job->eachtime)) == 5 && strtotime($d->format('Y-m-d H:i')) < strtotime($d->format('Y-m-d') . _escape($job->eachtime))) {
                                    // only run 'today' at or after give time.
                                    $run = false;
                                }
                            }
                        } elseif (substr(_escape($job->lastrun), 0, 10) != $d->format('Y-m-d')) {
                            $run = true;
                        }
                    }

                    if ($run == true) {
                        // save as executed
                        echo _t('Running: ') . _escape($job->url) . PHP_EOL . PHP_EOL;

                        try {
                            $upd = Node::table('cronjob_handler')->find(_escape($job->id));
                            $upd->lastrun = $d->format('Y-m-d H:i:s');
                            $upd->runned = $upd->runned + 1;
                            $upd->save();
                        } catch (NodeQException $e) {
                            Cascade::getLogger('error')->error(sprintf('CRONSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/cronjob/']);
                        } catch (Exception $e) {
                            Cascade::getLogger('error')->error(sprintf('CRONSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/cronjob/']);
                        }

                        echo _t('Connecting to cronjob') . PHP_EOL . PHP_EOL;

                        // execute cronjob
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, _escape($job->url));
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
        } catch (NodeQException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/cronjob/']);
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/cronjob/']);
        }
    });

    $app->before('POST|PUT|DELETE|OPTIONS', '/runALST/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 404);
        exit();
    });

    $app->get('/runALST/', function () use($app) {
        $count = $app->db->stal()
            ->where('acadStanding = "PROB"')
            ->count('id');
        if ($count > 0) {
            $q = "SELECT CONCAT(person.lname,', ',person.fname) AS 'Student Name',"
                . " CASE WHEN (person.altID IS NOT NULL) THEN person.altID ELSE person.personID END 'Student ID',"
                . " stal.acadProgCode AS 'Student Program',clas.name AS 'Class Level',"
                . " stal.enrollmentStatus AS 'Enrollment Status',stal.gpa AS GPA,stal.startTerm as 'Start Term'"
                . " FROM stal"
                . " LEFT JOIN person ON stal.stuID  = person.personID"
                . " LEFT JOIN clas ON stal.currentClassLevel = clas.code"
                . " WHERE stal.acadStanding = 'PROB'"
                . " AND stal.endDate IS NULL"
                . " GROUP BY stal.stuID, stal.acadStanding";

            $filename = \app\src\ID::string(10) . ".csv";
            $csv = new \app\src\CSVEmail(DB_HOST, DB_NAME, DB_USER, DB_PASS);
            $csv->setCSVname($filename);
            $csv->setQuery($q);
            $csv->setCSVinfo('"', ",", "\n");

            try {
                Node::dispense('csv_email');
                $node = Node::table('csv_email');
                $node->recipient = (string) _escape(get_option('registrar_email_address'));
                $node->message = (string) "<h3>" . _t('Students on Academic Probation') . "</h3><p>" . _t('The attached report can be opened with OpenOffice.org Calc, Google Docs, Microsoft Excel, or Apple Numbers.') . "</p>";
                $node->subject = (string) _t('Students on Academic Probation');
                $node->filename = (string) $filename;
                $node->sent = (int) 0;
                $node->save();

                $csv->saveCSV();
            } catch (NodeQException $e) {
                Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runALST/']);
            } catch (Exception $e) {
                Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runALST/']);
            }
        }
        return false;
    });

    $app->before('POST|PUT|DELETE|OPTIONS', '/master/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 404);
        exit();
    });

    $app->get('/master/', function () use($app) {
        /**
         * 
         */
        $app->hook->do_action('etsis_task_worker_cron');

        $jobby = new \Jobby\Jobby();
        try {
            $setting = Node::table('cronjob_setting')->find(1);

            // Every job has a name
            $jobby->add('MasterCronJob', [
                // Run a shell command
                'command' => '/usr/bin/curl -s ' . get_base_url() . 'cron/cronjob/?password=' . _escape($setting->cronjobpassword),
                // Ordinary crontab schedule format is supported.
                // This schedule runs every 5 minutes.
                // You could also insert DateTime string in the format of Y-m-d H:i:s.
                'schedule' => '*/5 * * * *',
                // Stdout and stderr is sent to the specified file
                'output' => APP_PATH . 'tmp/logs/etsis-error-' . \Jenssegers\Date\Date::now()->format('Y-m-d') . '.txt',
                // You can turn off a job by setting 'enabled' to false
                'enabled' => (_escape(get_option('enable_cron_jobs') == 1 ? (bool) true : (bool) false)),
            ]);
            $jobby->run();

            $tasks = Node::table('tasks')->where('enabled', '=', (bool) true)->findAll();
            if ($tasks->count() > 0) {

                $array = [];
                foreach ($tasks as $task) {
                    $array[] = (array) $task;
                }

                foreach ($array as $queue) {
                    if (!function_exists($queue['task_callback'])) {
                        Node::table('tasks')
                            ->where('id', '=', $queue['id'])
                            ->delete();
                    }
                    $task = new Queue($queue);
                    $task->createItem($queue);

                    $jobs_to_do = true;
                    $start = microtime(true);

                    try {
                        while ($jobs_to_do) {
                            $item = $task->claimItem();
                            $data = maybe_unserialize($item->data);

                            if ($item) {
                                Cascade::getLogger('info')->info(sprintf('QUEUESTATE[8190]: Processing item %s . . .', $data['pid']), ['Cron' => 'Item']);
                                // Execute the job task in a different function.
                                if ($task->executeAction($data)) {
                                    // Delete the item.
                                    $task->deleteItem($item);
                                    Cascade::getLogger('info')->info(sprintf('QUEUESTATE[8190]: Item %s processed.', $data['pid']), ['Cron' => 'Action Hook']);
                                } else {
                                    // Release the item to execute the job task again later.
                                    $task->releaseItem($item);
                                    Cascade::getLogger('info')->info(sprintf('QUEUESTATE[8190]: Item %s NOT processed.', $data['pid']), ['Cron' => 'Release Item']);
                                    $jobs_to_do = false;
                                    Cascade::getLogger('info')->info('QUEUESTATE[8190]: Queue not completed. Item not executed.', ['Cron' => 'Release Item']);
                                }
                            } else {
                                $jobs_to_do = false;
                                $time_elapsed = microtime(true) - $start;
                                $number_of_items = $task->numberOfItems();
                                if ($number_of_items == 0) {
                                    Cascade::getLogger('info')->info(sprintf('QUEUESTATE[8190]: Queue completed in %s seconds.', $time_elapsed), ['Cron' => '# of Items']);
                                } else {
                                    Cascade::getLogger('info')->info(sprintf('QUEUESTATE[8190]: Queue not completed, there are %s items left.', $number_of_items), ['Cron' => '# of Items']);
                                }
                            }
                        }
                    } catch (Exception $e) {
                        if ($queue['debug']) {
                            Cascade::getLogger('error')->error(sprintf('QUEUESTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Queue' => 'Claim Queue Item']);
                            Cascade::getLogger('system_email')->alert(sprintf('QUEUESTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Queue' => 'Claim Queue Item']);
                        }
                    }
                }
            }
        } catch (NodeQException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Master']);
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Master']);
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

    $app->before('POST|PUT|DELETE|OPTIONS', '/runEmailQueue/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/runEmailQueue/', function () use($app) {
        if (!function_exists('mrkt_module')) {
            return false;
        }

        try {
            $cpgn = $app->db->campaign()
                ->where('campaign.status = "processing"')
                ->findOne();

            if ($cpgn != false) {
                try {
                    /**
                     * Checks if any unsent emails are left in the queue.
                     * If not, mark campaign as `sent`.
                     */
                    $sent = Node::table('campaign_queue')
                        ->where('is_sent', '=', 'false')
                        ->andWhere('cid', '=', _escape($cpgn->id))
                        ->findAll()
                        ->count();
                    if ($sent <= 0 && _escape($cpgn->status) != 'sent') {
                        $complete = $app->db->campaign()
                            ->where('id = ?', _escape($cpgn->id))->_and_()
                            ->where('status <> "sent"')
                            ->findOne();
                        $complete->status = 'sent';
                        $complete->update();
                        return true;
                    }

                    // instantiate the message queue
                    $queue = new \app\src\Core\etsis_EmailQueue();

                    // get messages from the queue
                    $messages = $queue->getEmails();
                    $i = 0;
                    $last = Node::table('campaign_queue')->where('cid', '=', _escape($cpgn->id))->orderBy('id', 'DESC')->limit(1)->find();
                    // iterate messages
                    foreach ($messages as $message) {
                        $sub = get_person_by('email', $message->getToEmail());
                        /* $slist = $app->db->subscriber_list()
                          ->where('subscriber_list.lid = ?', $message->getListId())->_and_()
                          ->where('subscriber_list.sid = ?', $message->getSubscriberId())
                          ->findOne();

                          $list = get_list_by('id', $message->getListId()); */

                        /**
                         * Generate slug from subject. Useful for Google Analytics.
                         */
                        $slug = _etsis_unique_campaign_slug(_escape($cpgn->subject));
                        /**
                         * Create an array to merge later.
                         */
                        $custom_headers = [
                            'xcampaignid' => $message->getMessageId(),
                            'xlistid' => $message->getListId(),
                            'xsubscriberid' => $message->getSubscriberId(),
                            'xsubscriberemail' => $message->getToEmail(),
                        ];
                        $footer = _escape($cpgn->footer);
                        $footer = str_replace('{email}', _escape($sub->email), $footer);
                        $footer = str_replace('{from_email}', _escape($cpgn->from_email), $footer);
                        //$footer = str_replace('{personal_preferences}', get_base_url() . 'preferences/' . _escape($sub->code) . '/subscriber/' . _escape($sub->id) . '/', $footer);
                        //$footer = str_replace('{unsubscribe_url}', get_base_url() . 'unsubscribe/' . _escape($slist->code) . '/lid/' . _escape($slist->lid) . '/sid/' . _escape($slist->sid) . '/rid/' . _escape($message->getId()) . '/', $footer);

                        $msg = _escape($cpgn->html);
                        $msg = str_replace('{todays_date}', \Jenssegers\Date\Date::now()->format('M d, Y'), $msg);
                        $msg = str_replace('{subject}', _escape($cpgn->subject), $msg);
                        $msg = str_replace('{view_online}', '<a href="' . get_base_url() . 'archive/' . _escape($cpgn->id) . '/">' . _t('View this email in your browser') . '</a>', $msg);
                        $msg = str_replace('{first_name}', _escape($sub->fname), $msg);
                        $msg = str_replace('{last_name}', _escape($sub->lname), $msg);
                        $msg = str_replace('{email}', _escape($sub->email), $msg);
                        $msg = str_replace('{address1}', _escape($sub->address1), $msg);
                        $msg = str_replace('{address2}', _escape($sub->address2), $msg);
                        $msg = str_replace('{city}', _escape($sub->city), $msg);
                        $msg = str_replace('{state}', _escape($sub->state), $msg);
                        $msg = str_replace('{postal_code}', _escape($sub->postal_code), $msg);
                        $msg = str_replace('{country}', _escape($sub->country), $msg);
                        //$msg = str_replace('{unsubscribe_url}', '<a href="' . get_base_url() . 'unsubscribe/' . _escape($slist->code) . '/lid/' . _escape($slist->lid) . '/sid/' . _escape($slist->sid) . '/rid/' . _escape($message->getId()) . '/">' . _t('unsubscribe') . '</a>', $msg);
                        //$msg = str_replace('{personal_preferences}', '<a href="' . get_base_url() . 'preferences/' . _escape($sub->code) . '/subscriber/' . _escape($sub->id) . '/">' . _t('preferences page') . '</a>', $msg);
                        $msg .= $footer;
                        //$msg .= etsis_footer_logo();
                        $msg .= campaign_tracking_code(_escape($cpgn->id), _escape($sub->id));

                        if (++$i === 1) {
                            $q = $app->db->campaign()
                                ->where('id = ?', _escape($cpgn->id))
                                ->findOne();
                            $finish = strtotime($last->timestamp_to_send);
                            $q->sendfinish = date("Y-m-d H:i:s", strtotime('+15 minutes', $finish));
                            $q->update();
                        }
                        // send email
                        $app->hook->{'do_action_array'}('etsis_email_init', [
                            (object) $custom_headers,
                            $message->getToEmail(),
                            _escape($cpgn->subject),
                            etsis_link_tracking($msg, _escape($cpgn->id), _escape($sub->id), $slug),
                            etsis_link_tracking(_escape($cpgn->text), _escape($cpgn->id), _escape($sub->id), $slug),
                            $message
                            ]
                        );
                    }
                } catch (NodeQException $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runEmailQueue/']);
                } catch (InvalidArgumentException $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runEmailQueue/']);
                } catch (Exception $e) {
                    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runEmailQueue/']);
                }
            }
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('QUEUESTATE[%s]: Conflict: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runEmailQueue/']);
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('QUEUESTATE[%s]: Conflict: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runEmailQueue/']);
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('QUEUESTATE[%s]: Conflict: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runEmailQueue/']);
        }
    });

    $app->before('POST|PUT|DELETE|OPTIONS', '/runBounceHandler/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/runBounceHandler/', function () {
        if (!function_exists('mrkt_module')) {
            return false;
        }

        try {
            $node = Node::table('php_encryption')->find(1);
            try {
                if (_escape(get_option('etsis_bmh_password')) != '') {
                    $password = Crypto::decrypt(_escape(get_option('etsis_bmh_password')), Key::loadFromAsciiSafeString($node->key));
                } else {
                    $password = _escape(get_option('etsis_bmh_password'));
                }
            } catch (Defuse\Crypto\Exception\BadFormatException $e) {
                Cascade::getLogger('system_email')->alert(sprintf('BOUNCESTATE[%s]: Conflict: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runBounceHandler/']);
            } catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $e) {
                Cascade::getLogger('system_email')->alert(sprintf('BOUNCESTATE[%s]: Conflict: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runBounceHandler/']);
            } catch (Exception $e) {
                Cascade::getLogger('system_email')->alert(sprintf('BOUNCESTATE[%s]: Conflict: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runBounceHandler/']);
            }
        } catch (NodeQException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runBounceHandler/']);
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runBounceHandler/']);
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runBounceHandler/']);
        }

        $time_start = microtime_float();

        $bmh = new app\src\Core\etsis_BounceHandler();
        $bmh->actionFunction = 'bounce_callback_action'; // default is 'bounce_callback_action'
        $bmh->verbose = app\src\Core\etsis_BounceHandler::VERBOSE_SIMPLE; //app\src\Core\etsis_BounceHandler::VERBOSE_SIMPLE; //app\src\Core\etsis_BounceHandler::VERBOSE_REPORT; //app\src\Core\etsis_BounceHandler::VERBOSE_DEBUG; //app\src\Core\etsis_BounceHandler::VERBOSE_QUIET; // default is BounceMailHandler::VERBOSE_SIMPLE
        //$bmh->useFetchStructure  = true; // true is default, no need to specify
        //$bmh->testMode           = false; // false is default, no need to specify
        //$bmh->debugBodyRule      = false; // false is default, no need to specify
        //$bmh->debugDsnRule       = false; // false is default, no need to specify
        //$bmh->purgeUnprocessed   = false; // false is default, no need to specify
        $bmh->disableDelete = true; // false is default, no need to specify

        /*
         * for remote mailbox
         */
        $bmh->mailhost = _escape(get_option('etsis_bmh_host')); // your mail server
        $bmh->mailboxUserName = _escape(get_option('etsis_bmh_username')); // your mailbox username
        $bmh->mailboxPassword = $password; // your mailbox password
        $bmh->port = _escape(get_option('etsis_bmh_port')); // the port to access your mailbox, default is 143
        $bmh->service = _escape(get_option('etsis_bmh_service')); // the service to use (imap or pop3), default is 'imap'
        $bmh->serviceOption = _escape(get_option('etsis_bmh_service_option')); // the service options (none, tls, notls, ssl, etc.), default is 'notls'
        $bmh->boxname = (_escape(get_option('etsis_bmh_mailbox')) == '' ? 'INBOX' : _escape(get_option('etsis_bmh_mailbox'))); // the mailbox to access, default is 'INBOX'
        $bmh->moveHard = true; // default is false
        $bmh->hardMailbox = (_escape(get_option('etsis_bmh_mailbox')) == '' ? 'INBOX' : _escape(get_option('etsis_bmh_mailbox'))) . '.hard'; // default is 'INBOX.hard' - NOTE: must start with 'INBOX.'
        $bmh->moveSoft = true; // default is false
        $bmh->softMailbox = ''; // default is 'INBOX.soft' - NOTE: must start with 'INBOX.'
        $bmh->openMailbox();
        $bmh->processMailbox();
        $bmh->deleteMsgDate = Jenssegers\Date\Date::now()->format('yyyy-mm-dd H:i:s'); // format must be as 'yyyy-mm-dd'

        $time_end = microtime_float();
        $time = $time_end - $time_start;

        Cascade::getLogger('info')->info('BOUNCES[401]: ' . sprintf(_t('Seconds to process: %s'), $time));
    });

    $app->before('POST|PUT|DELETE|OPTIONS', '/updateSTTR/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/updateSTTR/', function () use($app) {
        try {
            $terms = $app->db->query("SELECT 
                    COALESCE(SUM(stac.attCred),0) AS stacAttCreds,COALESCE(SUM(stac.compCred),0) AS stacCompCreds,
                    stac.stuID,stac.termCode,stac.acadLevelCode,COALESCE(SUM(stac.gradePoints),0) AS stacPoints,
                    sttr.attCred AS sttrAttCreds,sttr.gradePoints AS sttrPoints,
                    sttr.gpa 
                FROM stac 
                LEFT JOIN sttr ON stac.stuID = sttr.stuID 
                LEFT JOIN grade_scale ON stac.grade = grade_scale.grade 
                WHERE stac.termCode = sttr.termCode 
                AND stac.acadLevelCode = sttr.acadLevelCode 
                AND stac.creditType = 'I' 
                AND grade_scale.count_in_gpa = '1' 
                AND (stac.grade IS NOT NULL) 
                GROUP BY stac.stuID,stac.termCode,stac.acadLevelCode 
                HAVING COALESCE(SUM(stac.gradePoints),0) > 0");
            $q = $terms->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            foreach ($q as $r) {
                $GPA = _escape($r['stacPoints']) / _escape($r['stacAttCreds']);
                $q2 = $app->db->sttr();
                $q2->set([
                        'attCred' => _escape($r['stacAttCreds']),
                        'compCred' => _escape($r['stacCompCreds']),
                        'gradePoints' => _escape($r['stacPoints']),
                        'stuLoad' => etsis_stld_rule(_escape($r['stuID']), _escape($r['stacAttCreds']), _escape($r['acadLevelCode']), _escape($r['termCode'])),
                        'gpa' => $GPA
                    ])
                    ->where('stuID = ?', _escape($r['stuID']))->_and_()
                    ->where('termCode = ?', _escape($r['termCode']))->_and_()
                    ->where('acadLevelCode = ?', _escape($r['acadLevelCode']))
                    ->update();
            }
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/updateSTTR/']);
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/updateSTTR/']);
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/updateSTTR/']);
        }
    });

    $app->before('POST|PUT|DELETE|OPTIONS', '/updateSTAL/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/updateSTAL/', function () use($app) {
        try {
            $spro = $app->db->sacp()
                ->select('sacp.stuID,sacp.acadProgCode,acad_program.acadLevelCode')
                ->_join('acad_program', 'sacp.acadProgCode = acad_program.acadProgCode')
                ->whereIn('sacp.currStatus', ['A', 'G', 'W']);
            $q = $spro->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            foreach ($q as $r) {
                $term = $app->db->sttr()
                    ->select('sttr.termCode,term.termStartDate')
                    ->_join('term', 'sttr.termCode = term.termCode')
                    ->_join('stal', 'sttr.stuID = stal.stuID AND sttr.acadLevelCode = stal.acadLevelCode')
                    ->_join('sacp', 'stal.acadProgCode = sacp.acadProgCode')
                    ->where('stuID = ?', _escape($r['stuID']))->_and_()
                    ->where('acadLevelCode = ?', _escape($r['acadLevelCode']))->_and_()
                    ->whereIn('sacp.currStatus', ['A', 'G', 'W'])
                    ->groupBy('stal.stuID,stal.acadProgCode,stal.acadLevelCode')
                    ->orderBy('created', 'ASC')
                    ->findOne();

                $status = $app->db->sttr()
                    ->select('stuLoad')
                    ->where('stuID = ?', _escape($r['stuID']))->_and_()
                    ->where('acadLevelCode = ?', _escape($r['acadLevelCode']))
                    ->orderBy('created', 'DESC')
                    ->findOne();

                $gpa = $app->db->stac()
                    ->select('SUM(stac.gradePoints)/SUM(stac.attCred) AS gpa')
                    ->_join('grade_scale', 'stac.grade = grade_scale.grade')
                    ->where('grade_scale.count_in_gpa = "1"')->_and_()
                    ->where('stac.stuID = ?', _escape($r['stuID']))->_and_()
                    ->where('(stac.grade IS NOT NULL)')->_and_()
                    ->where('stac.acadLevelCode = ?', _escape($r['acadLevelCode']))
                    ->groupBy('stac.stuID,stac.acadLevelCode')
                    ->findOne();

                $sacp = $app->db->sacp()
                    ->select('sacp.currStatus,IFNULL(sacp.graduationDate,sacp.endDate) AS EndDate')
                    ->_join('acad_program', 'sacp.acadProgCode = acad_program.acadProgCode')
                    ->where('sacp.stuID = ?', _escape($r['stuID']))->_and_()
                    ->where('acad_program.acadLevelCode = ?', _escape($r['acadLevelCode']))->_and_()
                    ->whereIn('sacp.currStatus', ['A', 'G', 'W'])
                    ->groupBy('acad_program.acadLevelCode,acad_program.acadProgCode')
                    ->findOne();

                $stal = $app->db->stal();
                $stal->set([
                        'currentClassLevel' => etsis_clvr_rule(_escape($r['stuID']), _escape($r['acadLevelCode'])) != '' ? etsis_clvr_rule(_escape($r['stuID']), _escape($r['acadLevelCode'])) : NULL,
                        'enrollmentStatus' => _escape($sacp->currStatus) <> 'A' ? _escape($sacp->currStatus) : _escape($status->stuLoad),
                        'acadStanding' => etsis_alst_rule(_escape($r['stuID']), _escape($r['acadLevelCode'])) != '' ? etsis_alst_rule(_escape($r['stuID']), _escape($r['acadLevelCode'])) : NULL,
                        'gpa' => _escape($gpa->gpa),
                        'startTerm' => _escape($term->termCode) != '' ? _escape($term->termCode) : NULL,
                        'startDate' => _escape($term->termStartDate) != '' ? _escape($term->termStartDate) : NULL,
                        'endDate' => _escape($sacp->EndDate) != NULL ? _escape($sacp->EndDate) : NULL
                    ])
                    ->where('stuID = ?', _escape($r['stuID']))->_and_()
                    ->where('acadLevelCode = ?', _escape($r['acadLevelCode']))->_and_()
                    ->where('acadProgCode = ?', _escape($r['acadProgCode']))
                    ->groupBy('stuID, acadProgCode, acadLevelCode')
                    ->update();
            }
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/updateSTAL/']);
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/updateSTAL/']);
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/updateSTAL/']);
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
                ->_join('saved_query', 'graduation_hold.queryID = b.id', 'b');
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
                $sq = $app->db->query(_escape($r1['savedQuery']));
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
                    $prog = $app->db->sacp();
                    $prog->graduationDate = _escape($r1['gradDate']);
                    $prog->currStatus = 'G';
                    $prog->statusDate = \Jenssegers\Date\Date::now();
                    $prog->endDate = \Jenssegers\Date\Date::now();
                    $prog->where('stuID = ?', _escape($r2['stuID']))
                        ->update();

                    $stal = $app->db->stal()
                        ->where('stuID = ?', _escape($r2['stuID']))->_and_()
                        ->where('acadProgCode = ?', _escape($r2['acadProgCode']))
                        ->findOne();
                    $stal->set([
                            'currentClassLevel' => NULL,
                            'enrollmentStatus' => 'G',
                            'acadStanding' => NULL,
                            'endDate' => _escape($r1['gradDate'])
                        ])
                        ->update();
                }
            }
            /* Delete records from graduation_hold after above queries have been processed. */
            $app->db->query("TRUNCATE graduation_hold");
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/runGraduation/']);
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/runGraduation/']);
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/runGraduation/']);
        }
    });

    $app->before('POST|PUT|DELETE|OPTIONS', '/purgeErrorLog/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/purgeErrorLog/', function () use($app) {
        $now = \Jenssegers\Date\Date::now()->format('Y-m-d');
        try {
            $app->db->error()
                ->where('DATE_ADD(error.addDate, INTERVAL 5 DAY) <= ?', $now)
                ->delete();
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/purgeErrorLog/']);
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/purgeErrorLog/']);
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/purgeErrorLog/']);
        }

        etsis_logger_error_log_purge();
    });

    $app->before('POST|PUT|DELETE|OPTIONS', '/purgeSavedQuery/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/purgeSavedQuery/', function () use($app) {
        $now = \Jenssegers\Date\Date::now()->format('Y-m-d');
        try {
            $app->db->saved_query()
                ->where('DATE_ADD(saved_query.createdDate, INTERVAL 30 DAY) <= ?', $now)->_and_()
                ->where('saved_query.purgeQuery = "1"')
                ->delete();
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/purgeSavedQuery/']);
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/purgeSavedQuery/']);
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/purgeSavedQuery/']);
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
                if (_escape($r['Balance']) >= 0) {
                    $result = $app->db->stu_acct_bill();
                    $result->balanceDue = '0';
                    $result->where('stuID = ?', _escape($r['stuID']))->_and_()
                        ->where('termCode = ?', _escape($r['termCode']))
                        ->update();
                } elseif (_escape($r['Balance']) < 0) {
                    $result = $app->db->stu_acct_bill();
                    $result->balanceDue = '1';
                    $result->where('stuID = ?', _escape($r['stuID']))->_and_()
                        ->where('termCode = ?', _escape($r['termCode']))
                        ->update();
                }
            }
        } catch (NotFoundException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/checkStuBalance/']);
        } catch (ORMException $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/checkStuBalance/']);
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/checkStuBalance/']);
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

        $dumpSettings = [
            'no-data' => $app->hook->apply_filter('db_table_no_data', ['']),
            'compress' => Mysqldump::NONE,
            'default-character-set' => Mysqldump::UTF8MB4,
            'add-drop-table' => true,
            'complete-insert' => false,
            'extended-insert' => false,
        ];

        try {
            $backupDir = $app->config('file.savepath') . 'backups' . DS . 'database' . DS;
            if (!etsis_file_exists($backupDir, false)) {
                _mkdir($backupDir);
            }
        } catch (\app\src\Core\Exception\IOException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runDBBackup/', 'Cron' => 'IOExeption']);
            return false;
        }

        try {
            $dump = new Mysqldump("mysql:host=$dbhost;dbname=$dbname", "$dbuser", "$dbpass", $dumpSettings);
            $dump->start($backupDir . DB_NAME . '_dump_' . Jenssegers\Date\Date::now()->format('Y-m-d-H-i') . '.sql');
        } catch (Exception $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/runDBBackup/', 'Cron' => 'Mysqldump']);
        } catch (\Exception $e) {
            Cascade::getLogger('system_email')->alert($e->getMessage(), ['Cron' => 'Route::/cron/runDBBackup/', 'Cron' => 'Mysqldump']);
        }

        $files = glob($backupDir . "*.sql");
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file) && time() - file_mod_time($file) >= $app->hook->apply_filter('backup_database', 10 * 24 * 3600)) { // 10 days
                    unlink($file);
                }
            }
        }
    });

    $app->before('POST|PUT|DELETE|OPTIONS', '/runSiteBackup/', function () use($app) {
        header('Content-Type: application/json');
        $app->res->_format('json', 401);
        exit();
    });

    $app->get('/runSiteBackup/', function () use($app) {
        try {
            $backupDir = $app->config('file.savepath') . 'backups' . DS . 'system' . DS;
            if (!etsis_file_exists($backupDir, false)) {
                _mkdir($backupDir);
            }
        } catch (\app\src\Core\Exception\IOException $e) {
            Cascade::getLogger('system_email')->alert(sprintf('IOSTATE[%s]: Forbidden: %s', $e->getCode(), $e->getMessage()), ['Cron' => 'Route::/cron/runSiteBackup/', 'Cron' => 'IOExeption']);
        }

        etsis_system_backup(BASE_PATH, $backupDir . 'system_' . Jenssegers\Date\Date::now()->format('Y-m-d-H-i') . '.zip');

        $files = glob($backupDir . "*.zip");
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file) && time() - file_mod_time($file) >= $app->hook->apply_filter('backup_system', 10 * 24 * 3600)) { // 10 days
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
        etsis_nodeq_course_registration();
        etsis_nodeq_student_email();
    });
});

$app->setError(function () use($app) {

    $app->view->display('error/404', [
        'title' => '404 Error'
    ]);
});

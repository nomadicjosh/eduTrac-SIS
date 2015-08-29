<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Cron Router
 *
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
session_start();
session_regenerate_id();

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
    2629743 => 'Month'];

// From: https://gist.github.com/Xeoncross/1204255
$regions = ['Africa' => DateTimeZone::AFRICA,
    'America' => DateTimeZone::AMERICA,
    'Antarctica' => DateTimeZone::ANTARCTICA,
    'Aisa' => DateTimeZone::ASIA,
    'Atlantic' => DateTimeZone::ATLANTIC,
    'Europe' => DateTimeZone::EUROPE,
    'Indian' => DateTimeZone::INDIAN,
    'Pacific' => DateTimeZone::PACIFIC];

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

/**
 * Retrieves a serialized array of cronjob handlers.
 * 
 * @since 6.0.00
 * @return mixed
 */
function getCronjobs()
{
    $cronDir = cronDir() . 'cron/';
    return unserialize(base64_decode(substr(file_get_contents($cronDir . 'cronjobs.dat.php'), 7, -2)));
}

/**
 * Saves new cronjob handlers to serialized array.
 * 
 * @since 6.0.00
 */
function saveCronjobs($data)
{
    $cronDir = cronDir() . 'cron/';
    if (!file_put_contents($cronDir . 'cronjobs.dat.php', '<' . '?php /*' . base64_encode(serialize($data)) . '*/')) {
        error_log('cannot write to cronjobs database file, please check file rights');
    }
}

/**
 * Saves cronjob handler activity to a log file.
 * 
 * @since 6.0.00
 */
function saveLogs($text)
{
    $cronDir = cronDir() . 'cron/';
    if (!file_put_contents($cronDir . 'logs/cronjobs.log', date('Y-m-d H:i:s') . ' - ' . $text . PHP_EOL . file_get_contents($cronDir . 'logs/cronjobs.log'))) {
        error_log('cannot write to cronjobs log file, please check rights');
    }
}

/**
 * Updates a cronjob handler.
 * 
 * @since 6.0.00
 */
function updateCronjobs($id = '')
{
    $app = \Liten\Liten::getInstance();
    $cronDir = cronDir() . 'cron/';
    if (file_put_contents($cronDir . 'cronjobs.dat.php', '<' . '?php /*' . base64_encode(serialize($_SESSION['cronjobs'])) . '*/')) {
        $app->flash('success_message', _t('Database saved.'));

        // create 'backup'
        file_put_contents($cronDir . 'cronjobs.backup-' . date('Y-m-d') . '.php', '<' . '?php /*' . base64_encode(serialize($_SESSION['cronjobs'])) . '*/');
    } else {
        $app->flash('error_message', _t('Database not saved, could not create database file on server, please check write rights of this script.'));
    }

    // remove old cronjob backup files
    $files = glob($cronDir . 'cronjobs.backup*.php');
    if (is_array($files)) {
        foreach ($files as $file) {
            if (is_file($file) && time() - filemtime($file) >= 2 * 24 * 60 * 60) { // 2 days
                unlink($file);
            }
        }
    }

    if ($id != '' && is_numeric($id)) {
        redirect(url('/cron/view/') . $id);
    } else {
        redirect($app->req->server['HTTP_REFERER']);
    }
    exit;
}
if (file_exists(cronDir() . 'cron/' . 'cronjobs.dat.php')) {
    $data = unserialize(base64_decode(substr(file_get_contents(cronDir() . 'cron/' . 'cronjobs.dat.php'), 7, -2)));
    if (is_array($data)) {
        $_SESSION['cronjobs'] = $data;
    }
} elseif (isset($_SESSION['cronjobs'])) {
    $_SESSION = null;
}

$logger = new \app\src\Log();
$dbcache = new \app\src\DBCache();
$email = new \app\src\Email;
$flashNow = new \app\src\Messages();
$emailer = new \app\src\PHPMailer;

$css = [ 'css/admin/module.admin.page.form_elements.min.css', 'css/admin/module.admin.page.tables.min.css'];
$js = [
    'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
    'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0'
];

$app->group('/cron', function() use($app, $css, $js, $logger, $emailer, $email) {

    /**
     * Before route checks to make sure the logged in user
     * us allowed to manage options/settings.
     */
    $app->before('GET', '/', function() {
        if (!hasPermission('access_cronjob_screen')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->match('GET|POST', '/', function () use($app, $css, $js) {
        if ($app->req->isPost()) {
            $cronDir = cronDir() . 'cron/';
            // remove from session
            foreach ($_POST['cronjobs'] as $k => $v) {
                // get log files, if exists;
                if (is_dir($cronDir . 'logs/')) {
                    $files = glob($cronDir . 'logs/*' . preg_replace('/[^A-Za-z0-9 ]/', '', $_SESSION['cronjobs']['jobs'][$k]['url']) . '.log');
                    // files found?
                    if (is_array($files) && count($files) > 0) {
                        // remove all!!
                        foreach ($files as $k => $file) {
                            if (!unlink($file)) {
                                $_SESSION['errors'][] = 'Could not remove file ' . $file . ' from server, please do this manually';
                                $app->flash('error_message', 'Could not remove file ' . $file . ' from server, please do this manually.');
                            }
                        }
                    }
                }
                unset($_SESSION['cronjobs']['jobs'][$k]);
            }

            $app->flash('success_message', count($_POST['cronjobs']) . ' cronjobs removed');

            updateCronjobs();
        }

        $app->view->display('cron/index', [
            'title' => 'Cronjob Handlers',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    /**
     * Before route checks to make sure the logged in user
     * us allowed to manage options/settings.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!hasPermission('access_cronjob_screen')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->match('GET|POST', '/new/', function () use($app, $css, $js, $flashNow) {
        if ($app->req->isPost()) {
            if (filter_var($_POST['url'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                $found = false;
                if (isset($_SESSION['cronjobs'], $_SESSION['cronjobs']['jobs']) && count($_SESSION['cronjobs']['jobs']) > 0) {
                    foreach ($_SESSION['cronjobs']['jobs'] as $null => $cronjob) {
                        if ($cronjob['url'] == $_POST['url']) {
                            $found = true;
                        }
                    }
                }

                if ($found == false) {
                    if ($_POST['time'] == '' && $_POST['each'] == '') {
                        $app->flash('error_message', _t('Time settings missing, please add time settings'));
                    } else {
                        if (isset($_POST['maillog'], $_POST['maillogaddress']) && !filter_var($_POST['maillogaddress'], FILTER_VALIDATE_EMAIL)) {
                            $app->flash('error_message', _t('Email address is invalid!'));
                        }

                        $_SESSION['cronjobs']['jobs'][] = array('url' => $_POST['url'],
                            'time' => ((isset($_POST['time']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $_POST['time'])) ? $_POST['time'] : ''),
                            'each' => ((isset($_POST['each']) && is_numeric($_POST['each'])) ? $_POST['each'] : ''),
                            'eachtime' => ((isset($_POST['eachtime']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $_POST['eachtime'])) ? $_POST['eachtime'] : ''),
                            'lastrun' => '',
                            'runned' => 0,
                            'savelog' => (isset($_POST['savelog']) ? true : false),
                            'maillog' => (isset($_POST['maillog']) ? true : false),
                            'maillogaddress' => ((isset($_POST['maillogaddress']) && filter_var($_POST['maillogaddress'], FILTER_VALIDATE_EMAIL)) ? $_POST['maillogaddress'] : ''));

                        updateCronjobs(count($_SESSION['cronjobs']['jobs']));
                    }
                } else {
                    $app->flash('error_message', _t('Cronjob already known in this system.'));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            } else {
                $app->flash('error_message', _t('Cronjob URL is wrong.'));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        $app->view->display('cron/new', [
            'title' => 'New Cronjob Handler',
            'cssArray' => $css,
            'jsArray' => $js
            ]
        );
    });

    $app->match('GET|POST', '/setting/', function () use($app, $css, $js, $flashNow) {
        if ($app->req->isPost()) {
            $good = true;

            if (strlen(trim($_POST['cronjobpassword'])) < 2) {
                $app->flash('error_message', _t('Your cronjob script cannot run without a password, Your cronjob password contains wrong characters, minimum of 4 letters and numbers.'));
                $good = false;
            }

            if ($good == true) {
                $_SESSION['cronjobs']['settings'] = [
                    'cronjobpassword' => $_POST['cronjobpassword'],
                    'timeout' => (isset($_POST['timeout']) && is_numeric($_POST['timeout']) ? $_POST['timeout'] : 30)
                ];
                updateCronjobs();
            }
        }

        $app->view->display('cron/setting', [
            'title' => 'Cronjob Handler Settings',
            'cssArray' => $css,
            'jsArray' => $js,
            'data' => $_SESSION['cronjobs']
            ]
        );
    });

    $app->match('GET|POST', '/view/(\d+)/', function ($id) use($app, $css, $js, $flashNow) {
        if ($app->req->isPost()) {

            if (filter_var($_POST['url'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                if (isset($_POST['maillog'], $_POST['maillogaddress']) && !filter_var($_POST['maillogaddress'], FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['errors'][] = 'Email address is invalid and not saved to database!';
                }

                $_SESSION['cronjobs']['jobs'][$id] = array('url' => $_POST['url'],
                    'time' => ((isset($_POST['time']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $_POST['time'])) ? $_POST['time'] : ''),
                    'each' => ((isset($_POST['each']) && is_numeric($_POST['each'])) ? $_POST['each'] : ''),
                    'eachtime' => ((isset($_POST['eachtime']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $_POST['eachtime'])) ? $_POST['eachtime'] : ''),
                    'lastrun' => $_SESSION['cronjobs']['jobs'][$id]['lastrun'],
                    'runned' => $_SESSION['cronjobs']['jobs'][$id]['runned'],
                    'savelog' => (isset($_POST['savelog']) ? true : false),
                    'maillog' => (isset($_POST['maillog']) ? true : false),
                    'maillogaddress' => ((isset($_POST['maillogaddress']) && filter_var($_POST['maillogaddress'], FILTER_VALIDATE_EMAIL)) ? $_POST['maillogaddress'] : ''));

                updateCronjobs($id);
            } else {
                $_SESSION['errors'][] = 'Current URL is not correct, must contact http(s):// and a path';
                $app->flash('error_message', _t('Current URL is not correct; must begin with http(s):// and followed with a path.'));
            }

            redirect($app->req->server['HTTP_REFERER']);
        }

        $data = $_SESSION['cronjobs']['jobs'][$id];

        $app->view->display('cron/view', [
            'title' => 'View Cronjob Handler',
            'cssArray' => $css,
            'jsArray' => $js,
            'data' => $data,
            'id' => $id
            ]
        );
    });

    $app->match('GET|POST', '/log/', function () use($app) {
        if ($app->req->isPost()) {
            $app->flash('success_message', _t('Cronjob log cleaned.'));
            file_put_contents(cronDir() . 'cron/logs/cronjobs.log', '');

            redirect($app->req->server['HTTP_REFERER']);
        }

        $app->view->display('cron/log');
    });

    $app->get('/cronjob/', function () use($app, $email) {
        $cronDir = cronDir() . 'cron/';
        if (file_exists($cronDir . 'cronjobs.dat.php')) {
            $cronjobs = getCronjobs();

            if (!isset($_GET['password']) && !isset($argv[1])) {
                saveLogs('No cronjob password found');
                die(htmlspecialchars('No cronjob password found, use cronjob.php?password=<yourpassword> or full path to cronjob.php <yourpassword>'));
            } elseif (isset($_GET['password']) && $_GET['password'] != $cronjobs['settings']['cronjobpassword']) {
                saveLogs('Invalid $_GET password');
                die('Invalid $_GET password');
            } elseif (isset($argv[0]) && (substr($argv[1], 0, 8) != 'password' OR substr($argv[1], 9) != $cronjobs['settings']['cronjobpassword'])) {
                saveLogs('Invalid argument password (password=yourpassword)');
                die('Invalid argument password (password=password)');
            }

            if (isset($cronjobs['run']) && $cronjobs['run'] == true) {
                die('Cronjob already running');
            }

            $cronjobs['run'] = true;
            saveCronjobs($cronjobs);

            if (isset($cronjobs['jobs']) && is_array($cronjobs['jobs']) && count($cronjobs['jobs']) > 0) {
                // execute only one job and then exit
                foreach ($cronjobs['jobs'] as $k => $cronjob) {

                    if (isset($_GET['id']) && $k == $_GET['id']) {
                        $run = true;
                    } else {
                        $run = false;
                        if (isset($cronjob['time']) && $cronjob['time'] != '') {
                            // voer alleen uit als tijd ouder is dan vandaag 16.00 uur, maar pas na 16.00 uur
                            if (substr($cronjob['lastrun'], 0, 10) != date('Y-m-d')) {
                                if (strtotime(date('Y-m-d H:i')) > strtotime(date('Y-m-d ') . $cronjob['time'])) {
                                    $run = true;
                                }
                            }
                        } elseif (isset($cronjob['each']) && $cronjob['each'] > 0) {
                            if (strtotime($cronjob['lastrun']) + $cronjob['each'] < strtotime("now")) {
                                $run = true;
                                // if time set, daily after time...
                                if ($cronjob['each'] > (60 * 60 * 24) &&
                                    isset($cronjob['eachtime']) &&
                                    strlen($cronjob['eachtime']) == 5 &&
                                    strtotime(date('Y-m-d H:i')) < strtotime(date('Y-m-d') . $cronjob['eachtime'])) {
                                    // only run 'today' at or after give time.
                                    $run = false;
                                }
                            }
                        } elseif (substr($cronjob['lastrun'], 0, 10) != date('Y-m-d')) {
                            $run = true;
                        }
                    }

                    if ($run == true) {
                        // save as executed
                        echo 'Running: ' . $cronjobs['jobs'][$k]['url'] . PHP_EOL;

                        $cronjobs['jobs'][$k]['lastrun'] = date('Y-m-d H:i:s');
                        $cronjobs['jobs'][$k]['runned'] ++;

                        saveCronjobs($cronjobs);

                        saveLogs($cronjob['url']);

                        echo 'Connecting to cronjob' . PHP_EOL;

                        // execute cronjob
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $cronjob['url']);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, (isset($cronjons['settings'], $cronjobs['settings']['timeout']) ? $cronjob['settings']['timeout'] : 5));

                        $data = curl_exec($ch);

                        $cronjobs = getCronjobs();
                        if (curl_errno($ch)) {
                            echo 'Cronjob error: ' . curl_error($ch) . PHP_EOL;

                            saveLogs($cronjob['url'] . ' - Error: ' . curl_error($ch));
                        } else {
                            echo 'Cronjob data loaded' . PHP_EOL;

                            if (isset($cronjob['savelog']) && $cronjob['savelog'] == true) {
                                if (!is_dir($cronDir . 'logs')) {
                                    mkdir($cronDir . 'logs');
                                }

                                if (is_dir($cronDir . 'logs')) {
                                    echo 'Cronjob save log' . PHP_EOL;
                                    file_put_contents($cronDir . 'logs/' . date('Y-m-d-H-i-s') . '-' . preg_replace('/[^A-Za-z0-9 ]/', '', $cronjob['url']) . '.log', $data);
                                }
                            }

                            if (isset($cronjob['maillog'], $cronjob['maillogaddress']) && $cronjob['maillog'] == true && filter_var($cronjob['maillogaddress'], FILTER_VALIDATE_EMAIL)) {
                                echo 'Cronjob mail to client: ' . $cronjob['maillogaddress'] . PHP_EOL;

                                $random_hash = md5(date('r', time()));
                                $headers = 'From: ' . $cronjob['maillogaddress'] . "\r\nReply-To: " . $cronjob['maillogaddress'];
                                $headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-" . $random_hash . "\"";
                                $attachment = chunk_split(base64_encode($data));

                                ob_start();

                                ?> 
                                --PHP-mixed-<?php echo $random_hash; ?>  
                                Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>" 

                                --PHP-alt-<?php echo $random_hash; ?>  
                                Content-Type: text/plain; charset="iso-8859-1" 
                                Content-Transfer-Encoding: 7bit

                                Attatched is your log file from running the cronjob "<?php echo htmlspecialchars($cronjob['url']); ?>" on <?php echo $cronjob['lastrun']; ?> 

                                --PHP-alt-<?php echo $random_hash; ?>  
                                Content-Type: text/html; charset="UTF-8" 
                                Content-Transfer-Encoding: 7bit

                                <p>Attatched is your log file from running the cronjob "<strong><?php echo htmlspecialchars($cronjob['url']); ?></strong>" on </strong><?php echo $cronjob['lastrun']; ?></strong</p>

                                --PHP-alt-<?php echo $random_hash; ?>-- 

                                --PHP-mixed-<?php echo $random_hash; ?>  
                                Content-Type: application/zip; name="<?php echo date("Y-m-d-H-i-s") . '-' . preg_replace("/[^A-Za-z0-9 ]/", '', $cronjob['url']) . '.log'; ?>"  
                                Content-Transfer-Encoding: base64  
                                Content-Disposition: attachment  

                                <?php echo $attachment; ?> 
                                --PHP-mixed-<?php echo $random_hash; ?>-- 

                                <?php
                                $message = ob_get_clean();
                                $mail_sent = $email->et_mail($cronjob['maillogaddress'], 'Cronjob log ' . date('Y-m-d H:i:s') . ' for ' . htmlspecialchars($cronjob['url']), $message, $headers);

                                saveLogs($mail_sent ? 'Mail sent' : 'Mail failed');
                            }
                        }

                        curl_close($ch);
                    }

                    // update cronjob list as the user can change stuff...
                    $cronjobs = getCronjobs();
                }
            }

            $cronjobs['run'] = false;
            saveCronjobs($cronjobs);
        } else {
            die('cronjob database file not found...');
        }
    });

    $app->get('/activityLog/', function () use($logger) {
        $logger->purgeLog();
    });

    $app->get('/runStuTerms/', function () use($app) {
        /**
         * Select all records from the stu_acad_cred table.
         */
        $terms = $app->db->stu_acad_cred()
            ->select('stuID,courseSecCode,termCode,acadLevelCode,SUM(attCred)')
            ->groupBy('stuID,termCode,acadLevelCode');
        $q = $terms->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        if (count($q) > 0) {
            /**
             * If a student ID exists in the stu_acad_cred table, 
             * but does not exist in the stu_term table, then insert 
             * that new record into the stu_term table.
             */
            $app->db->query(
                "INSERT IGNORE INTO stu_term (stuID,termCode,termCredits,acadLevelCode) 
				SELECT stuID,termCode,SUM(attCred),acadLevelCode FROM stu_acad_cred 
				GROUP BY stuID,termCode,acadLevelCode"
            );
        }
    });

    $app->get('/runStuLoad/', function () use($app) {
        $terms = $app->db->stu_term()
            ->select('stuID,termCode,acadLevelCode,termCredits');
        $q = $terms->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        if (count($q) > 0) {
            foreach ($q as $r) {
                $ins = $app->db->stu_term_load();
                $ins->stuID = _h($r['stuID']);
                $ins->termCode = _h($r['termCode']);
                $ins->stuLoad = getstudentload(_h($r['termCode']), _h($r['termCredits']), _h($r['acadLevelCode']));
                $ins->acadLevelCode = _h($r['acadLevelCode']);
                $ins->save();
            }
        }
    });

    $app->get('/runEmailHold/', function () use($app) {
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
        $q = $email->find(function($data) {
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

        $query = $r['savedQuery'];
        $hold1 = $app->db->query($query);
        $hold2 = $app->db->email_queue()->where('holdID = ?', _h($r['id']))->_and_()->where('sent = "0"');
        $q2 = $hold2->find(function($data) {
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
        if (count($r['fromEmail']) > 0) {
            if (count($q2) <= 0) {
                $q = $hold1->find(function($data) {
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
                    $update->where('id = ?', _h($r['id']))->update();
                    $app->cookies->remove('email_hold' . _h($r['id']));
                    session_destroy();
                }
            }
        }
    });

    $app->get('/runEmailQueue/', function () use($app, $emailer) {
        $queue = $app->db->email_queue()->where('sent = "0"');
        $queue->find(function($data) use($app, $emailer) {
            foreach ($data as $d) {
                if (has_action('etMailer_init', 'et_smtp')) {
                    $emailer->IsSMTP();
                    $emailer->Mailer = "smtp";
                    $emailer->Host = _h(get_option('et_smtp_host'));
                    $emailer->SMTPSecure = _h(get_option('et_smtp_smtpsecure'));
                    $emailer->Port = _h(get_option('et_smtp_port'));
                    $emailer->SMTPAuth = (_h(get_option("et_smtp_smtpauth")) == "yes") ? TRUE : FALSE;
                    if ($emailer->SMTPAuth) {
                        $emailer->Username = _h(get_option('et_smtp_username'));
                        $emailer->Password = _h(get_option('et_smtp_password'));
                    }
                    $emailer->AddAddress($d['email'], $d['lname'] . ', ' . $d['fname']);
                    $emailer->From = $d['fromEmail'];
                    $emailer->FromName = $d['fromName'];
                    $emailer->Sender = $emailer->From; //Return-Path
                    $emailer->AddReplyTo($emailer->From, $emailer->FromName); //Reply-To
                    $emailer->IsHTML(true);
                    $emailer->Subject = $d['subject'];
                    $emailer->Body = $d['body'];
                    $emailer->Send();
                    $emailer->ClearAddresses();
                    $emailer->ClearAttachments();
                } else {
                    $emailer->AddAddress($d['email'], $d['lname'] . ', ' . $d['fname']);
                    $emailer->From = $d['fromEmail'];
                    $emailer->FromName = $d['fromName'];
                    $emailer->Sender = $emailer->From; //Return-Path
                    $emailer->AddReplyTo($emailer->From, $emailer->FromName); //Reply-To
                    $emailer->IsHTML(true);
                    $emailer->Subject = $d['subject'];
                    $emailer->Body = $d['body'];
                    $emailer->Send();
                    $emailer->ClearAddresses();
                    $emailer->ClearAttachments();
                }
                $u = $app->db->email_queue();
                $u->sent = 1;
                $u->sentDate = $app->db->NOW();
                $u->where('id = ?', $d['id']);
                $u->update();
            }
        });
    });

    $app->get('/purgeEmailHold/', function () use($app) {
        $app->db->email_hold()
            ->where('processed = "1"')->_and_()
            ->where('DATE_ADD(dateTime, INTERVAL 15 DAY) <= ?', date('Y-m-d'))
            ->delete();
    });

    $app->get('/purgeEmailHold/', function () use($app) {
        $app->db->email_queue()
            ->where('sent = "1"')
            ->delete();
    });

    $app->get('/updateStuTerms/', function () use($app) {
        $terms = $app->db->query("SELECT 
                    SUM(a.attCred) as Credits,
                    a.stuID as stuAcadCredID,
                    a.termCode as termAcadCredCode,
                    a.acadLevelCode as acadCredLevel,
                    b.stuID AS stuTermID,
                    b.termCode AS TermCode,
                    b.acadLevelCode as termAcadLevel,
                    b.termCredits AS TermCredits 
                FROM 
                    stu_acad_cred a 
                LEFT JOIN 
                    stu_term b 
                ON 
                    a.stuID = b.stuID 
                WHERE 
                    a.termCode = b.termCode 
                AND 
                    a.acadLevelCode = b.acadLevelCode 
                GROUP BY 
                    a.stuID,a.termCode"
        );
        $q = $terms->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        foreach ($q as $r) {
            if ($r['Credits'] != $r['TermCredits']) {
                $q2 = $app->db->stu_term();
                $q2->termCredits = $r['Credits'];
                $q2->where('stuID = ?', $r['stuTermID'])->_and_()
                    ->where('termCode = ?', $r['TermCode'])->_and_()
                    ->where('acadLevelCode = ?', $r['termAcadLevel']);
                $q2->update();
            }
        }
    });

    $app->get('/updateStuLoad/', function () use($app) {
        $load = $app->db->query("SELECT 
                    a.termCredits,
                    a.stuID AS StudentID,
                    a.termCode AS TermCode,
                    a.acadLevelCode AS AcademicLevel,
                    a.LastUpdate AS termLatest,
                    b.LastUpdate AS stuTermLatest 
                FROM 
                    stu_term a 
                LEFT JOIN 
                    stu_term_load b 
                ON 
                    a.stuID = b.stuID 
                WHERE 
                    a.termCode = b.termCode 
                AND 
                    a.acadLevelCode = b.acadLevelCode"
        );
        $q = $load->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        foreach ($q as $r) {
            if ($r['termLatest'] > $r['stuTermLatest']) {
                $q2 = $app->db->stu_term_load();
                $q2->stuLoad = getstudentload(_h($r['TermCode']), _h($r['termCredits']), _h($r['AcademicLevel']));
                $q2->where('stuID = ?', $r['StudentID'])->_and_()
                    ->where('termCode = ?', $r['TermCode'])->_and_()
                    ->where('acadLevelCode = ?', $r['AcademicLevel']);
                $q2->update();
            }
        }
    });

    $app->get('/runGraduation/', function () use($app) {
        $hold = $app->db->graduation_hold()
            ->select('graduation_hold.id,graduation_hold.queryID,graduation_hold.gradDate,b.savedQuery')
            ->_join('saved_query', 'graduation_hold.queryID = b.savedQueryID', 'b');
        $q1 = $hold->find(function($data) {
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
            $sq = $app->db->query($r1['savedQuery']);
            $q2 = $sq->find(function($data) {
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
                $prog->where('stuID = ?', $r2['stuID'])->update();
            }
        }
        /* Delete records from graduation_hold after above queries have been processed. */
        $app->db->query("TRUNCATE graduation_hold");
    });

    $app->get('/runTermGPA/', function () use($app) {
        $gs = $app->db->grade_scale()
            ->select('b.stuID,b.termCode,b.acadLevelCode,SUM(b.attCred) AS Attempted,')
            ->select('SUM(b.compCred) AS Completed,SUM(b.gradePoints) AS Points')
            ->select('SUM(b.gradePoints)/SUM(b.attCred) AS GPA')
            ->_join('stu_acad_cred', 'grade_scale.grade = b.grade', 'b')
            ->where('grade_scale.count_in_gpa = "1"')->_and_()
            ->where('grade_scale.status = "1"')->_and_()
            ->where('b.grade <> "NULL"')
            ->groupBy('b.stuID,b.termCode,b.acadLevelCode');
        $q = $gs->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        if (count($q) > 0) {
            foreach ($q as $r) {

                $ins = $app->db->stu_term_gpa();
                $ins->stuID = _h($r['stuID']);
                $ins->termCode = _h($r['termCode']);
                $ins->acadLevelCode = _h($r['acadLevelCode']);
                $ins->attCred = _h($r['Attempted']);
                $ins->compCred = _h($r['Completed']);
                $ins->gradePoints = _h($r['Points']);
                $ins->termGPA = _h($r['GPA']);
                $ins->save();
            }
        }
    });

    $app->get('/updateTermGPA/', function () use($app) {
        $gpa = $app->db->query("SELECT 
                    a.stuID,
                    a.termCode,
                    a.acadLevelCode,
                    a.termGPA,
                    a.gradePoints AS termGradePoints,
                    SUM(b.attCred) AS Attempted,
                    SUM(b.compCred) AS Completed,
                    SUM(b.gradePoints) AS stacGradePoints 
                FROM 
                    stu_term_gpa a 
                LEFT JOIN 
                    stu_acad_cred b 
                ON 
                    a.stuID = b.stuID 
                LEFT JOIN 
                	grade_scale c 
            	ON 
            		b.grade = c.grade 
                WHERE 
                    a.termCode = b.termCode 
                AND 
                	b.grade <> 'NULL' 
        		AND 
        			c.count_in_gpa = '1' 
    			AND 
    				c.status = '1' 
                AND 
                    a.acadLevelCode = b.acadLevelCode 
                GROUP BY 
                	a.stuID,a.termCode,a.acadLevelCode"
        );
        $q = $gpa->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        foreach ($q as $r) {
            $GPA = $r['stacGradePoints'] / $r['Attempted'];
            if ($r['termGradePoints'] != $r['stacGradePoints'] || $r['termGPA'] != $GPA) {
                $q2 = $app->db->stu_term_gpa();
                $q2->attCred = $r['Attempted'];
                $q2->compCred = $r['Completed'];
                $q2->gradePoints = $r['termGradePoints'];
                $q2->termGPA = $GPA;
                $q2->where('stuID = ?', $r['stuID'])->_and_()
                    ->where('termCode = ?', $r['termCode'])->_and_()
                    ->where('acadLevelCode = ?', $r['acadLevelCode']);
                $q2->update();
            }
        }
    });

    $app->get('/purgeErrorLog/', function () use($app) {
        $app->db->error()
            ->where('DATE_ADD(addDate, INTERVAL 5 DAY) <= ?', date('Y-m-d'))
            ->delete();
    });

    $app->get('/purgeSavedQuery/', function () use($app) {
        $app->db->saved_query()
            ->where('DATE_ADD(createdDate, INTERVAL 30 DAY) <= ?', date('Y-m-d'))->_and_()
            ->where('purgeQuery = "1"')
            ->delete();
    });

    $app->get('/checkStuBalance/', function () use($app) {
        $bal = $app->db->query(
            "SELECT pay.stuID,pay.termCode,COALESCE(Fees,0)+COALESCE(Tuition,0) as Fees,COALESCE(SUM(pay.amount),0) AS Payments,COALESCE(Fees,0)+COALESCE(Tuition,0)+COALESCE(SUM(pay.amount),0) AS Balance
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
             GROUP BY pay.stuID,pay.termCode"
        );
        $q = $bal->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        foreach ($q as $r) {
            if ($r['Balance'] >= 0) {
                $result = $app->db->stu_acct_bill();
                $result->balanceDue = '0';
                $result->where('stuID = ?', $r['stuID'])->_and_()
                    ->where('termCode = ?', $r['termCode'])
                    ->update();
            } elseif($r['Balance'] < 0) {
                $result = $app->db->stu_acct_bill();
                $result->balanceDue = '1';
                $result->where('stuID = ?', $r['stuID'])->_and_()
                    ->where('termCode = ?', $r['termCode'])
                    ->update();
            }
        }
    });

    $app->get('/runDBBackup/', function () {
        $dbhost = DB_HOST;
        $dbuser = DB_USER;
        $dbpass = DB_PASS;
        $dbname = DB_NAME;
        _mkdir('/tmp/' . subdomain_as_directory() . '/backups/');
        $backupDir = '/tmp/' . subdomain_as_directory() . '/backups/';
        $backupFile = $backupDir . $dbname . '-' . date("Y-m-d-H-i-s") . '.gz';
        if (!file_exists($backupFile)) {
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
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

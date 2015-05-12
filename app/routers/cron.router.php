<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

$json_url = url('/connect/');

$logger = new \app\src\Log();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();
$emailer = new \app\src\PHPMailer;

/**
 * Before route checks to make sure the logged in user
 * us allowed to manage options/settings.
 */
$app->before('GET', '/cron(.*)', function() {
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

$app->group('/cron', function() use($app, $css, $js, $logger, $emailer) {

    $app->get('/', function () use($app, $css, $js) {

        $cron = $app->db->cronjob();
        $q = $cron->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('cron/index', [
            'title' => 'Cronjobs',
            'cssArray' => $css,
            'jsArray' => $js,
            'cronjob' => $q
            ]
        );
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js) {

        $cron = $app->db->cronjob()->where('id = ?', $id);
        $q = $cron->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        if ($app->req->isPost()) {
            if ($_POST['minutes'] > 0)
                $time_interval = $_POST['minutes'] * 60;
            elseif ($_POST['hours'] > 0)
                $time_interval = $_POST['hours'] * 3600;
            elseif ($_POST['days'] > 0)
                $time_interval = $_POST['days'] * 86400;
            else
                $time_interval = $_POST['weeks'] * 604800;

            $_POST['time_last_fired'] = ($_POST['time_last_fired']) ? $_POST['time_last_fired'] : time();
            $fire_time = $_POST['time_last_fired'] + $time_interval;

            $cron = $app->db->cronjob();
            $cron->name = $app->req->_post('name');
            $cron->scriptpath = $app->req->_post('scriptpath');
            $cron->time_interval = $time_interval;
            $cron->fire_time = $fire_time;
            $cron->run_only_once = $app->req->_post('run_only_once');
            $cron->where('id = ?', $app->req->_post('id'))->update();
            redirect($app->req->server['HTTP_REFERER']);
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($q == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($q) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('cron/view', [
                'title' => 'Cronjobs',
                'cssArray' => $css,
                'jsArray' => $js,
                'cronjob' => $q
                ]
            );
        }
    });

    $app->get('/fireCron/', function () use($app) {
        if (isset($_GET['image'])) {
            header("Content-Type: image/gif");
            header("Content-Length: 49");
            echo pack('H*', '47494638396101000100910000000000ffffffffffff00000021f90405140002002c00000000010001000002025401003b');
        }

        $app->view->display('cron/fire-cron');
    });

    $app->get('/activityLog/', function () use($app, $logger) {
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
                    $body = $r['body'];
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
                if ($app->hook->{'has_action'}('etMailer_init', 'et_smtp')) {
                    $emailer->IsSMTP();
                    $emailer->Mailer = "smtp";
                    $emailer->Host = _h($app->hook->{'get_option'}('et_smtp_host'));
                    $emailer->SMTPSecure = _h($app->hook->{'get_option'}('et_smtp_smtpsecure'));
                    $emailer->Port = _h($app->hook->{'get_option'}('et_smtp_port'));
                    $emailer->SMTPAuth = (_h($app->hook->{'get_option'}("et_smtp_smtpauth")) == "yes") ? TRUE : FALSE;
                    if ($emailer->SMTPAuth) {
                        $emailer->Username = _h($app->hook->{'get_option'}('et_smtp_username'));
                        $emailer->Password = _h($app->hook->{'get_option'}('et_smtp_password'));
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

    $app->get('/runDBBackup/', function () use($app) {
        $dbhost = DB_HOST;
        $dbuser = DB_USER;
        $dbpass = DB_PASS;
        $dbname = DB_NAME;
        $backupFile = APP_PATH . '_HOLD_' . DS . $dbname . '-' . date("Y-m-d-H-i-s") . '.gz';
        if (!file_exists($backupFile)) {
            $command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $dbname | gzip > $backupFile";
            system($command);
        }
        $files = glob(APP_PATH . '_HOLD_' . DS . "*.gz");
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file) && time() - filemtime($file) >= 20 * 24 * 3600) { // 20 days
                    unlink($file);
                }
            }
        }
    });
});

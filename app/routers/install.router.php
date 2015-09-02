<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use \app\src\Session;

Session::init();

/**
 * Install Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

/**
 * Before route check.
 */
$app->before('GET|POST', '/install.*', function() use($app) {
    if (file_exists(BASE_PATH . 'config.php')) {
        redirect(url('/'));
    }
    
    if(!$app->req->_get('step')) {
        redirect(url('/install/?step=1'));
    }

    if ($app->req->_get('step') === '') {
        redirect(url('/install/?step=1'));
    }
});

$app->get('/install/', function () use($app) {

    $app->view->display('install/index');
});

$app->match('GET|POST', '/install/checkDB/', function () use($app) {

    if ($app->req->isPost()) {
        Session::set('dbhost', $app->req->_post('dbhost'));
        Session::set('dbuser', $app->req->_post('dbuser'));
        Session::set('dbpass', $app->req->_post('dbpass'));
        Session::set('dbname', $app->req->_post('dbname'));

        try {
            $connect = new \PDO("mysql:host=" . Session::get('dbhost') . ";dbname=" . Session::get('dbname'), Session::get('dbuser'), Session::get('dbpass'));
            $connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $connect->prepare('SET NAMES utf8');
            $connect->prepare('SET CHARACTER SET utf8');
        } catch (\PDOException $e) {
            $error = 'ERROR: ' . $e->getMessage();
            return $error;
        }

        $_SESSION['error_message'] = [];
        if (!$connect) {
            $_SESSION['error_message'][] = _t('Unable to establish a database connection.');
            redirect(url('/install/?step=3'));
        } else {
            redirect(url('/install/?step=4'));
        }
    }
});

$app->match('GET|POST', '/install/installData/', function () use($app) {

    if ($app->req->isPost()) {
        try {
            $connect = new \PDO("mysql:host=" . Session::get('dbhost') . ";dbname=" . Session::get('dbname'), Session::get('dbuser'), Session::get('dbpass'));
            $connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $connect->prepare('SET NAMES utf8');
            $connect->prepare('SET CHARACTER SET utf8');
        } catch (\PDOException $e) {
            $error = 'ERROR: ' . $e->getMessage();
            return $error;
        }

        $_SESSION['error_message'] = [];
        if ($connect) {
            $q = _file_get_contents(APP_PATH . 'views/install/data/install.sql');
            $connect->exec($q);
            redirect(url('/install/?step=5'));
        } else {
            $_SESSION['error_message'][] = _t('Unable to establish a database connection.');
            redirect(url('/install/?step=3'));
        }
    }
});

$app->match('GET|POST', '/install/createAdmin/', function () use($app) {

    if ($app->req->isPost()) {
        $now = date('Y-m-d h:m:s');
        Session::set('instname', $app->req->_post('institutionname'));
        Session::set('uname', $app->req->_post('uname'));
        Session::set('fname', $app->req->_post('fname'));
        Session::set('lname', $app->req->_post('lname'));
        Session::set('password', et_hash_password($app->req->_post('password')));
        Session::set('email', $app->req->_post('email'));
        Session::set('apikey', \app\src\ID::code(20));

        try {
            $connect = new \PDO("mysql:host=" . Session::get('dbhost') . ";dbname=" . Session::get('dbname'), Session::get('dbuser'), Session::get('dbpass'));
            $connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $connect->prepare('SET NAMES utf8');
            $connect->prepare('SET CHARACTER SET utf8');
        } catch (\PDOException $e) {
            $error = 'ERROR: ' . $e->getMessage();
            return $error;
        }

        if ($connect) {
            $sql = [];

            $sql[] = "INSERT INTO `person` (`personID`, `uname`, `password`, `fname`, `lname`, `email`,`personType`,`approvedDate`,`approvedBy`) VALUES ('', '" . Session::get('uname') . "', '" . Session::get('password') . "', '" . Session::get('fname') . "', '" . Session::get('lname') . "', '" . Session::get('email') . "', 'STA', '" . $now . "', '1');";

            $sql[] = "INSERT INTO `person_roles` VALUES(1, 1, 8, '" . $now . "');";

            $sql[] = "INSERT INTO `staff` VALUES(1, 1, 'NULL', 'NULL', 'NULL', '', 'NULL', 'A', '" . $now . "', 1, '" . $now . "');";

            $sql[] = "INSERT INTO `address` VALUES(00000000001, 00000001, '10 Eliot Street', '#2', 'Somerville', 'MA', '02143', 'US', 'P', '" . $now . "', '0000-00-00', 'C', '6718997836', '', '', '', 'CEL', '', 'support@edutrac.org', '', '" . $now . "', 00000001, '" . $now . "');";

            $sql[] = "INSERT INTO `job` VALUES(1, 1, 'IT Support', '34.00', 40, NULL, '" . $now . "', 00000001, '" . $now . "');";

            $sql[] = "INSERT INTO `staff_meta` VALUES(1, 'FT', 1, 00000001, 00000001, 'STA', '2011-02-01', '2011-02-01', NULL, '" . $now . "', 00000001, '" . $now . "');";

            $sql[] = "INSERT INTO `options_meta` VALUES(1, 'dbversion', '00048');";

            $sql[] = "INSERT INTO `options_meta` VALUES(2, 'system_email', '" . Session::get('email') . "');";

            $sql[] = "INSERT INTO `options_meta` VALUES(3, 'enable_ssl', '0');";

            $sql[] = "INSERT INTO `options_meta` VALUES(4, 'institution_name', '" . Session::get('instname') . "');";

            $sql[] = "INSERT INTO `options_meta` VALUES(5, 'cookieexpire', '604800');";

            $sql[] = "INSERT INTO `options_meta` VALUES(6, 'cookiepath', '/');";

            $sql[] = "INSERT INTO `options_meta` VALUES(7, 'enable_benchmark', '0');";

            $sql[] = "INSERT INTO `options_meta` VALUES(8, 'maintenance_mode', '0');";

            $sql[] = "INSERT INTO `options_meta` VALUES(9, 'current_term_code', '');";

            $sql[] = "INSERT INTO `options_meta` VALUES(10, 'open_registration', '1');";

            $sql[] = "INSERT INTO `options_meta` VALUES(11, 'help_desk', 'http://www.edutracsis.com/');";

            $sql[] = "INSERT INTO `options_meta` VALUES(12, 'reset_password_text', '<b>eduTrac Password Reset</b><br>Password &amp; Login Information<br><br>You or someone else requested a new password to the eduTrac online system. If you did not request this change, please contact the administrator as soon as possible @ #adminemail#.&nbsp; To log into the eduTrac system, please visit #url# and login with your username and password.<br><br>FULL NAME:&nbsp; #fname# #lname#<br>USERNAME:&nbsp; #uname#<br>PASSWORD:&nbsp; #password#<br><br>If you need further assistance, please read the documentation at #helpdesk#.<br><br>KEEP THIS IN A SAFE AND SECURE LOCATION.<br><br>Thank You,<br>eduTrac Web Team<br>');";

            $sql[] = "INSERT INTO `options_meta` VALUES(13, 'api_key', '" . Session::get('apikey') . "');";

            $sql[] = "INSERT INTO `options_meta` VALUES(14, 'room_request_email', 'request@myschool.edu');";

            $sql[] = "INSERT INTO `options_meta` VALUES(15, 'room_request_text', '<p>&nbsp;</p>\r\n<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#F4F3F4\">\r\n<tbody>\r\n<tr>\r\n<td style=\"padding: 15px;\"><center>\r\n<table width=\"550\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td align=\"left\">\r\n<div style=\"border: solid 1px #d9d9d9;\">\r\n<table id=\"header\" style=\"line-height: 1.6; font-size: 12px; font-family: Helvetica, Arial, sans-serif; border: solid 1px #FFFFFF; color: #444;\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" valign=\"bottom\" height=\"30\">.</td>\r\n</tr>\r\n<tr>\r\n<td style=\"line-height: 32px; padding-left: 30px;\" valign=\"baseline\"><span style=\"font-size: 32px;\">eduTrac SIS</span></td>\r\n<td style=\"padding-right: 30px;\" align=\"right\" valign=\"baseline\"><span style=\"font-size: 14px; color: #777777;\">Room/Event Reservation Request</span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"content\" style=\"margin-top: 15px; margin-right: 30px; margin-left: 30px; color: #444; line-height: 1.6; font-size: 12px; font-family: Arial, sans-serif;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding: 15px 0;\">Below are the details of a new room request.</div>\r\n<div style=\"padding: 15px 0;\"><strong>Name:</strong> #name#<br /><br /><strong>Email:</strong> #email#<br /><br /><strong>Event Title:</strong> #title#<br /><strong>Description:</strong> #description#<br /><strong>Request Type:</strong> #request_type#<br /><strong>Category:</strong> #category#<br /><strong>Room#:</strong> #room#<br /><strong>Start Date:</strong> #firstday#<br /><strong>End Date:</strong> #lastday#<br /><strong>Start Time:</strong> #sTime#<br /><strong>End Time:</strong> #eTime#<br /><strong>Repeat?:</strong> #repeat#<br /><strong>Occurrence:</strong> #occurrence#<br /><br /><br />\r\n<h3>Legend</h3>\r\n<ul>\r\n<li>Repeat - 1 means yes it is an event that is repeated</li>\r\n<li>Occurrence - 1 = repeats everyday, 7 = repeats weekly, 14 = repeats biweekly</li>\r\n</ul>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"footer\" style=\"line-height: 1.5; font-size: 12px; font-family: Arial, sans-serif; margin-right: 30px; margin-left: 30px;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr style=\"font-size: 11px; color: #999999;\">\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding-top: 15px; padding-bottom: 1px;\">Powered by eduTrac SIS</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" height=\"15\">.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</center></td>\r\n</tr>\r\n</tbody>\r\n</table>');";

            $sql[] = "INSERT INTO `options_meta` VALUES(16, 'room_booking_confirmation_text', '<p>&nbsp;</p>\r\n<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#F4F3F4\">\r\n<tbody>\r\n<tr>\r\n<td style=\"padding: 15px;\"><center>\r\n<table width=\"550\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td align=\"left\">\r\n<div style=\"border: solid 1px #d9d9d9;\">\r\n<table id=\"header\" style=\"line-height: 1.6; font-size: 12px; font-family: Helvetica, Arial, sans-serif; border: solid 1px #FFFFFF; color: #444;\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" valign=\"bottom\" height=\"30\">.</td>\r\n</tr>\r\n<tr>\r\n<td style=\"line-height: 32px; padding-left: 30px;\" valign=\"baseline\"><span style=\"font-size: 32px;\">eduTrac SIS</span></td>\r\n<td style=\"padding-right: 30px;\" align=\"right\" valign=\"baseline\"><span style=\"font-size: 14px; color: #777777;\">Room/Event&nbsp;Booking&nbsp;Confirmation</span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"content\" style=\"margin-top: 15px; margin-right: 30px; margin-left: 30px; color: #444; line-height: 1.6; font-size: 12px; font-family: Arial, sans-serif;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding: 15px 0;\">Your room request or event request entitled <strong>#title#</strong> has been booked. If you have any questions or concerns, please email our office at <a href=\"mailto:request@bdci.edu\">request@bdci.edu</a></div>\r\n<div style=\"padding: 15px 0;\">Sincerely,<br />Room Scheduler</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"footer\" style=\"line-height: 1.5; font-size: 12px; font-family: Arial, sans-serif; margin-right: 30px; margin-left: 30px;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr style=\"font-size: 11px; color: #999999;\">\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding-top: 15px; padding-bottom: 1px;\">Powered by eduTrac SIS</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" height=\"15\">.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</center></td>\r\n</tr>\r\n</tbody>\r\n</table>');";

            $sql[] = "INSERT INTO `options_meta` VALUES(17, 'myet_welcome_message', '<p>Welcome to the <em>my</em>eduTrac campus portal. The <em>my</em>eduTrac campus portal&nbsp;is your personalized campus web site at Eastbound University.</p>\r\n<p>If you are a prospective student who is interested in applying to the college, checkout the <a href=\"pages/?pg=admissions\">admissions</a>&nbsp;page for more information.</p>');";

            $sql[] = "INSERT INTO `options_meta` VALUES(18, 'contact_phone', '888.888.8888');";

            $sql[] = "INSERT INTO `options_meta` VALUES(19, 'contact_email', 'contact@colegio.edu');";

            $sql[] = "INSERT INTO `options_meta` VALUES(20, 'mailing_address', '10 Eliot Street, Suite 2\r\nSomerville, MA 02140');";

            $sql[] = "INSERT INTO `options_meta` VALUES(21, 'enable_myet_portal', '0');";

            $sql[] = "INSERT INTO `options_meta` VALUES(22, 'screen_caching', '1');";

            $sql[] = "INSERT INTO `options_meta` VALUES(23, 'db_caching', '1');";

            $sql[] = "INSERT INTO `options_meta` VALUES(24, 'admissions_email', 'admissions@colegio.edu');";

            $sql[] = "INSERT INTO `options_meta` VALUES(25, 'coa_form_text', '<p>Dear Admin,</p>\r\n<p>#name# has submitted a change of address. Please see below for details.</p>\r\n<p><strong>ID:</strong> #id#</p>\r\n<p><strong>Address1:</strong> #address1#</p>\r\n<p><strong>Address2:</strong> #address2#</p>\r\n<p><strong>City:</strong> #city#</p>\r\n<p><strong>State:</strong> #state#</p>\r\n<p><strong>Zip:</strong> #zip#</p>\r\n<p><strong>Country:</strong> #country#</p>\r\n<p><strong>Phone:</strong> #phone#</p>\r\n<p><strong>Email:</strong> #email#</p>\r\n<p>&nbsp;</p>\r\n<p>----<br /><em>This is a system generated email.</em></p>');";

            $sql[] = "INSERT INTO `options_meta` VALUES(26, 'enable_myet_appl_form', '0');";

            $sql[] = "INSERT INTO `options_meta` VALUES(27, 'myet_offline_message', 'Please excuse the dust. We are giving the portal a new facelift. Please try back again in an hour.\r\n\r\nSincerely,\r\nIT Department');";

            $sql[] = "INSERT INTO `options_meta` VALUES(28, 'curl', '1');";
            
            $sql[] = "INSERT INTO `options_meta` VALUES(29, 'system_timezone', 'America/New_York');";
            
            $sql[] = "INSERT INTO `options_meta` VALUES(30, 'number_of_courses', '3');";

            $sql[] = "INSERT INTO `options_meta` VALUES(31, 'account_balance', '');";
            
            $sql[] = "INSERT INTO `options_meta` VALUES(32, 'reg_instructions', '');";

            $sql[] = "INSERT INTO `options_meta` VALUES(33, 'et_core_locale', 'en_US');";

            $sql[] = "INSERT INTO `options_meta` VALUES(34, 'send_acceptance_email', '0');";

            $sql[] = "INSERT INTO `options_meta` VALUES(35, 'person_login_details', '<p>Dear #fname#:</p>\r\n<p>An account has just been created for you. Below are your login details.</p>\r\n<p>Username: #uname#</p>\r\n<p>Password: #password#</p>\r\n<p>ID: #id#</p>\r\n<p>Alternate ID:&nbsp;#altID#</p>\r\n<p>You may log into your account at the url below:</p>\r\n<p><a href=\"#url#\">#url#</a></p>');";

            $sql[] = "INSERT INTO `options_meta` VALUES(36, 'myet_layout', 'default');";
            
            $sql[] = "INSERT INTO `options_meta` VALUES(37, 'open_terms', '\"15/FA\"');";
            
            $sql[] = "INSERT INTO `options_meta` VALUES(38, 'elfinder_driver', 'elf_local_driver');";

            foreach ($sql as $query) {
                $connect->exec($query);
            }
        }
    }
    redirect(url('/install/?step=6'));
});

$app->match('GET|POST', '/install/finishInstall/', function () use($app) {

    if ($app->req->isPost()) {
        /**
		 * If the config.php file does not exist, copy the 
		 * sample file and rename it.
		 */
    	if(!file_exists(BASE_PATH . 'config.php')) {
        	copy(BASE_PATH . 'config.sample.php',BASE_PATH . 'config.php');
        }
        $file = BASE_PATH . 'config.php';
        $config = _file_get_contents($file);
        
        $config = str_replace('{product}', 'eduTrac SIS', $config);
        $config = str_replace('{company}', '7 Media', $config);
        $config = str_replace('{version}', CURRENT_RELEASE, $config);
        $config = str_replace('{datenow}', date('Y-m-d h:m:s'), $config);
        $config = str_replace('{hostname}', Session::get('dbhost'), $config);
        $config = str_replace('{database}', Session::get('dbname'), $config);
        $config = str_replace('{username}', Session::get('dbuser'), $config);
        $config = str_replace('{password}', Session::get('dbpass'), $config);
        
        file_put_contents($file, $config);
		
		# Destroy the session
        Session::destroy();
        
        redirect(url('/'));
    }
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});

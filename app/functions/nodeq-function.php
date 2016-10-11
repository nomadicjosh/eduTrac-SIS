<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

use \app\src\Core\NodeQ\etsis_NodeQ as Node;

/**
 * eduTrac SIS NodeQ Functions
 *
 * @license GPLv3
 *         
 * @since 6.2.11
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */

/**
 * Login Details Email
 * 
 * Function used to send login details to new
 * person record.
 * 
 * @since 6.2.11
 */
function etsis_nodeq_login_details()
{
    $app = \Liten\Liten::getInstance();

    $email = _etsis_email();
    $site = _t('myeduTrac :: ') . _h(get_option('institution_name'));
    $host = $app->req->server['HTTP_HOST'];
    // Creates node's schema if does not exist.
    Node::dispense('login_details');
    
    try {
        $sql = Node::table('login_details')->where('sent','=',0)->findAll();
        
        if ($sql->count() == 0) {
            Node::table('login_details')->delete();
        }

        $numItems = $sql->count();
        $i = 0;
        if ($sql->count() > 0) {
            foreach ($sql as $r) {
                $message = _escape(get_option('person_login_details'));
                $message = str_replace('#uname#', _h($r->uname), $message);
                $message = str_replace('#fname#', _h($r->fname), $message);
                $message = str_replace('#lname#', _h($r->lname), $message);
                $message = str_replace('#name#', get_name(_h($r->personid)), $message);
                $message = str_replace('#id#', _h($r->personid), $message);
                $message = str_replace('#altID#', _h($r->altid), $message);
                $message = str_replace('#password#', _h($r->password), $message);
                $message = str_replace('#url#', get_base_url(), $message);
                $message = str_replace('#helpdesk#', _h(get_option('help_desk')), $message);
                $message = str_replace('#instname#', _h(get_option('institution_name')), $message);
                $message = str_replace('#mailaddr#', _h(get_option('mailing_address')), $message);
                $headers = "From: $site <auto-reply@$host>\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                $email->etsis_mail(_h($r->email), _t("myeduTrac Login Details"), $message, $headers);
                
                $upd = Node::table('login_details')->find(_h($r->id));
                $upd->sent = 1;
                $upd->save();

                if (++$i === $numItems) {
                    //If we reach the last item, send user a desktop notification.
                    etsis_push_notify('Login Details', 'New login details sent.');
                }
            }
        }
    } catch (\Exception $e) {
        return new \app\src\Core\Exception\Exception($e->getMessage(), 'NodeQ');
    }
}

/**
 * Reset Password Email
 * 
 * Function used to send reset password emails.
 * 
 * @since 6.2.11
 */
function etsis_nodeq_reset_password()
{
    $app = \Liten\Liten::getInstance();

    $email = _etsis_email();
    $from = _h(get_option('institution_name'));
    $host = $app->req->server['HTTP_HOST'];
    // Creates node's schema if does not exist.
    Node::dispense('reset_password');

    try {
        $sql = Node::table('reset_password')->where('sent','=',0)->findAll();
        
        if ($sql->count() == 0) {
            Node::table('reset_password')->delete();
        }

        $numItems = $sql->count();
        $i = 0;
        if ($sql->count() > 0) {
            foreach ($sql as $r) {
                $message = _escape(get_option('reset_password_text'));
                $message = str_replace('#instname#', $from, $message);
                $message = str_replace('#mailaddr#', _h(get_option('mailing_address')), $message);
                $message = str_replace('#url#', get_base_url(), $message);
                $message = str_replace('#helpdesk#', _h(get_option('help_desk')), $message);
                $message = str_replace('#adminemail#', _h(get_option('institution_name')), $message);
                $message = str_replace('#uname#', _h($r->uname), $message);
                $message = str_replace('#email#', _h($r->email), $message);
                $message = str_replace('#name#', get_name(_h($r->personid)), $message);
                $message = str_replace('#fname#', _h($r->fname), $message);
                $message = str_replace('#lname#', _h($r->lname), $message);
                $message = str_replace('#password#', _h($r->password), $message);
                $headers = "From: $from <auto-reply@$host>\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                $email->etsis_mail(_h($r->email), _t('Reset Password'), $message, $headers);
                
                $upd = Node::table('reset_password')->find(_h($r->id));
                $upd->sent = 1;
                $upd->save();

                if (++$i === $numItems) {
                    //If we reach the last item, send user a desktop notification.
                    etsis_push_notify('Reset Password', 'Reset password email sent.');
                }
            }
        }
    } catch (\Exception $e) {
        return new \app\src\Core\Exception\Exception($e->getMessage(), 'NodeQ');
    }
}

/**
 * CSV to Email
 * 
 * Function used to send .csv email reports.
 * 
 * @since 6.2.11
 */
function etsis_nodeq_csv_email()
{
    $app = \Liten\Liten::getInstance();

    $email = _etsis_email();
    $site = _h(get_option('institution_name'));

    $sitename = strtolower($app->req->server['SERVER_NAME']);
    if (substr($sitename, 0, 4) == 'www.') {
        $sitename = substr($sitename, 4);
    }
    // Creates node's schema if does not exist.
    Node::dispense('csv_email');

    try {
        $sql = Node::table('csv_email')->where('sent','=',0)->findAll();

        if ($sql->count() == 0) {
            Node::table('csv_email')->delete();
        }

        $numItems = $sql->count();
        $i = 0;
        if ($sql->count() > 0) {
            foreach ($sql as $r) {
                $headers = "From: $site <auto-reply@$sitename>\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();
                $headers .= "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                $attachment = $app->config('file.savepath') . _h($r->filename);

                $email->etsis_mail(_h($r->recipient), _h($r->subject), _escape($r->message), $headers, [$attachment]);

                $upd = Node::table('csv_email')->find(_h($r->id));
                $upd->sent = 1;
                $upd->save();
                
                unlink($attachment);

                if (++$i === $numItems) {
                    //If we reach the last item, send user a desktop notification.
                    etsis_push_notify('CSV to Email', 'Email sent.');
                }
            }
        }
    } catch (\Exception $e) {
        return new \app\src\Core\Exception\Exception($e->getMessage(), 'NodeQ');
    }
}

/**
 * Change of Address Email
 * 
 * Function used to send change of address to
 * appropriate staff member.
 * 
 * @since 6.2.11
 */
function etsis_nodeq_change_address()
{
    $app = \Liten\Liten::getInstance();

    $email = _etsis_email();
    $host = $app->req->server['HTTP_HOST'];
    $site = _t('myeduTrac :: ') . _h(get_option('institution_name'));
    // Creates node's schema if does not exist.
    Node::dispense('change_address');
    
    try {
        $sql = Node::table('change_address')->where('sent','=',0)->findAll();

        if ($sql->count() == 0) {
            Node::table('change_address')->delete();
        }

        $numItems = $sql->count();
        $i = 0;
        if ($sql->count() > 0) {
            foreach ($sql as $r) {
                $message = _escape(get_option('coa_form_text'));
                $message = str_replace('#uname#', _h($r->uname), $message);
                $message = str_replace('#fname#', _h($r->fname), $message);
                $message = str_replace('#lname#', _h($r->lname), $message);
                $message = str_replace('#name#', get_name(_h($r->personid)), $message);
                $message = str_replace('#id#', _h($r->personid), $message);
                $message = str_replace('#address1#', _h($r->address1), $message);
                $message = str_replace('#address2#', _h($r->address2), $message);
                $message = str_replace('#city#', _h($r->city), $message);
                $message = str_replace('#state#', _h($r->state), $message);
                $message = str_replace('#zip#', _h($r->zip), $message);
                $message = str_replace('#country#', _h($r->country), $message);
                $message = str_replace('#phone#', _h($r->phone), $message);
                $message = str_replace('#email#', _h($r->email), $message);
                $message = str_replace('#adminemail#', _h(get_option('system_email')), $message);
                $message = str_replace('#url#', get_base_url(), $message);
                $message = str_replace('#helpdesk#', _h(get_option('help_desk')), $message);
                $message = str_replace('#currentterm#', _h(get_option('current_term_code')), $message);
                $message = str_replace('#instname#', _h(get_option('institution_name')), $message);
                $message = str_replace('#mailaddr#', _h(get_option('mailing_address')), $message);

                $headers = "From: $site <auto-reply@$host>\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();
                $headers .= "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                $email->etsis_mail(_h(get_option('contact_email')), _t('Change of Address Request'), $message, $headers);
                
                $upd = Node::table('change_address')->find(_h($r->id));
                $upd->sent = 1;
                $upd->save();

                if (++$i === $numItems) {
                    //If we reach the last item, send user a desktop notification.
                    etsis_push_notify('Change of Address', 'Request has been submitted.');
                }
            }
        }
    } catch (\Exception $e) {
        return new \app\src\Core\Exception\Exception($e->getMessage(), 'NodeQ');
    }
}

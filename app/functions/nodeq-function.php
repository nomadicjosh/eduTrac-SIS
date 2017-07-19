<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\Exception;
use Cascade\Cascade;

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
    try {
        // Creates node's schema if does not exist.
        Node::dispense('login_details');

        $sql = Node::table('login_details')->where('sent', '=', 0)->findAll();

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
                $message = process_email_html($message, _t("myetSIS Login Details"));
                $headers[] = sprintf("From: %s <auto-reply@%s>", _t('myetSIS :: ') . _h(get_option('institution_name')), get_domain_name());
                if (!function_exists('etsis_smtp')) {
                    $headers[] = 'Content-Type: text/html; charset="UTF-8"';
                    $headers[] = sprintf("X-Mailer: eduTrac SIS %s", RELEASE_TAG);
                }

                try {
                    _etsis_email()->etsisMail(_h($r->email), _t("myetSIS Login Details"), $message, $headers);
                } catch (phpmailerException $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                } catch (Exception $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                }

                $upd = Node::table('login_details')->find(_h($r->id));
                $upd->sent = 1;
                $upd->save();

                if (++$i === $numItems) {
                    //If we reach the last item, send user a desktop notification.
                    etsis_push_notify('Login Details', 'New login details sent.');
                }
            }
        }
    } catch (NodeQException $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
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
    $from = _h(get_option('institution_name'));

    try {
        // Creates node's schema if does not exist.
        Node::dispense('reset_password');

        $sql = Node::table('reset_password')->where('sent', '=', 0)->findAll();

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
                $message = process_email_html($message, _t('Reset Password'));
                $headers[] = sprintf("From: %s <auto-reply@%s>", $from, get_domain_name());
                if (!function_exists('etsis_smtp')) {
                    $headers[] = 'Content-Type: text/html; charset="UTF-8"';
                    $headers[] = sprintf("X-Mailer: eduTrac SIS %s", RELEASE_TAG);
                }

                try {
                    _etsis_email()->etsisMail(_h($r->email), _t('Reset Password'), $message, $headers);
                } catch (phpmailerException $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                } catch (Exception $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                }

                $upd = Node::table('reset_password')->find(_h($r->id));
                $upd->sent = 1;
                $upd->save();

                if (++$i === $numItems) {
                    //If we reach the last item, send user a desktop notification.
                    etsis_push_notify('Reset Password', 'Reset password email sent.');
                }
            }
        }
    } catch (NodeQException $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
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

    try {
        // Creates node's schema if does not exist.
        Node::dispense('csv_email');

        $sql = Node::table('csv_email')->where('sent', '=', 0)->findAll();

        if ($sql->count() == 0) {
            Node::table('csv_email')->delete();
        }

        $numItems = $sql->count();
        $i = 0;
        if ($sql->count() > 0) {
            foreach ($sql as $r) {
                $message = process_email_html(_escape($r->message), _h($r->subject));
                $headers[] = sprintf("From: %s <auto-reply@%s>", _h(get_option('institution_name')), get_domain_name());
                if (!function_exists('etsis_smtp')) {
                    $headers[] = 'Content-Type: text/html; charset="UTF-8"';
                    $headers[] = sprintf("X-Mailer: eduTrac SIS %s", RELEASE_TAG);
                }

                $attachment = $app->config('file.savepath') . _h($r->filename);

                try {
                    _etsis_email()->etsisMail(_h($r->recipient), _h($r->subject), $message, $headers, [$attachment]);
                } catch (phpmailerException $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                } catch (Exception $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                }

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
    } catch (NodeQException $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
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
    try {
        // Creates node's schema if does not exist.
        Node::dispense('change_address');

        $sql = Node::table('change_address')->where('sent', '=', 0)->findAll();

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
                $message = str_replace('#id#', get_alt_id(_h($r->personid)), $message);
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
                $message = process_email_html($message, _t('Change of Address Request'));
                $headers[] = sprintf("From: %s <auto-reply@%s>", _t('myetSIS :: ') . _h(get_option('institution_name')), get_domain_name());
                if (!function_exists('etsis_smtp')) {
                    $headers[] = 'Content-Type: text/html; charset="UTF-8"';
                    $headers[] = sprintf("X-Mailer: eduTrac SIS %s", RELEASE_TAG);
                }

                try {
                    _etsis_email()->etsisMail(_h(get_option('contact_email')), _t('Change of Address Request'), $message, $headers);
                } catch (phpmailerException $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                } catch (Exception $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                }

                $upd = Node::table('change_address')->find(_h($r->id));
                $upd->sent = 1;
                $upd->save();

                if (++$i === $numItems) {
                    //If we reach the last item, send user a desktop notification.
                    etsis_push_notify('Change of Address', 'Request has been submitted.');
                }
            }
        }
    } catch (NodeQException $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    }
}

/**
 * Acceptance Letter
 * 
 * Function used to send acceptance letter when applicant
 * has been accepted and moved to student.
 * 
 * @since 6.3.0
 */
function etsis_nodeq_acceptance_letter()
{
    try {
        // Creates node's schema if does not exist.
        Node::dispense('acceptance_letter');

        $sql = Node::table('acceptance_letter')->where('sent', '=', 0)->findAll();

        if ($sql->count() == 0) {
            Node::table('acceptance_letter')->delete();
        }

        $numItems = $sql->count();
        $i = 0;
        if ($sql->count() > 0) {
            foreach ($sql as $r) {
                $message = _escape(get_option('student_acceptance_letter'));
                $message = str_replace('#uname#', $r->uname, $message);
                $message = str_replace('#fname#', $r->fname, $message);
                $message = str_replace('#lname#', $r->lname, $message);
                $message = str_replace('#name#', $r->name, $message);
                $message = str_replace('#id#', get_alt_id($r->personid), $message);
                $message = str_replace('#email#', $r->email, $message);
                $message = str_replace('#sacp#', $r->sacp, $message);
                $message = str_replace('#acadlevel#', $r->acadlevel, $message);
                $message = str_replace('#degree#', $r->degree, $message);
                $message = str_replace('#startterm#', $r->startterm, $message);
                $message = str_replace('#adminemail#', _h(get_option('system_email')), $message);
                $message = str_replace('#url#', get_base_url(), $message);
                $message = str_replace('#helpdesk#', _h(get_option('help_desk')), $message);
                $message = str_replace('#currentterm#', _h(get_option('current_term_code')), $message);
                $message = str_replace('#instname#', _h(get_option('institution_name')), $message);
                $message = str_replace('#mailaddr#', _h(get_option('mailing_address')), $message);
                $message = process_email_html($message, _h(get_option('institution_name')) . ' ' . _t('Decision Notification'));
                $headers[] = sprintf("From: %s <auto-reply@%s>", _h(get_option('institution_name')), get_domain_name());
                if (!function_exists('etsis_smtp')) {
                    $headers[] = 'Content-Type: text/html; charset="UTF-8"';
                    $headers[] = sprintf("X-Mailer: eduTrac SIS %s", RELEASE_TAG);
                }

                try {
                    _etsis_email()->etsisMail(_h(get_option('contact_email')), _h(get_option('institution_name')) . ' ' . _t('Decision Notification'), $message, $headers);
                } catch (phpmailerException $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                } catch (Exception $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                }

                $upd = Node::table('acceptance_letter')->find(_h($r->id));
                $upd->sent = 1;
                $upd->save();

                if (++$i === $numItems) {
                    //If we reach the last item, send user a desktop notification.
                    etsis_push_notify('Acceptance Letter', 'An acceptance letter has been emailed to the new student.');
                }
            }
        }
    } catch (NodeQException $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    }
}

/**
 * Send SMS
 * 
 * Function used to send sms messages.
 * 
 * @since 6.3.0
 */
function etsis_nodeq_send_sms()
{
    try {
        // Creates node's schema if does not exist.
        Node::dispense('sms');

        $sql = Node::table('sms')->where('sent', '=', 0)->findAll();

        if ($sql->count() == 0) {
            Node::table('sms')->delete();
        }

        $numItems = $sql->count();
        $i = 0;
        if ($sql->count() > 0) {
            foreach ($sql as $r) {
                try {
                    $client = new Twilio\Rest\Client(get_option('twilio_account_sid'), get_option('twilio_auth_token'));
                    $client->messages->create(
                        $r->number, // Text this number
                        [
                        'from' => get_option('twilio_phone_number'), // From a valid Twilio number
                        'body' => $r->text
                        ]
                    );
                } catch (Twilio\Exceptions\RestException $ex) {
                    \Cascade\Cascade::getLogger('error')->error($ex->getMessage());
                }

                $upd = Node::table('sms')->find(_h($r->id));
                $upd->sent = 1;
                $upd->save();

                if (++$i === $numItems) {
                    //If we reach the last item, send user a desktop notification.
                    etsis_push_notify('SMS', 'SMS messages sent.');
                }
            }
        }
    } catch (NodeQException $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    }
}

/**
 * Sends course registration emails.
 * 
 * Function used to send acceptance letter when applicant
 * has been accepted and moved to student.
 * 
 * @since 6.3.0
 */
function etsis_nodeq_course_registration()
{
    try {
        // Creates node's schema if does not exist.
        Node::dispense('crse_rgn');

        $sql = Node::table('crse_rgn')->where('sent', '=', 0)->findAll();

        if ($sql->count() == 0) {
            Node::table('crse_rgn')->delete();
        }

        if ($sql->count() > 0) {
            foreach ($sql as $r) {
                _etsis_email()->crseRGNEmail(_h($r->stuid), _h($r->sections));

                $upd = Node::table('crse_rgn')->find(_h($r->id));
                $upd->sent = 1;
                $upd->save();
            }
        }
    } catch (NodeQException $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    }
}

/**
 * Student email.
 * 
 * Function used to send emails from professors to their students.
 * 
 * @since 6.3.0
 */
function etsis_nodeq_student_email()
{
    try {
        // Creates node's schema if does not exist.
        Node::dispense('student_email');

        $sql = Node::table('student_email')->where('sent', '=', 0)->findAll();

        if ($sql->count() == 0) {
            Node::table('student_email')->delete();
        }

        $numItems = $sql->count();
        $i = 0;
        if ($sql->count() > 0) {
            foreach ($sql as $r) {
                $from = get_person_by('email', _h($r->from));
                $to = get_person_by('personID', _h($r->to));
                $message = process_email_html(_escape($r->message), _h($r->subject));
                $headers[] = sprintf("From: %s", _h($from->email) != '' ? _h($from->email) : 'no-reply@' . get_domain_name());
                if (_h($r->cc) != '') {
                    $headers[] = sprintf("Cc: %s", _h($r->cc));
                }
                if (_h($r->bcc) != '') {
                    $headers[] = sprintf("Bcc: %s", _h($r->bcc));
                }
                $headers[] = sprintf("Reply-to: %s", _h($from->email));
                if (!function_exists('etsis_smtp')) {
                    $headers[] = 'Content-Type: text/html; charset="UTF-8"';
                    $headers[] = sprintf("X-Mailer: eduTrac SIS %s", RELEASE_TAG);
                }

                try {
                    _etsis_email()->etsisMail(_h($to->email), _h($r->subject), $message, $headers, _h($r->attachment) != '' ? [_h($r->attachment)] : []);
                } catch (phpmailerException $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                } catch (Exception $e) {
                    Cascade::getLogger('system_email')->alert(sprintf('PHPMAILER[%s]: %s', $e->getCode(), $e->getMessage()));
                }

                $upd = Node::table('student_email')->find(_h($r->id));
                $upd->sent = 1;
                $upd->save();

                if (++$i === $numItems) {
                    if (file_exists(_h($r->attachment))) {
                        unlink(_h($r->attachment));
                    }
                    //If we reach the last item, send user a desktop notification.
                    etsis_push_notify('Student email.', 'Email sent.');
                }
            }
        }
    } catch (NodeQException $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('system_email')->alert(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    }
}

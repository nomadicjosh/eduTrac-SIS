<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Email Class
 *  
 * eduTrac SIS
 * Copyright (C) 2013 Joshua Parker
 * 
 * eduTrac SIS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @since       3.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
class Email
{

    private $_mailer;

    public function __construct()
    {
        $this->_mailer = new \app\src\PHPMailer(true);
    }

    /**
     * Borrowed from WordPress
     *
     * Send mail, similar to PHP's mail
     * A true return value does not automatically mean that the user received the
     * email successfully. It just only means that the method used was able to
     * process the request without any errors.
     */
    public function et_mail($to, $subject, $message, $headers = '', $attachments = array())
    {
        $charset = 'UTF-8';

        extract(apply_filter('et_mail', compact('to', 'subject', 'message', 'headers', 'attachments')));

        if (!is_array($attachments))
            $attachments = explode("\n", str_replace("\r\n", "\n", $attachments));

        // From email and name
        // If we don't have a name from the input headers
        if (!isset($from_name))
            $from_name = 'eduTrac';

        if (!isset($from_email)) {
            // Get the site domain and get rid of www.
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') {
                $sitename = substr($sitename, 4);
            }

            $from_email = 'eduTrac@' . $sitename;
        }

        // Plugin authors can override the default mailer
        $this->_mailer->From = apply_filter('et_mail_from', $from_email);
        $this->_mailer->FromName = apply_filter('et_mail_from_name', $from_name);

        // Set destination addresses
        if (!is_array($to))
            $to = explode(',', $to);

        foreach ((array) $to as $recipient) {
            try {
                // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                $recipient_name = '';
                if (preg_match('/(.*)<(.+)>/', $recipient, $matches)) {
                    if (count($matches) == 3) {
                        $recipient_name = $matches[1];
                        $recipient = $matches[2];
                    }
                }
                $this->_mailer->AddAddress($recipient, $recipient_name);
            } catch (phpmailerException $e) {
                continue;
            }
        }

        // Set mail's subject and body
        $this->_mailer->Subject = $subject;
        $this->_mailer->Body = $message;

        // Set to use PHP's mail()
        $this->_mailer->IsMail();

        // Set Content-Type and charset
        // If we don't have a content-type from the input headers
        if (!isset($content_type))
            $content_type = 'text/plain';

        $content_type = apply_filter('et_mail_content_type', $content_type);

        $this->_mailer->ContentType = $content_type;

        // Set whether it's plaintext, depending on $content_type
        if ('text/html' == $content_type)
            $this->_mailer->IsHTML(true);

        // Set the content-type and charset
        $this->_mailer->CharSet = apply_filter('et_mail_charset', $charset);

        // Set custom headers
        if (!empty($headers)) {
            foreach ((array) $headers as $name => $content) {
                $this->_mailer->AddCustomHeader(sprintf('%1$s: %2$s', $name, $content));
            }

            if (false !== stripos($content_type, 'multipart') && !empty($boundary))
                $this->_mailer->AddCustomHeader(sprintf("Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary));
        }

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                try {
                    $this->_mailer->AddAttachment($attachment);
                } catch (phpmailerException $e) {
                    continue;
                }
            }
        }

        do_action_array('etMailer_init', array(&$this->_mailer));

        // Send!
        try {
            $this->_mailer->Send();
        } catch (phpmailerException $e) {
            return false;
        }

        return true;
    }

    public function et_progress_report($email, $id, $host)
    {
        $name = get_name($id);
        $site = get_option('institution_name');
        $message = "You have a new progress report from your child's teacher: $name \n
		
		Log into your account to view this new progress report. \n
		
		$host \n
		
		Thank You \n
		
		Administrator \n
		______________________________________________________
		THIS IS AN AUTOMATED RESPONSE. 
		***DO NOT RESPOND TO THIS EMAIL****
		";

        $headers = "From: $site <auto-reply@$host>\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $this->et_mail($email, "Progress Report", $message, $headers);
        return apply_filter('progress_report', $message, $headers);
    }

    public function course_registration($id, $term, $host)
    {
        $name = get_name($id);
        $site = get_option('institution_name');
        $message = "<p>Dear Registrar:</p>
        
        <p>The following student submitted a new course registration.</p>
        
        <p><strong>Student Name:</strong> $name</p>
        
        <p><strong>Student ID:</strong> $id</p>
        
        <p><strong>Term:</strong> $term</p>
        
        <p>Log into your account to verify this student's registration.</p>
        
        <p>$host</p>
        
        <p>Thank You</p>
        
        <p>Administrator<br />
        ______________________________________________________<br />
        THIS IS AN AUTOMATED RESPONSE.<br />
        ***DO NOT RESPOND TO THIS EMAIL****</p>
        ";

        $headers = "From: $site <auto-reply@$host>\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $this->et_mail(get_option('registrar_email_address'), _t("Course Registration"), $message, $headers);
        return apply_filter('course_registration', $message, $headers);
    }

    public function stu_email($email, $from, $subject, $message, $attachment = '')
    {
        $headers = "From: $from" . "\r\n";
        $headers .= "Reply-To: " . get_persondata('email') . "\r\n";
        $headers .= "CC: " . get_persondata('email') . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $this->et_mail($email, $subject, $message, $headers, $attachment);
        return apply_filter('stu_email', $headers);
    }

    public function myetRegConfirm($email, $id, $username, $password, $host)
    {
        $name = get_name($id);
        $site = _t('myeduTrac::') . get_option('institution_name');
        $message = "<p>Hello $name:</p>
        
		<p>Below are your login details. Keep this email for future reference.</p>
        
        <p><strong>Username:</strong> $username</p>
        
        <p><strong>Password:</strong> $password</p>
        
        <p>$host</p>
        
        <p>Thank You</p>
        
        <p>Administrator<br />
        ______________________________________________________<br />
        THIS IS AN AUTOMATED RESPONSE.<br />
        ***DO NOT RESPOND TO THIS EMAIL****</p>
        ";

        $headers = "From: $site <auto-reply@$host>\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $this->et_mail($email, get_option('institution_name') . _t(" Account Login Details"), $message, $headers);
        return apply_filter('myedutrac_appl_confirm', $message, $headers);
    }

    public function myetApplication($id, $host)
    {
        $name = get_name($id);
        $site = _t('myeduTrac::') . get_option('institution_name');
        $message = "<p>Dear Admissions:</p>
        
		<p>A new application has been submitted via <em>my</em>eduTrac self service.</p>
        
        <p>Log into your account to view this new application.</p>
        
        <p><strong>Applicant:</strong> $name</p>
        <p><strong>Applicant's ID:</strong> $id</p>
        
        <p>$host</p>
        
        <p>Thank You</p>
        
        <p>Administrator<br />
        ______________________________________________________<br />
        THIS IS AN AUTOMATED RESPONSE.<br />
        ***DO NOT RESPOND TO THIS EMAIL****</p>
        ";

        $headers = "From: $site <auto-reply@$host>\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $this->et_mail(get_option('admissions_email'), _t("Application for Admissions"), $message, $headers);
        return apply_filter('myedutrac_application', $message, $headers);
    }
}

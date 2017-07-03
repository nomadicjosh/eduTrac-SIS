<?php namespace app\src\Core;

use Cascade\Cascade;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Email Class
 *
 * @license GPLv3
 *         
 * @since 3.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Email
{

    public $mailer;
    public $app;

    public function __construct()
    {
        $this->mailer = _etsis_phpmailer();
        $this->app = \Liten\Liten::getInstance();
    }

    /**
     * Borrowed from WordPress
     *
     * Send mail, similar to PHP's mail
     * A true return value does not automatically mean that the user received the
     * email successfully. It just only means that the method used was able to
     * process the request without any errors.
     *
     * @since 6.3.0
     * @param string $to
     *            Recipient's email address.
     * @param string $subject
     *            Subject of the email.
     * @param mixed $message
     *            The body of the email.
     * @param mixed $headers
     *            Email headers sent.
     * @param mixed $attachments
     *            Attachments to be sent with the email.
     * @return mixed
     */
    public function etsisMail($to, $subject, $message, $headers = '', $attachments = array())
    {
        $charset = 'UTF-8';

        /**
         * Filter the etsisMail() arguments.
         *
         * @since 1.0.0
         *       
         * @param array $args
         *            A compacted array of etsisMail() arguments, including the "to" email,
         *            subject, message, headers, and attachments values.
         */
        $atts = $this->app->hook->apply_filter('etsis_mail', compact('to', 'subject', 'message', 'headers', 'attachments'));

        if (isset($atts['to'])) {
            $to = $atts['to'];
        }
        if (isset($atts['subject'])) {
            $subject = $atts['subject'];
        }
        if (isset($atts['message'])) {
            $message = $atts['message'];
        }
        if (isset($atts['headers'])) {
            $headers = $atts['headers'];
        }
        if (isset($atts['attachments'])) {
            $attachments = $atts['attachments'];
        }

        if (!is_array($attachments)) {
            $attachments = explode("\n", str_replace("\r\n", "\n", $attachments));
        }

        // Headers
        if (empty($headers)) {
            $headers = [];
        } else {
            if (!is_array($headers)) {
                // Explode the headers out, so this function can take both
                // string headers and an array of headers.
                $tempheaders = explode("\n", str_replace("\r\n", "\n", $headers));
            } else {
                $tempheaders = $headers;
            }
            $headers = [];
            $cc = [];
            $bcc = [];
            // If it's actually got contents
            if (!empty($tempheaders)) {
                // Iterate through the raw headers
                foreach ((array) $tempheaders as $header) {
                    if (strpos($header, ':') === false) {
                        if (false !== stripos($header, 'boundary=')) {
                            $parts = preg_split('/boundary=/i', trim($header));
                            $boundary = trim(str_replace(array(
                                "'",
                                '"'
                                    ), '', $parts[1]));
                        }
                        continue;
                    }
                    // Explode them out
                    list ($name, $content) = explode(':', trim($header), 2);
                    // Cleanup crew
                    $name = trim($name);
                    $content = trim($content);
                    switch (strtolower($name)) {
                        // Mainly for legacy -- process a From: header if it's there
                        case 'from':
                            $bracket_pos = strpos($content, '<');
                            if ($bracket_pos !== false) {
                                // Text before the bracketed email is the "From" name.
                                if ($bracket_pos > 0) {
                                    $from_name = substr($content, 0, $bracket_pos - 1);
                                    $from_name = str_replace('"', '', $from_name);
                                    $from_name = trim($from_name);
                                }
                                $from_email = substr($content, $bracket_pos + 1);
                                $from_email = str_replace('>', '', $from_email);
                                $from_email = trim($from_email);
                                // Avoid setting an empty $from_email.
                            } elseif ('' !== trim($content)) {
                                $from_email = trim($content);
                            }
                            break;
                        case 'content-type':
                            if (strpos($content, ';') !== false) {
                                list ($type, $charset_content) = explode(';', $content);
                                $content_type = trim($type);
                                if (false !== stripos($charset_content, 'charset=')) {
                                    $charset = trim(str_replace(array(
                                        'charset=',
                                        '"'
                                            ), '', $charset_content));
                                } elseif (false !== stripos($charset_content, 'boundary=')) {
                                    $boundary = trim(str_replace(array(
                                        'BOUNDARY=',
                                        'boundary=',
                                        '"'
                                            ), '', $charset_content));
                                    $charset = '';
                                }
                                // Avoid setting an empty $content_type.
                            } elseif ('' !== trim($content)) {
                                $content_type = trim($content);
                            }
                            break;
                        case 'cc':
                            $cc = array_merge((array) $cc, explode(',', $content));
                            break;
                        case 'bcc':
                            $bcc = array_merge((array) $bcc, explode(',', $content));
                            break;
                        default:
                            // Add it to our grand headers array
                            $headers[trim($name)] = trim($content);
                            break;
                    }
                }
            }
        }

        // Empty out the values that may be set
        $this->mailer->ClearAllRecipients();
        $this->mailer->ClearAttachments();
        $this->mailer->ClearCustomHeaders();
        $this->mailer->ClearReplyTos();

        // From email and name
        // If we don't have a name from the input headers
        if (!isset($from_name)) {
            $from_name = 'eduTrac SIS';
        }

        if (!isset($from_email)) {
            // Get the site domain and get rid of www.
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') {
                $sitename = substr($sitename, 4);
            }

            $from_email = 'etsis@' . $sitename;
        }

        /**
         * Filter the email address to send from.
         *
         * @since 1.0.0
         *       
         * @param string $from_email
         *            Email address to send from.
         */
        $this->mailer->From = $this->app->hook->apply_filter('etsis_mail_from', $from_email);

        /**
         * Filter the name to associate with the "from" email address.
         *
         * @since 1.0.0
         *       
         * @param string $from_name
         *            Name associated with the "from" email address.
         */
        $this->mailer->FromName = $this->app->hook->apply_filter('etsis_mail_from_name', $from_name);

        // Set destination addresses
        if (!is_array($to)) {
            $to = explode(',', $to);
        }

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
                $this->mailer->AddAddress($recipient, $recipient_name);
            } catch (phpmailerException $e) {
                Cascade::getLogger('error')->error(sprintf('PHPMailer[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                continue;
            }
        }

        // Set mail's subject and body
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $message;

        // Add any CC and BCC recipients
        if (!empty($cc)) {
            foreach ((array) $cc as $recipient) {
                try {
                    // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                    $recipient_name = '';
                    if (preg_match('/(.*)<(.+)>/', $recipient, $matches)) {
                        if (count($matches) == 3) {
                            $recipient_name = $matches[1];
                            $recipient = $matches[2];
                        }
                    }
                    $this->mailer->AddCc($recipient, $recipient_name);
                } catch (phpmailerException $e) {
                    Cascade::getLogger('error')->error(sprintf('PHPMailer[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                    continue;
                }
            }
        }

        if (!empty($bcc)) {
            foreach ((array) $bcc as $recipient) {
                try {
                    // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                    $recipient_name = '';
                    if (preg_match('/(.*)<(.+)>/', $recipient, $matches)) {
                        if (count($matches) == 3) {
                            $recipient_name = $matches[1];
                            $recipient = $matches[2];
                        }
                    }
                    $this->mailer->AddBcc($recipient, $recipient_name);
                } catch (phpmailerException $e) {
                    Cascade::getLogger('error')->error(sprintf('PHPMailer[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                    continue;
                }
            }
        }

        // Set to use PHP's mail()
        $this->mailer->IsMail();

        // Set Content-Type and charset
        // If we don't have a content-type from the input headers
        if (!isset($content_type)) {
            $content_type = 'text/plain';
        }

        /**
         * Filter the etsisMail() content type.
         *
         * @since 1.0.0
         *       
         * @param string $content_type
         *            Default etsisMail() content type.
         */
        $content_type = $this->app->hook->apply_filter('etsis_mail_content_type', $content_type);

        $this->mailer->ContentType = $content_type;

        // Set whether it's plaintext, depending on $content_type
        if ('text/html' == $content_type) {
            $this->mailer->IsHTML(true);
        }

        // Set the content-type and charset

        /**
         * Filter the default etsisMail() charset.
         *
         * @since 1.0.0
         *       
         * @param string $charset
         *            Default email charset.
         */
        $this->mailer->CharSet = $this->app->hook->apply_filter('etsis_mail_charset', $charset);

        // Set custom headers
        if (!empty($headers)) {
            foreach ((array) $headers as $name => $content) {
                $this->mailer->AddCustomHeader(sprintf('%1$s: %2$s', $name, $content));
            }

            if (false !== stripos($content_type, 'multipart') && !empty($boundary)) {
                $this->mailer->AddCustomHeader(sprintf("Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary));
            }
        }

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                try {
                    $this->mailer->AddAttachment($attachment);
                } catch (phpmailerException $e) {
                    Cascade::getLogger('error')->error(sprintf('PHPMailer[%s]: Error: %s', $e->getCode(), $e->getMessage()));
                    continue;
                }
            }
        }

        /**
         * Fires after PHPMailer is initialized.
         *
         * @since 1.0.0
         *       
         * @param PHPMailer $this->mailer
         *            The PHPMailer instance, passed by reference.
         */
        $this->app->hook->do_action_array('etsisMailer_init', [
            &$this->mailer
        ]);

        // Send!
        try {
            return $this->mailer->Send();
        } catch (phpmailerException $e) {

            $mail_error_data = compact($to, $subject, $message, $headers, $attachments);
            /**
             * Fires after a phpmailerException is caught.
             *
             * @since 6.2.3
             *       
             * @param etsis_Error $error
             *            A etsis_Error object with the phpmailerException code, message, and an array
             *            containing the mail recipient, subject, message, headers, and attachments.
             */
            $this->app->hook->do_action('etsis_mail_failed', new \app\src\Core\etsis_Error($e->getCode(), $e->getMessage(), $mail_error_data));
            Cascade::getLogger('error')->error(sprintf('PHPMailer[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            return false;
        }

        return true;
    }

    /**
     * When a prospects register via the eduTrac SIS self service portal, an
     * email is sent with account login details.
     *
     * @since 6.3.0
     * @param int $id
     *            Person ID of the applicant.
     * @param string $password
     *            Login password of the new prospect.
     * @return mixed
     */
    public function myetsisRegConfirm($id, $password)
    {
        $site = _t('myetSIS::') . _h(get_option('institution_name'));
        $domain = get_domain_name();
        $nae = get_person_by('personID', $id);

        $message = sprintf(_t('<p>Hello %s:</p>'), _h($nae->fname));
        $message .= _t("<p>Below are your login details. Keep this email for future reference.</p>");
        $message .= sprintf(_t('<p><strong>Username:</strong> %s</p>'), _h($nae->uname));
        $message .= sprintf(_t("<p><strong>Password:</strong> %s</p>"), $password);
        $message .= sprintf(_t('<p><a href="%s">%s</a></p>'), etsis_login_url(), etsis_login_url());
        $message .= '______________________________________________________<br />';
        $message .= _t("THIS IS AN AUTOMATED RESPONSE.<br />");
        $message .= _t("****DO NOT RESPOND TO THIS EMAIL****");

        $msg = process_email_html($message, _t(" Account Login Details"));
        $headers = "From: $site <auto-reply@$domain>\r\n";
        if (_h(get_option('etsis_smtp_smtpauth')) != 'yes') {
            $headers .= "X-Mailer: tinyCampaign " . CURRENT_RELEASE . "\r\n";
            $headers .= "MIME-Version: 1.0" . "\r\n";
        }

        $this->etsisMail(_h($nae->email), _h(get_option('institution_name')) . _t(":: Account Login Details"), $msg, $headers);
        return $this->app->hook->apply_filter('myetsis_appl_confirm', $msg, $headers);
    }

    /**
     * Email sent to admissions to alert of a new application.
     *
     * @since 6.3.0
     * @param int $id
     *            Person ID of the applicant.
     * @param int $applID Unique application id.
     * @return mixed
     */
    public function myetsisApplication($id, $applID)
    {
        $site = _t('myetSIS::') . _h(get_option('institution_name'));
        $domain = get_domain_name();
        $redirect_to = etsis_login_url(get_base_url() . 'appl/' . $applID . '/');

        $message = _t('<p>Dear Admissions:</p>');
        $message .= _t("<p>A new application has been submitted via <em>my</em>eduTrac SIS self service.</p>");
        $message .= _t('<p>Click on the link below and log into your account in order to view this new application.</p>');
        $message .= sprintf(_t('<p><strong>Applicant:</strong> %s</p>'), get_name($id));
        $message .= sprintf(_t("<p><strong>Applicant's ID:</strong> %s</p>"), get_alt_id($id));
        $message .= sprintf(_t('<p><a href="%s">%s</a></p>'), $redirect_to, $redirect_to);
        $message .= '______________________________________________________<br />';
        $message .= _t("THIS IS AN AUTOMATED RESPONSE.<br />");
        $message .= _t("****DO NOT RESPOND TO THIS EMAIL****");

        $msg = process_email_html($message, _t("Application for Admissions"));
        $headers = "From: $site <auto-reply@$domain>\r\n";
        if (_h(get_option('etsis_smtp_smtpauth')) != 'yes') {
            $headers .= "X-Mailer: eduTrac SIS " . RELEASE_TAG . "\r\n";
            $headers .= "MIME-Version: 1.0" . "\r\n";
        }

        $this->etsisMail(_h(get_option('admissions_email')), _t("Application for Admissions"), $msg, $headers);
        return $this->app->hook->apply_filter('myetsis_application', $msg, $headers);
    }

    /**
     * Sends new course registration information to the registrar.
     * 
     * @since 6.3.0
     *
     * @param int $id
     *            Student ID.
     * @param mixed $courses
     *            Courses student registered for.
     * @return type
     */
    public function crseRGNEmail($id, $courses)
    {
        $nae = get_person_by('personID', $id);
        $domain = get_domain_name();
        $site = _h(get_option('institution_name'));
        $redirect_to = etsis_login_url(get_base_url() . 'stu/stac/' . $id . '/');

        $message = _t('<p>Dear Registrar:</p>');
        $message .= _t("<p>This is a receipt for the following student's registration.</p>");
        $message .= sprintf(_t('<p><strong>Student Name:</strong> %s</p>'), concat_ws(', ', _h($nae->lname), _h($nae->fname)));
        $message .= sprintf(_t('<p><strong>Student ID:</strong> %s</p>'), get_alt_id($id));
        $message .= sprintf(_t('<p><strong>Courses:</strong> %s</p>'), $courses);
        $message .= _t("<p>Click on the link below and log into your account in order to verify this student's registration.</p>");
        $message .= sprintf(_t('<p><a href="%s">%s</a></p>'), $redirect_to, $redirect_to);
        $message .= '______________________________________________________<br />';
        $message .= _t("THIS IS AN AUTOMATED RESPONSE.<br />");
        $message .= _t("****DO NOT RESPOND TO THIS EMAIL****");

        $msg = process_email_html($message, _t("Course Registration"));
        $headers = "From: $site <auto-reply@$domain>\r\n";
        if (_h(get_option('etsis_smtp_smtpauth')) != 'yes') {
            $headers .= "X-Mailer: eduTrac SIS " . RELEASE_TAG . "\r\n";
            $headers .= "MIME-Version: 1.0" . "\r\n";
        }
        try {
            $this->etsisMail(_h($nae->email), _t('Course Registration'), $msg, $headers);
        } catch (\phpmailerException $e) {
            _etsis_flash()->error($e->getMessage());
        }

        return $this->app->hook->apply_filter('crse_rgn_email', $msg, $headers);
    }

    /**
     * Borrowed from WordPress
     *
     * Send mail, similar to PHP's mail
     * A true return value does not automatically mean that the user received the
     * email successfully. It just only means that the method used was able to
     * process the request without any errors.
     *
     * @since 1.0.0
     * @param string $to
     *            Recipient's email address.
     * @param string $subject
     *            Subject of the email.
     * @param mixed $message
     *            The body of the email.
     * @param mixed $headers
     *            Email headers sent.
     * @param mixed $attachments
     *            Attachments to be sent with the email.
     * @return mixed
     */
    public function etsis_mail($to, $subject, $message, $headers = '', $attachments = array())
    {
        _deprecated_class_method(__METHOD__, '6.3.0', 'etsisMail');

        return $this->etsisMail($to, $subject, $message, $headers, $attachments);
    }

    /**
     * Method used to send students an email currently via the faculty portal.
     *
     * @deprecated since release 6.2.11
     */
    public function stu_email()
    {
        _deprecated_class_method(__METHOD__, '6.2.11');
    }
}

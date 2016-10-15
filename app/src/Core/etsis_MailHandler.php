<?php namespace app\src\Core;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\MailHandler;
use app\src\Core\etsis_Email;

/**
 * Monolog Handler Email Class
 *
 * @license GPLv3
 *         
 * @since 6.2.12
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_MailHandler extends MailHandler
{

    protected $mailer;
    protected $email_to;
    protected $subject;
    private $messageTemplate;

    public function __construct(etsis_Email $mailer, $message, $email_to, $subject, $level = Logger::ALERT, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->mailer = $mailer;
        $this->email_to = $email_to;
        $this->subject = $subject;
        $this->messageTemplate = $message;
    }

    protected function send($content, array $records)
    {
        return $this->buildMessage($content, $records);
    }

    /**
     * Creates instance of etsis_Email to be sent
     *
     * @param  string        $content formatted email body to be sent
     * @param  array         $records Log records that formed the content
     * @return etsis_Email
     */
    protected function buildMessage(string $content, array $records)
    {
        $message = null;
        if ($this->mailer instanceof etsis_Email) {
            $message = clone $this->mailer;
        } elseif (is_callable($this->mailer)) {
            $message = call_user_func($this->mailer, $content, $records);
        }
        if (!$message instanceof etsis_Email) {
            throw new \InvalidArgumentException(_t('Could not resolve message as instance of etsis_Email or a callable returning it'));
        }
        if ($records) {
            $subjectFormatter = new LineFormatter($this->subject);
            $message = $this->mailer->etsis_mail($this->email_to, $subjectFormatter->format($this->getHighestRecord($records)), $content);
        }
        return $message;
    }
}

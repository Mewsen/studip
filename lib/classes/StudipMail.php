<?php

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

/**
 * StudipMail.php
 *
 * class for constructing and sending emails in Stud.IP
 *
 *
 * @author  André Noack <noack@data-quest>, Suchi & Berg GmbH <info@data-quest.de>
 * @version 1
 * @license GPL2 or any later version
 * @copyright 2009 authors
 */
class StudipMail
{
    public const DEBUG_TRANSPORTER = 'debug';

    public const SENDMAIL_TRANSPORTER = 'sendmail';

    public const NULL_TRANSPORTER = 'null';

    public const NATIVE_TRANSPORTER = 'php';

    public const SMTP_TRANSPORTER = 'smtp';

    private static ?Mailer $mailer = null;

    private static string $transporter;

    private string $body_text;

    private string $body_html;

    private string $subject;

    /**
     * Array of all attachments, name ist key
     *
     */
    private array $attachments = [];

    /**
     * Array of attachments that are related to the content
     *
     */
    private array $related_attachments = [];

    private array $sender;

    /**
     * Array of all recipients, mail is key
     */
    private array $recipients = [];

    /**
     * @var array
     */
    private array $reply_to;

    /**
     * Sets the default transporter used in StudipMail::send()
     * @param string $transporter
     * @return void
     */
    public static function setDefaultTransporter(string $transporter)
    {
        self::$transporter = $transporter;
    }

    /**
     * gets the default transporter used in StudipMail::send()
     *
     * @return string
     */
    public static function getDefaultTransporter()
    {
        return self::$transporter;
    }

    /**
     * Gets the configured abuse mail contact
     *
     * @return string
     */
    public static function getAbuseEmail(): string
    {
        $mail_localhost = $GLOBALS['MAIL_LOCALHOST'] ?: $_SERVER['SERVER_NAME'];
        return $GLOBALS['MAIL_ABUSE'] ?: "abuse@{$mail_localhost}";
    }

    /**
     * convenience method for sending a qick, text based email message
     *
     * @param string $recipient
     * @param string $subject
     * @param string $text Plain text version of the message (required).
     * @param string|null $html HTML version of the message (optional).
     * @return bool
     */
    public static function sendMessage(string $recipient, string $subject, string $text, string $html = ''): bool
    {
        $mail = new StudipMail();

        // Add Stud.IP logo as "pseudo" attachment - this will be embedded in the mail via Content-ID.
        $mail->addRelatedAttachment(
            $GLOBALS['STUDIP_BASE_PATH'] . '/public/assets/images/logos/logo-hires.png',
            'studip-logo.png',
            'image/png',
            'studiplogo'
        );

        return $mail->setSubject($subject)
                    ->addRecipient($recipient)
                    ->setBodyText($text)
                    ->setBodyHtml($html)
                    ->send();
    }

    /**
     * convenience method for sending a qick, text based email message
     * to the configured abuse adress
     *
     * @param string $subject
     * @param string $text
     * @return bool
     */
    public static function sendAbuseMessage($subject, $text): bool
    {
        $mail = new StudipMail();
        $abuse = self::getAbuseEmail();
        return $mail->setSubject($subject)
                    ->addRecipient($abuse)
                    ->setBodyText($text)
                    ->send();
    }

    /**
     * sets some default values for sender and reply to from
     * configuration settings.
     *
     */
    public function __construct($data = null)
    {
        $dsn = match (self::getDefaultTransporter()) {
            self::SENDMAIL_TRANSPORTER => 'sendmail://default',
            self::NATIVE_TRANSPORTER   => 'native://default',
            self::NULL_TRANSPORTER     => 'null://null',
            default => $this->buildSmtpDsn()
        };
        if (!self::$mailer) {
            if (self::getDefaultTransporter() === self::DEBUG_TRANSPORTER) {
                $transport = new StudipDebugTransport(
                    $GLOBALS['TMP_PATH'] . '/' .
                    ($GLOBALS['DEBUG_MAIL_LOG_FILE_NAME'] ?? 'studip-mail-debug.log')
                );
            } else {
                $transport = Transport::fromDsn($dsn);
            }
            self::$mailer = new Mailer($transport);
        }
        $this->mailer = self::$mailer;
        $mail_localhost = $GLOBALS['MAIL_LOCALHOST'] ?: $_SERVER['SERVER_NAME'];
        $this->setSenderEmail($GLOBALS['MAIL_ENV_FROM'] ?: "wwwrun@{$mail_localhost}");
        $this->setSenderName($GLOBALS['MAIL_FROM'] ?: 'Stud.IP - ' . Config::get()->UNI_NAME_CLEAN);

        if ($data) {
            $this->setData($data);
        }
    }

    private function buildSmtpDsn(): string
    {
        $host = $GLOBALS['MAIL_HOST_NAME'] ?: 'localhost';
        $port = $GLOBALS['MAIL_SMTP_OPTIONS']['port'] ?? 25;
        $user = $GLOBALS['MAIL_SMTP_OPTIONS']['user'] ?? '';
        $pass = $GLOBALS['MAIL_SMTP_OPTIONS']['password'] ?? '';

        $query = [];

        if (!empty($GLOBALS['MAIL_SMTP_OPTIONS']['ssl'])) {
            $query['encryption'] = 'ssl';
        } elseif (!empty($GLOBALS['MAIL_SMTP_OPTIONS']['start_tls'])) {
            $query['encryption'] = 'tls';
        }

        if (!empty($GLOBALS['MAIL_SMTP_OPTIONS']['authentication_mechanism'])) {
            $query['auth_mode'] = $GLOBALS['MAIL_SMTP_OPTIONS']['authentication_mechanism'];
        }

        $mail_localhost = $GLOBALS['MAIL_LOCALHOST'] ?: $_SERVER['SERVER_NAME'];

        if ($mail_localhost) {
            $query['local_domain'] = $mail_localhost;
        }

        $credentials = '';
        if ($user !== '') {
            $credentials = urlencode($user);
            if ($pass !== '') {
                $credentials .= ':' . urlencode($pass);
            }
            $credentials .= '@';
        }

        $qs = $query ? '?' . http_build_query($query) : '';

        return "smtp://{$credentials}{$host}:{$port}{$qs}";
    }

    /**
     * @param string $mail
     * @return StudipMail provides fluent interface
     */
    public function setSenderEmail($mail)
    {
        $this->sender['mail'] = $mail;
        return $this;
    }

    /**
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->sender['mail'];
    }

    /**
     * @param string $name
     * @return StudipMail provides fluent interface
     */
    public function setSenderName($name)
    {
        $this->sender['name'] = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSenderName()
    {
        return $this->sender['name'];
    }

    /**
     * @param $mail
     * @return StudipMail provides fluent interface
     */
    public function setReplyToEmail($mail)
    {
        $this->reply_to['mail'] = $mail;
        return $this;
    }

    /**
     * @return string
     */
    public function getReplyToEmail()
    {
        return $this->reply_to['mail'] ?? '';
    }

    /**
     * @param $name
     * @return StudipMail provides fluent interface
     */
    public function setReplyToName($name)
    {
        $this->reply_to['name'] = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getReplyToName()
    {
        return $this->reply_to['name'] ?? '';
    }

    /**
     * @param $subject
     * @return StudipMail provides fluent interface
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param $mail
     * @param $name
     * @param $type
     * @return StudipMail provides fluent interface
     */
    public function addRecipient($mail, $name = '', $type = 'To')
    {
        $type = ucfirst($type);
        $type = in_array($type, ['To', 'Cc', 'Bcc']) ? $type : 'To';
        if (!isset($this->recipients[$mail]) || $this->recipients[$mail]['type'] !== 'To') {
            $this->recipients[$mail] = compact('mail', 'name', 'type');
        }
        return $this;
    }

    /**
     * @param $mail
     * @return StudipMail provides fluent interface
     */
    public function removeRecipient($mail)
    {
        unset($this->recipients[$mail]);
        return $this;
    }

    /**
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @param $file_name
     * @param $name
     * @param $type
     * @param $disposition
     * @return StudipMail provides fluent interface
     */
    public function addFileAttachment($file_name, $name = '', $type = 'automatic/name', $disposition = 'attachment')
    {
        $name = $name ?: basename($file_name);
        $this->attachments[$name] = compact('file_name', 'name', 'type', 'disposition');
        return $this;
    }

    /**
     * @param FileRef $file_ref The FileRef object of a file that shall be added to a mail
     * @return StudipMail provides fluent interface
     */
    public function addStudipAttachment(FileRef $file_ref)
    {
        if (!$file_ref->isNew()) {
            $this->addFileAttachment(
                $file_ref->file->getPath(),
                $file_ref->name
            );
        }
        return $this;
    }

    public function addRelatedAttachment(string $file_name, string $name, string $type, string $content_id): void
    {
        $this->related_attachments[$name] = [
            'FileName' => $file_name,
            'Name' => $name,
            'Content-Type' => $type,
            'Disposition' => 'inline',
            'Content-ID' => $content_id
        ];
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param $body
     * @return StudipMail provides fluent interface
     */
    public function setBodyText($body)
    {
        $this->body_text = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getBodyText()
    {
        return $this->body_text;
    }

    /**
     * @param $body
     * @return StudipMail provides fluent interface
     */
    public function setBodyHtml($body)
    {
        $this->body_html = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getBodyHtml()
    {
        return $this->body_html;
    }

    /**
     * send the mail
     *
     * @return bool
     */
    public function send(): bool
    {
        try {
            $email = new Email();
            $email->returnPath($this->getSenderEmail());
            $email->from(
                new Address(
                    $this->getSenderEmail(),
                    $this->getSenderName()
                )
            );
            if ($this->getReplyToEmail()) {
                $email->replyTo(
                    new Address(
                        $this->getReplyToEmail(),
                        $this->getReplyToName()
                    )
                );
            }
            foreach ($this->getRecipients() as $recipient) {
                $address = new Address(
                    $recipient['mail'],
                    $recipient['name']
                );

                switch ($recipient['type']) {
                    case 'Cc':
                        $email->cc($address);
                        break;
                    case 'Bcc':
                        $email->bcc($address);
                        break;
                    default:
                        $email->to($address);
                }
            }

            $email->subject($this->getSubject());

            if ($this->getBodyHtml()) {
                $text_message = $this->getBodyText();
                if (!$text_message) {
                    $text_message = _(
                        'Diese Nachricht ist im HTML-Format verfasst. Sie benötigen eine E-Mail-Anwendung, die das HTML-Format anzeigen kann.'
                    );
                }
                $email->text($text_message);
                $email->html($this->getBodyHtml());

                foreach ($this->related_attachments as $attachment) {
                    $part = new DataPart(
                        fopen($attachment['FileName'], 'r'),
                        $attachment['Content-ID'],
                        $attachment['Content-Type']
                    );
                    $part->asInline();
                    $email->addPart($part);
                }
            } else {
                $email->text($this->getBodyText());
            }

            foreach ($this->getAttachments() as $attachment) {
                if (!empty($attachment['file_name'])) {
                    $email->attachFromPath(
                        $attachment['file_name'],
                        $attachment['name'],
                        $attachment['type'] !== 'automatic/name' ? $attachment['type'] : null
                    );
                } elseif (!empty($attachment['data'])) {
                    $email->attach(
                        $attachment['data'],
                        $attachment['name'],
                        $attachment['type'] !== 'automatic/name' ? $attachment['type'] : null
                    );
                }
            }
            $this->mailer->send($email);
            return true;
        } catch (\Throwable $e) {
            Log::error('StudipMail::send - ' . $e->getMessage());
            return false;
        }
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function setData($data)
    {
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }
}

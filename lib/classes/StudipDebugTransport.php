<?php

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;

class StudipDebugTransport extends AbstractTransport
{
    private string $logfile;

    public function __construct(string $logfile)
    {
        parent::__construct();
        $this->logfile = $logfile;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (!$email instanceof Email) {
            return;
        }

        $username = $GLOBALS['user']->username ?? 'unknown';

        $entry  = "\n-- " . date('d.m.Y H:i:s') . " " . $username;
        $entry .= "\nTo: " . implode(', ', array_map(fn($a) => $a->toString(), $email->getTo()));
        $entry .= "\nSubject: " . $email->getSubject();


        $headers = '';
        foreach ($email->getHeaders()->all() as $header) {
            $headers .= $header->toString() . "\n";
        }

        if (strlen(trim($headers))) {
            $entry .= "\n" . $headers;
        }

        $entry .= "\n";

        if ($email->getTextBody()) {
            $entry .= $email->getTextBody();
        } elseif ($email->getHtmlBody()) {
            $entry .= $email->getHtmlBody();
        }

        $entry .= "\n";

        file_put_contents($this->logfile, $entry, FILE_APPEND);
    }

    public function __toString(): string
    {
        return 'studip-debug://default';
    }
}

<?php
/*
 * debug_message.php
 *
 *
 *
 */


class debug_message_class extends email_message_class
{
    private $logfile = '';

    function __construct() {
        $this->logfile = implode('/', [
            $GLOBALS['TMP_PATH'],
            $GLOBALS['DEBUG_MAIL_LOG_FILE_NAME'] ?? 'studip-mail-debug.log'
        ]);
    }

	function SendMail($to,$subject,$body,$headers,$return_path) {
		if ($log = fopen($this->logfile, "a")){
			if(strlen($headers)) $headers.="\n";
			fwrite($log, "\n-- " . strftime("%x %X"). ' ' . $GLOBALS['user']->username);
			fwrite($log, "\nTo: ".$to."\nSubject: ".$subject."\n".$headers."\n");
			fwrite($log,$body."\n");
			fclose($log);
		}
	}
}
?>

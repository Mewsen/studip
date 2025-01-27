<?php
/**
 * send_massmails.php
 *
 * @author Thomas Hackl <hackl@data-quest.de>
 * @access public
 * @since  6.0
 */

/**
 * Cronjob class to send massmails.
 */
class SendMassmailsJob extends CronJob
{

    /**
     * Returns the name of the cronjob.
     * @return string : name of the cronjob
     */
    public static function getName()
    {
        return _('Nachrichten an Zielgruppen senden');
    }

    /**
     * Returns the description of the cronjob.
     * @return string : description of the cronjob.
     */
    public static function getDescription()
    {
        return _('Sendet alle anstehenden Nachrichten an Zielgruppen und räumt bereits gesendete auf.');
    }

    /**
     * Sends all mass mails.
     * @param integer $last_result : not evaluated for execution, so any integer
     * will do. Usually it would be a unix-timestamp of last execution. But in
     * this case we don't care at all.
     * @param array $parameters : not needed here
     */
    public function execute($last_result, $parameters = [])
    {
        // Find all messages that need to be sent:
        foreach (\MassMail\MassMailMessage::findUnsent() as $message) {
            // Mark message as "currently working on".
            $message->locked = 1;
            $message->store();

            $messaging = new messaging();

            // Markers present: this must be a personalized message to every recipient.
            if ($message->hasMarkers()) {

                foreach ($message->getRecipients() as $recipient) {

                    $mail = new Message();
                    $mail->setId($mail->getNewId());

                    $result = $messaging->insert_message(
                        $message->replaceMarkers(User::findOneByUsername($recipient)),
                        $recipient,
                        $message->sender_id,
                        time(),
                        $mail->id,
                        '',
                        '',
                        $message->subject
                    );

                    echo sprintf("Sending message %s to %s\n", $message->subject, $recipient);
                }

            // No markers -> we can send this as one single message to everyone at once.
            } else {

                $mail = new Message();
                $mail->setId($mail->getNewId());

                $result = $messaging->insert_message(
                    $message->message,
                    $message->getRecipients(),
                    $message->sender_id,
                    time(),
                    $mail->id,
                    '',
                    '',
                    $message->subject
                );

                echo sprintf("Sending message %s to %u recipients\n", $message->subject, count($message->getRecipients()));
            }

            if ($result) {
                echo "Success!\n";
                $message->locked = 0;
                $message->sent = 1;
                $message->store();
            }

        }

        // Now cleanup all messages that have been sent and are older than the configured number of days.
        foreach (\MassMail\MassMailMessage::findObsolete() as $message) {
            $message->delete();
        }
    }
}

<?php
/**
 * remind_oer_upload.class.php - Sends reminder emails for uploading files to OER Campus.
 *
 * @author Michaela Brückner <brueckner@data-quest.de>, Suchi & Berg GmbH <info@data-quest.de>
 * @access public
 * @since  5.2
 */

require_once 'lib/classes/CronJob.class.php';

class RemindOerUpload extends CronJob
{

    public static function getName()
    {
        return _('An OER Campus Upload erinnern');
    }

    public static function getDescription()
    {
        return _('Erinnert den Autor am Ende des Semesters an eine Datei, die in den OER Campus hochgeladen werden soll.');
    }

    public function execute($last_result, $parameters = [])
    {
        // check the reminder date, which now is in past
        $query = "SELECT * FROM `oer_post_upload`
                    WHERE `reminder_date` < UNIX_TIMESTAMP()";
        $results = DBManager::get()->exec($query);

        // TODO fuer jedes Resultat dem ersteller eine nachricht schicken
        // ggf vorher sammeln, eine nachricht pro ersteller
        // eintraege entfernen, sonst werden benachrichtigungen mehrmals versandt

    }
}

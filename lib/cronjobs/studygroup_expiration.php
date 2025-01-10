<?php
/**
 * studygroup_expiration.php - Delete expired study groups
 *
 * @author Rasmus Fuhse <fuhse@data-quest.de>
 * @author Michaela Brückner <brueckner@data-quest.de>
 * @access public
 * @since  6.0
 */

require_once 'lib/classes/CronJob.php';

class StudygroupExpirationJob extends CronJob
{
    /**
     * Returns the name of the cronjob.
     */
    public static function getName()
    {
        return _('Studiengruppen aufräumen');
    }

    /**
     * Returns the description of the cronjob.
     */
    public static function getDescription()
    {
        return _('Studiengruppen, die abgelaufen sind, werden gelöscht. Zusätzlich werden Gruppengründer:innen benachrichtigt, wenn ihre Studiengruppen in einem Monat ablaufen.');
    }

    /**
     * Return the paremeters for this cronjob.
     *
     * @return Array Parameters.
     */
    public static function getParameters()
    {
        return [];
    }

    /**
     * Executes the cronjob.
     *
     * @param mixed $last_result What the last execution of this cronjob
     *                           returned.
     * @param Array $parameters Parameters for this cronjob instance which
     *                          were defined during scheduling.
     */
    public function execute($last_result, $parameters = [])
    {
        $statement = DBManager::get()->prepare("
            SELECT `range_id`
            FROM `config_values`
            WHERE `field` = 'STUDYGROUP_EXPIRATION_DATE'
                AND `value` > 0 AND `value` < UNIX_TIMESTAMP()
        ");
        $statement->execute();
        while ($course_id = $statement->fetch(PDO::FETCH_COLUMN)) {
            $course = Course::find($course_id);
            $course->delete();
        }

        //now the notifications
        $messaging = new messaging();
        $statement = DBManager::get()->prepare("
            SELECT `seminare`.*
            FROM `seminare`
                INNER JOIN `config_values` ON (`config_values`.`range_id` = `seminare`.`Seminar_id` AND `config_values`.`field` = 'STUDYGROUP_EXPIRATION_DATE')
            WHERE `config_values`.`value` >= UNIX_TIMESTAMP() - 86400 * 31
                AND `config_values`.`value` < UNIX_TIMESTAMP() - 86400 * 30
        ");
        $statement->execute([
            'last_time' => $last_result
        ]);
        while ($course = Course::buildExisting($statement->fetch(PDO::FETCH_ASSOC))) {
            foreach ($course->getTeachers() as $course_member) {
                setTempLanguage($course_member->user_id);
                $message = sprintf(
                    _('Ihre Studiengruppe %s wird in einem Monat ablaufen und dann automatisch gelöscht werden. Falls Sie die Studiengruppe noch benötigen, ändern Sie in der Verwaltung der Studiengruppe das Ablaufdatum.'),
                    $course->getFullName()
                );
                $subject = _('Ablauf Ihrer Studiengruppe');
                $messaging->insert_message(
                    $message,
                    $course_member->user->username,
                    '____%system%____',
                    '',
                    '',
                    '',
                    '',
                    $subject
                );
                restoreLanguage();
            }
        }
    }
}

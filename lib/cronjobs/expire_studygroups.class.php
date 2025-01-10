<?php
/**
 * expire_studygroups.class.php - Removes studygroups that have reached their expiration date
 * and also notifies about upcoming expirations to the founders of the studygroups.
 *
 * @author Rasmus Fuhse <fuhse@data-quest.de>
 * @access public
 * @since  6.0
 */

class ExpireStudygroups extends CronJob
{

    public static function getName()
    {
        return _('Studiengruppen abräumen');
    }

    public static function getDescription()
    {
        return _('Löscht ablaufende Studiengruppen und benachrichtigt einen Monat vor Ablauf der Studiengruppe über die Löschung.');
    }

    public function execute($last_result, $parameters = [])
    {
        $statement = DBManager::get()->prepare("
            SELECT `seminare`.*
            FROM `seminare`
                INNER JOIN `config_values` ON (`config_values`.`range_id` = `seminare`.`Seminar_id` AND `config_values`.`field` = 'STUDYGROUP_EXPIRATION_DATE')
            WHERE `config_values`.`value` >= UNIX_TIMESTAMP()
        ");
        $statement->execute();
        while ($course = Course::buildExisting($statement->fetch(PDO::FETCH_ASSOC))) {
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

        }
    }
}

<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/613
 */
final class RemoveCourseMembersHideConfiguration extends Migration
{
    public function description()
    {
        return 'Removes the configuration "COURSE_MEMBERS_HIDE" in favor of the setting in modules.';
    }

    protected function up()
    {
        // Migrate config settings
        $query = "SELECT `pluginid`
                  FROM `plugins`
                  WHERE `pluginclassname` = 'CoreParticipants'";
        $plugin_id = DBManager::get()->fetchColumn($query);

        $query = "SELECT `range_id`
                  FROM `config_values`
                  JOIN `tools_activated` USING (`range_id`)
                  WHERE `field` = 'COURSE_MEMBERS_HIDE'
                    AND `plugin_id` = ?
                    AND  IFNULL(`metadata`, '') NOT LIKE ?";
        DBManager::get()->fetchFirst(
            $query,
            [$plugin_id, '%"visibility":"tutor"%'],
            function ($course_id) use ($plugin_id) {
                $metadata = $this->getMetaDataForCourseAndPlugin($course_id, $plugin_id);
                $metadata['visibility'] = 'tutor';

                $query = "UPDATE `tools_activated`
                          SET `metadata` = ?
                          WHERE `range_id` = ?
                            AND `plugin_id` = ?";
                DBManager::get()->execute($query, [
                    json_encode($metadata),
                    $course_id,
                    $plugin_id
                ]);
            }
        );

        // Code taken from migration 1.305
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'COURSE_MEMBERS_HIDE'";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        // Code taken from migration 1.305
        $query = "INSERT IGNORE INTO `config` (`field`, `value`, `type`, `range`, `mkdate`, `chdate`, `description`)
                  VALUES (:name, :value, :type, :range, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)";

        $statement = DBManager::get()->prepare($query);
        $statement->execute([
            ':name'        => 'COURSE_MEMBERS_HIDE',
            ':description' => 'Über diese Option können Sie die Teilnehmendenliste für Studierende der Veranstaltung unsichtbar machen',
            ':range'       => 'course',
            ':type'        => 'boolean',
            ':value'       => '0'
        ]);
    }

    private function getMetaDataForCourseAndPlugin(string $course_id, string $plugin_id): array
    {
        $query = "SELECT `metadata`
                  FROM `tools_activated`
                  WHERE `range_id` = ?
                    AND `plugin_id` = ?";
        $value = DBManager::get()->fetchColumn($query, [$course_id, $plugin_id]);
        return json_decode($value, true) ?: [];
    }
}

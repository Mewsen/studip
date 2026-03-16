<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/6363
 */
final class AddKeysForStudygroupProposals extends Migration
{
    public function description()
    {
        return 'Add keys for the studygroup proposals query in MyStudygroupsController';
    }

    protected function up()
    {
        $query = "ALTER TABLE `seminare`
                  DROP INDEX `status`,
                  ADD INDEX `status_mkdate_seminar` (`status`, `mkdate`, `seminar_id`)";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `mvv_stg_stgteil`
                  DROP INDEX `stgteil_id`,
                  ADD INDEX `stgteil_studiengang` (`stgteil_id`, `studiengang_id`)";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `mvv_stgteil`
                  DROP INDEX `fach_id`,
                  ADD INDEX `fach_stgteil` (`fach_id`, `stgteil_id`)";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `studygroup_stgteil`
                  DROP INDEX `studygroup_id_2`,
                  DROP INDEX `stgteil_id`,
                  ADD INDEX `stgteil_studygroup` (`stgteil_id`, `studygroup_id`)";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        $query = "ALTER TABLE `studygroup_stgteil`
                  DROP INDEX `stgteil_studygroup`,
                  ADD INDEX `stgteil_id` (`stgteil_id`),
                  ADD INDEX `studygroup_id_2` (`studygroup_id`)";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `mvv_stgteil`
                  DROP INDEX `fach_stgteil`,
                  ADD INDEX `fach_id` (`fach_id`)";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `mvv_stg_stgteil`
                  DROP INDEX `stgteil_studiengang`,
                  ADD INDEX `stgteil_id` (`stgteil_id`)";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `seminare`
                  DROP INDEX `status_mkdate_seminar`,
                  ADD INDEX `status` (`status`)";
        DBManager::get()->exec($query);
    }
}

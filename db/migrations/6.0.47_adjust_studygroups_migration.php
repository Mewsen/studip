<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/5489
 */
final class AdjustStudygroupsMigration extends Migration
{
    public function description()
    {
        return 'Fixes issues with migration 6.0.38';
    }

    public function up()
    {
        $query = "ALTER TABLE `studygroup_courses`
                  MODIFY COLUMN `studygroup_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                  MODIFY COLUMN `course_id` CHAR(32) COLLATE latin1_bin DEFAULT NULL,
                  MODIFY COLUMN `mkdate` INT(11) UNSIGNED DEFAULT NULL";
        DBManager::get()->execute($query);

        $query = "ALTER TABLE `studygroup_courses_proposals`
                  MODIFY COLUMN `studygroup_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                  MODIFY COLUMN `course_id` CHAR(32) COLLATE latin1_bin DEFAULT NULL,
                  MODIFY COLUMN `user_id` CHAR(32) COLLATE latin1_bin DEFAULT NULL,
                  MODIFY COLUMN `mkdate` INT(11) UNSIGNED DEFAULT NULL";
        DBManager::get()->execute($query);

        $query = "ALTER TABLE `tags`
                  MODIFY COLUMN `mkdate` INT(11) UNSIGNED DEFAULT NULL,
                  MODIFY COLUMN `chdate` INT(11) UNSIGNED DEFAULT NULL";
        DBManager::get()->execute($query);

        $query = "ALTER TABLE `tags_relations`
                  MODIFY COLUMN `tag_id` INT(11) UNSIGNED DEFAULT NULL,
                  MODIFY COLUMN `range_id` VARCHAR(32) COLLATE latin1_bin DEFAULT NULL,
                  MODIFY COLUMN `range_type` VARCHAR(100) COLLATE latin1_bin DEFAULT NULL,
                  MODIFY COLUMN `mkdate` INT(11) UNSIGNED DEFAULT NULL";
        DBManager::get()->execute($query);

        $query = "ALTER TABLE `seminare`
                  MODIFY COLUMN `expires` INT(11) UNSIGNED DEFAULT NULL";
        DBManager::get()->execute($query);

        $query = "ALTER TABLE `studygroup_stgteil`
                  MODIFY COLUMN `studygroup_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                  MODIFY COLUMN `stgteil_id` VARCHAR(32) COLLATE latin1_bin NOT NULL,
                  MODIFY COLUMN `mkdate` INT(11) UNSIGNED DEFAULT NULL";
        DBManager::get()->execute($query);
    }

    public function down()
    {
        $query = "ALTER TABLE `studygroup_stgteil`
                  MODIFY COLUMN `studygroup_id` CHAR(32) NOT NULL,
                  MODIFY COLUMN `stgteil_id` VARCHAR(32) NOT NULL,
                  MODIFY COLUMN `mkdate` INT(11) DEFAULT NULL";
        DBManager::get()->execute($query);

        $query = "ALTER TABLE `seminare`
                  MODIFY COLUMN `expires` INT(11) DEFAULT NULL";
        DBManager::get()->execute($query);

        $query = "ALTER TABLE `tags_relations`
                  MODIFY COLUMN `tag_id` INT(11) DEFAULT NULL,
                  MODIFY COLUMN `range_id` VARCHAR(32) DEFAULT NULL,
                  MODIFY COLUMN `range_type` VARCHAR(100) DEFAULT NULL,
                  MODIFY COLUMN `mkdate` INT(11) DEFAULT NULL";
        DBManager::get()->execute($query);

        $query = "ALTER TABLE `tags`
                  MODIFY COLUMN `mkdate` INT(11) DEFAULT NULL,
                  MODIFY COLUMN `chdate` INT(11) DEFAULT NULL";
        DBManager::get()->execute($query);

        $query = "ALTER TABLE `studygroup_courses_proposals`
                  MODIFY COLUMN `studygroup_id` CHAR(32) NOT NULL,
                  MODIFY COLUMN `course_id` CHAR(32) DEFAULT NULL,
                  MODIFY COLUMN `user_id` CHAR(32) DEFAULT NULL,
                  MODIFY COLUMN `mkdate` INT(11) DEFAULT NULL";
        DBManager::get()->execute($query);

        $query = "ALTER TABLE `studygroup_courses`
                  MODIFY COLUMN `studygroup_id` CHAR(32) NOT NULL,
                  MODIFY COLUMN `course_id` CHAR(32) DEFAULT NULL,
                  MODIFY COLUMN `mkdate` INT(11) DEFAULT NULL";
        DBManager::get()->execute($query);
    }
}

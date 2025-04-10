<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/5490
 */
final class AdjustScheduleTable extends Migration
{
    public function description()
    {
        return 'Fixes problems with migration 6.0.13';
    }

    public function up()
    {
        $query = "ALTER TABLE `schedule_courses`
                  MODIFY COLUMN `course_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                  MODIFY COLUMN `mkdate` INT(11) UNSIGNED NOT NULL DEFAULT 0,
                  MODIFY COLUMN `chdate` INT(11) UNSIGNED NOT NULL DEFAULT 0";
        DBManager::get()->execute($query);
    }

    public function down()
    {
        $query = "ALTER TABLE `schedule_courses`
                  MODIFY COLUMN `course_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                  MODIFY COLUMN `mkdate` BIGINT(10) NOT NULL DEFAULT 0,
                  MODIFY COLUMN `chdate` BIGINT(10) NOT NULL DEFAULT 0";
        DBManager::get()->execute($query);
    }
}

<?php


class AlterScheduleTable extends Migration
{
    public function description()
    {
        return 'Renames and alters the schedule table';
    }

    protected function up()
    {
        $db = DBManager::get();

        $db->exec("RENAME TABLE `schedule` TO `schedule_entries`");

        $db->exec(
            "ALTER TABLE `schedule_entries`
            DROP COLUMN color,
            CHANGE COLUMN start start_time SMALLINT(6) NOT NULL,
            CHANGE COLUMN end end_time SMALLINT(6) NOT NULL,
            CHANGE COLUMN day dow TINYINT(1) NOT NULL,
            CHANGE COLUMN title label VARCHAR(255) NOT NULL DEFAULT '',
            CHANGE COLUMN content content TEXT,
            ADD COLUMN mkdate BIGINT(10) NOT NULL DEFAULT 0,
            ADD COLUMN chdate BIGINT(10) NOT NULL DEFAULT 0"
        );

        $db->exec("RENAME TABLE `schedule_seminare` TO `schedule_courses`");
        $db->exec(
            "ALTER TABLE `schedule_courses`
            DROP COLUMN color,
            CHANGE COLUMN seminar_id course_id CHAR(32) NOT NULL,
            ADD COLUMN mkdate BIGINT(10) NOT NULL DEFAULT 0,
            ADD COLUMN chdate BIGINT(10) NOT NULL DEFAULT 0"
        );
    }

    protected function down()
    {
        $db = DBManager::get();

        $db->exec(
            "ALTER TABLE `schedule_courses`
            ADD COLUMN color TINYINT(4) NULL DEFAULT NULL,
            CHANGE COLUMN course_id seminar_id CHAR(32) NOT NULL,
            DROP COLUMN mkdate,
            DROP COLUMN chdate"
        );
        $db->exec("RENAME TABLE `schedule_courses` TO `schedule_seminare`");

        $db->exec(
            "ALTER TABLE `schedule_entries`
            ADD COLUMN color TINYINT(4) NULL DEFAULT NULL,
            CHANGE COLUMN start_time start SMALLINT(6) NOT NULL,
            CHANGE COLUMN end_time end SMALLINT(6) NOT NULL,
            CHANGE COLUMN dow day TINYINT(1) NOT NULL,
            CHANGE COLUMN label title VARCHAR(255) NOT NULL,
            CHANGE COLUMN content content VARCHAR(255) NOT NULL,
            DROP COLUMN mkdate,
            DROP COLUMN chdate"
        );
        $db->exec("RENAME TABLE `schedule_entries` TO `schedule`");
    }
}

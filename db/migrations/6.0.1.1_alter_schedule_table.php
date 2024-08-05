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

        $db->exec("RENAME TABLE IF EXISTS `schedule` TO `schedule_entries`");

        $db->exec(
            "ALTER IGNORE TABLE IF EXISTS `schedule_entries`
            DROP COLUMN color,
            CHANGE COLUMN start start_time SMALLINT(6) NOT NULL,
            CHANGE COLUMN end end_time SMALLINT(6) NOT NULL,
            CHANGE COLUMN day dow TINYINT(1) NOT NULL,
            CHANGE COLUMN title label VARCHAR(255) NOT NULL DEFAULT '',
            CHANGE COLUMN content content TEXT,
            ADD COLUMN mkdate BIGINT(10) NOT NULL DEFAULT 0,
            ADD COLUMN chdate BIGINT(10) NOT NULL DEFAULT 0"
        );

        $db->exec("RENAME TABLE IF EXISTS `schedule_seminare` TO `schedule_courses`");
        $db->exec(
            "ALTER IGNORE TABLE IF EXISTS `schedule_courses`
            DROP COLUMN color,
            CHANGE COLUMN seminar_id course_id CHAR(32) NOT NULL,
            ADD COLUMN mkdate BIGINT(10) NOT NULL DEFAULT 0,
            ADD COLUMN chdate BIGINT(10) NOT NULL DEFAULT 0"
        );
    }

    protected function down()
    {
        //Oh no! No! I'm too young to die! I'm too young and too handsome!
        //(looks into mirror)
        //Ny-aah! ... Well, I'm too young!
    }
}

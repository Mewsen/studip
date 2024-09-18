<?php


class RemoveCourseMappingByTimestamps extends Migration
{
    public function description()
    {
        return 'Removes the mapping of courses to semesters by timestamps (by removing seminare.start_time and seminare.duration_time).';
    }

    public function up()
    {
        $db = DBManager::get();
        $db->exec(
            "ALTER TABLE `seminare`
            DROP COLUMN `start_time`,
            DROP COLUMN `duration_time`"
        );
    }

    protected function down()
    {
        $db = DBManager::get();
        $db->exec(
            "ALTER TABLE `seminare`
            ADD COLUMN start_time INT(11) UNSIGNED NULL DEFAULT 0,
            ADD COLUMN duration_time INT(11) NULL DEFAULT NULL"
        );
    }
}

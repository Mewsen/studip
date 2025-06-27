<?php
final class RemoveMkdateChdateFromSemesterCoursesTable extends Migration
{
    public function description()
    {
        return 'Remove mkdate and chdate from semester_courses table';
    }

    public function up()
    {
        $query = "ALTER TABLE `semester_courses`
                  DROP COLUMN `mkdate`,
                  DROP COLUMN `chdate`";
        DBManager::get()->exec($query);
    }

    public function down()
    {
        $query = "ALTER TABLE `semester_courses`
                  ADD COLUMN `mkdate` INT(11) UNSIGNED NOT NULL DEFAULT 0,
                  ADD COLUMN `chdate` INT(11) UNSIGNED NOT NULL DEFAULT 0";
        DBManager::get()->exec($query);
    }
}

<?php

final class ChangeTimetableConfigEntryTypes extends Migration
{
    public function description()
    {
        return 'Sets correct types for timetable config entries.';
    }

    public function up()
    {
        DBManager::get()->exec("UPDATE `config` SET `type` = 'boolean'
            WHERE `field` IN ('TIMETABLE_COURSE_NUMBER_VISIBLE', 'TIMETABLE_COURSE_NAME_VISIBLE')");
    }
}

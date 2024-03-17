<?php

final class ResetMyCoursesFilter extends Migration
{
    public function description()
    {
        return 'Cleanup user-config';
    }

    public function up()
    {
        DBManager::get()->exec("UPDATE `config_values` SET `value` = '' WHERE `value` = 'all' AND `field` IN ('ADMIN_COURSES_TEACHERFILTER', 'MY_COURSES_SELECTED_CYCLE')");
    }
}

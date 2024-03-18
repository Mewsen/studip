<?php

final class ResetMyCoursesFilter2 extends Migration
{
    public function description()
    {
        return 'Cleanup user-config again';
    }

    public function up()
    {
        $query = "UPDATE `config_values`
                  SET `value` = ''
                  WHERE `value` = 'all'
                    AND `field` = 'MY_COURSES_SELECTED_STGTEIL'";
        DBManager::get()->exec($query);
    }
}

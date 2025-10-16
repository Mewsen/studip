<?php
final class AddConfigurationToAlwaysShowSemNumberOnMyCourses extends Migration
{
    public function description()
    {
        return 'Adds configuration MY_COURSES_ALWAYS_SHOW_SEMNUM';
    }

    protected function up()
    {
        $query = "INSERT IGNORE INTO `config` (
                    `field`, `value`, `type`, `range`,
                    `section`, `description`,
                    `mkdate`, `chdate`
                  ) VALUES (
                    'MY_COURSES_ALWAYS_SHOW_SEMNUM', 0, 'boolean', 'global',
                    'MeineVeranstaltungen', 'Zeigt die Veranstaltungsnummer immer auf \"Meine Veranstaltungen\" an',
                    UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
                  )";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'MY_COURSES_ALWAYS_SHOW_SEMNUM'";
        DBManager::get()->exec($query);
    }
}

<?php

final class AddMaxShowAdminCoursesConfig extends Migration
{
    public function description()
    {
        return 'Adds the configuration MAX_SHOW_ADMIN_COURSES, if it doesn\'t exist yet and set a default value.';
    }

    public function up()
    {
        DBManager::get()->exec("INSERT IGNORE INTO `config`
             (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
             VALUES
             (
              'MAX_SHOW_ADMIN_COURSES',
              500,
              'integer',
              'global',
              'MeineVeranstaltungen',
              UNIX_TIMESTAMP(),
              UNIX_TIMESTAMP(),
              'Wie viele Veranstaltungen sollen auf der Admin-Veranstaltungsseite angezeigt werden.'
             )"
        );
    }

    public function down()
    {
        DBManager::get()->exec("DELETE FROM `config_values` WHERE `field` = 'MAX_SHOW_ADMIN_COURSES'");
    }
}

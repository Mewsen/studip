<?php

final class AddLoginNewsConfig extends Migration
{
    public function description()
    {
        return 'Add configs for login news';
    }

    public function up()
    {
        $query = 'INSERT INTO `config` (`field`, `value`, `type`, `section`, `range`, `description`, `mkdate`, `chdate`)
                  VALUES (:name, :value, :type, :section, :range, :description, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())';
        $statement = DBManager::get()->prepare($query);
        $statement->execute([
            'name' => 'LOGIN_NEWS_VISIBILITY',
            'value' => '1',
            'type' => 'boolean',
            'section' => 'Loginseite',
            'range' => 'global',
            'description' => 'Soll Ankündigungs-Galerie auf der Loginseite sichtbar sein?'
        ]);
    }

    public function down()
    {
        $query = "DELETE `config`, `config_values`, `i18n`
        FROM `config`
        LEFT JOIN `config_values` USING (`field`)
        LEFT JOIN `i18n`
          ON `table` = 'config'
              AND `field` = 'value'
              AND `object_id` = MD5(`config`.`field`)
        WHERE `field` IN (
             'LOGIN_NEWS_VISIBILITY'
        )";
        DBManager::get()->exec($query);
    }
}
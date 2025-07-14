<?php

final class AddThemesConfig extends Migration
{
    public function description()
    {
        return 'Add configs for Stud.IP Themes';
    }

    public function up()
    {
        $query = 'INSERT INTO `config` (`field`, `value`, `type`, `section`, `range`, `description`, `mkdate`, `chdate`)
                  VALUES (:name, :value, :type, :section, :range, :description, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())';
        $statement = DBManager::get()->prepare($query);
        $statement->execute([
            'name' => 'STUDIP_THEME_LIGHT',
            'value' => '1',
            'type' => 'integer',
            'section' => 'Themes',
            'range' => 'global',
            'description' => 'Welches Theme soll im Light-Mode verwendet werden?'
        ]);
        $statement->execute([
            'name' => 'STUDIP_THEME_DARK',
            'value' => '2',
            'type' => 'integer',
            'section' => 'Themes',
            'range' => 'global',
            'description' => 'Welches Theme soll im Dark-Mode verwendet werden?'
        ]);
        $statement->execute([
            'name' => 'STUDIP_THEME_HIGH_CONTRAST',
            'value' => '3',
            'type' => 'integer',
            'section' => 'Themes',
            'range' => 'global',
            'description' => 'Welches Theme soll im High-Contrast-Mode verwendet werden?'
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
                       'STUDIP_THEME_LIGHT',
                       'STUDIP_THEME_DARK',
                       'STUDIP_THEME_HIGH_CONTRAST'
                  )";
        DBManager::get()->exec($query);
    }
}
<?php

/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/2795
 */
final class AddCustomizedHolidaysConfiguration extends Migration
{
    public function description()
    {
        return 'Adds a confiugration for customized holidays';
    }

    protected function up()
    {
        $query = "INSERT INTO `config` (
                      `field`, `value`, `type`, `range`, `section`,
                      `mkdate`, `chdate`,
                      `description`
                  ) VALUES (
                      'CUSTOMIZED_HOLIDAYS', '[]', 'array', 'global', 'global',
                      UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
                      'Speichert die internen Ids von Feiertagen, die als gesetztlich markiert werden sollen'
                  )";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'CUSTOMIZED_HOLIDAYS'";
        DBManager::get()->exec($query);
    }
}

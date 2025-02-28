<?php
final class RemoveSkype extends Migration
{
    public function description()
    {
        return 'Removes ENABLE_SKYPE_INFO configuration';
    }

    public function up()
    {
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'ENABLE_SKYPE_INFO'";
        DBManager::get()->exec($query);
    }

    public function down()
    {
        $query = "INSERT INTO `config` (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
                  VALUES ('ENABLE_SKYPE_INFO', '0', 'boolean', 'global', 'privacy', 1170242666, 1170242666, 'Ermöglicht die Eingabe / Anzeige eines Skype Namens ')";
        DBManager::get()->exec($query);
    }
}

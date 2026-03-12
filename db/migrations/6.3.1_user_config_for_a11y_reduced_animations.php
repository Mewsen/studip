<?php
final class UserConfigForA11yReducedAnimations extends Migration
{
    public function description()
    {
        return 'Creates new user config entry for reduced animations';
    }

    protected function up()
    {
        $query  = "INSERT INTO `config` (`field`, `type`, `value`, `range`, `section`, `mkdate`, `chdate`, `description`)
                   VALUES (?, ?, ?, ?, ?, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), ?)";
        DBManager::get()->execute($query, [
            'A11Y_USER_REDUCE_ANIMATIONS',
            'string',
            'default',
            'user',
            'accessibility',
            'Speichert, ob reduzierte Animationen verwendet werden sollen (mögliche Werte: default, yes, no)'
        ]);
    }

    protected function down()
    {
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'A11Y_USER_REDUCE_ANIMATIONS'";
        DBManager::get()->exec($query);
    }
}

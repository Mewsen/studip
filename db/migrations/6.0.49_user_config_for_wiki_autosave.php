<?php
final class UserConfigForWikiAutosave extends Migration
{
    public function description()
    {
        return 'Adds a user configuration for autostoring of wiki pages';
    }

    public function up()
    {
        $query = "INSERT IGNORE INTO `config` (
                    `field`, `value`, `type`, `range`, `section`,
                    `mkdate`, `chdate`,
                    `description`
                  ) VALUES (
                    'WIKI_ENABLE_AUTOSAVE', '1', 'boolean', 'user', 'wiki',
                    UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
                    'Aktiviert das automatische Speichern im Wiki'
                  )";
        DBManager::get()->exec($query);
    }

    public function down()
    {
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'WIKI_ENABLE_AUTOSAVE'";
        DBManager::get()->exec($query);
    }
}

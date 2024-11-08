<?php

final class RootAssistantConfigs extends Migration
{
    public function description()
    {
        return 'Creates configs for the root-assistant';
    }

    public function up()
    {
        $query = 'INSERT INTO `config` (`field`, `value`, `type`, `section`, `range`, `description`, `mkdate`, `chdate`)
                  VALUES (:name, :value, :type, :section, :range, :description, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())';
        $statement = DBManager::get()->prepare($query);
        $statement->execute([
            'name'          => 'SHOW_RELEASE_NOTES',
            'value'         => '1',
            'type'          => 'boolean',
            'section'       => 'Root-Assistent',
            'range'         => 'global',
            'description'   => 'Sollen die Release-Notes für root-User angezeigt werden?'
        ]);
        $statement->execute([
            'name'          => 'UPDATE_NEWS_SEEN',
            'value'         => '0',
            'type'          => 'boolean',
            'section'       => 'Root-Assistent',
            'range'         => 'global',
            'description'   => 'Bestätigung, dass die Update-Neuigkeiten gesehen wurden'
        ]);
        $statement->execute([
            'name'          => 'MIGRATION_START_TIME',
            'value'         => time(),
            'type'          => 'string',
            'section'       => 'Root-Assistent',
            'range'         => 'global',
            'description'   => 'Speichert die Startzeit (Timestamp) der letzten Migration'
        ]);

        $statement->execute([
            'name'          => 'MIGRATION_START_VERSION',
            'value'         => '5.5',
            'type'          => 'string',
            'section'       => 'Root-Assistent',
            'range'         => 'global',
            'description'   => 'Speichert die jeweilige Stud.IP-Version beim Start der Migration'
        ]);

    }

    public function down()
    {
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` IN (
                     'MIGRATION_START_TIME',
                     'MIGRATION_START_VERSION',
                     'SHOW_RELEASE_NOTES',
                     'UPDATE_NEWS_SEEN'
                  )";
        DBManager::get()->exec($query);
    }
}

<?php
final class RemoveRestapi extends Migration
{
    private Migration $other_migration;

    public function __construct($verbose = false)
    {
        parent::__construct($verbose);

        require_once __DIR__ . '/1.127_setup_api.php';
        $this->other_migration = new SetupApi($verbose);
    }

    public function description()
    {
        return 'Removes the deprecated REST API (essentially reverts migration 1.127)';
    }

    protected function up()
    {
        $this->other_migration->dropTables();

        // Delete config
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING(`field`)
                  WHERE `field` IN ('API_ENABLED', 'API_OAUTH_AUTH_PLUGIN')";
        DBManager::get()->exec($query);

        // Disable all RESTAPI-Plugins
        $query = "UPDATE `plugins`
                  SET `enabled` = 'no'
                  WHERE FIND_IN_SET('RESTAPIPlugin', `plugintype`)";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        // Add config entries
        $query = "INSERT IGNORE INTO `config`
                    (`field`, `value`, `type`, `range`, `section`,
                     `mkdate`, `chdate`, `description`)
                  VALUES (:field, :value, :type, 'global', 'global',
                          UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)";
        $statement = DBManager::get()->prepare($query);

        $statement->execute([
            ':field' => 'API_ENABLED',
            ':value' => 0,
            ':type'  => 'boolean',
            ':description' => 'Schaltet die REST-API an',
        ]);

        $statement->execute([
            ':field'       => 'API_OAUTH_AUTH_PLUGIN',
            ':value'       => 'Standard',
            ':type'        => 'string',
            ':description' => 'Definiert das für OAuth verwendete Authentifizierungsverfahren',
        ]);

        $this->other_migration->createTables();
    }
}

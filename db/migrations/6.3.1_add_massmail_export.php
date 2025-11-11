<?php
final class AddMassmailExport extends Migration
{
    public function description()
    {
        return 'Adds a global configuration for enabling the export of massmail recipient lists';
    }

    public function up()
    {
        $query = "INSERT IGNORE INTO `config` (
                    `field`, `value`, `type`, `range`, `section`,
                    `mkdate`, `chdate`,
                    `description`
                  ) VALUES (
                    'MASSMAIL_EXPORT_RECIPIENTS_ENABLE', '0', 'boolean', 'global', 'MassMail',
                    UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
                    'Schaltet den Export von Emofängerlisten für Massenmails aus oder ein'
                  )";
        DBManager::get()->exec($query);
    }

    public function down()
    {
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'MASSMAIL_EXPORT_RECIPIENTS_ENABLE'";
        DBManager::get()->exec($query);
    }
}

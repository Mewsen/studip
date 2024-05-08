<?php
return new class extends Migration
{
    public function description(): string
    {
        return 'Creates a config entry for the key used for captchas and '
             . 'db storage for solved challenges.';
    }

    protected function up(): void
    {
        $query = "INSERT INTO `config` (`field`, `value`, `type`, `range`, `mkdate`, `chdate`, `description`)
                  VALUES ('CAPTCHA_KEY', '', 'string', 'global', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), ?)";
        DBManager::get()->execute($query, [
            'Speichert den für Captchas verwendeten Schlüssel (Wert leeren, um einen neuen zu generieren)',
        ]);

        $query = "CREATE TABLE `captcha_challenges` (
                      `challenge_id` int(11) NOT NULL AUTO_INCREMENT,
                      `salt` CHAR(32) COLLATE `latin1_bin` NOT NULL,
                      `number` INT(11) UNSIGNED NOT NULL,
                      `mkdate` INT(11) UNSIGNED NOT NULL,
                      PRIMARY KEY (`challenge_id`)                        
                  )";
        DBManager::get()->exec($query);
    }

    protected function down(): void
    {
        $query = "DROP TABLE `captcha_challenges`";
        DBManager::get()->exec($query);

        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'CAPTCHA_KEY'";
        DBManager::get()->exec($query);
    }
};

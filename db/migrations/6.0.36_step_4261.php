<?php

final class Step4261 extends Migration
{
    public function description()
    {
        return 'Add field to module table to store original language.';
    }

    protected function up()
    {
        $db = DBManager::get();

        // retrieve default language from config
        $config_language = $db->fetchColumn(
            "SELECT `value` FROM `config` WHERE `field` = 'DEFAULT_LANGUAGE'"
        );
        $default_language = $config_language ?? array_keys($GLOBALS['CONTENT_LANGUAGES'])[0] ?? 'de_DE';
        $db->execute(
            'ALTER TABLE `mvv_modul`
            ADD `original_language` VARCHAR(10) NOT NULL DEFAULT ? COLLATE latin1_bin AFTER `verantwortlich`',
            [$default_language]
        );

        $query = "INSERT INTO `config` (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
                  VALUES ('MVV_DEFAULT_LANGUAGE', ?, 'string', 'global', 'mvv', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), ?)";
        $db->execute($query,
            [
                $default_language,
                'Code der Inhalts-Sprache, die als Original-Sprache der Deskriptoren für Module und Modulteile vorausgewählt ist.',
            ]
        );
    }

    protected function down()
    {
        $db = DBManager::get();

        $db->exec(
            "ALTER TABLE `mvv_modul`
            DROP COLUMN `original_language`"
        );

        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'MVV_DEFAULT_LANGUAGE'";
        $db->exec($query);
    }
}

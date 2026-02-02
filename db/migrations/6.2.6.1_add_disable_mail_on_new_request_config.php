<?php


class AddDisableMailOnNewRequestConfig extends Migration
{
    public function description()
    {
        return 'Adds the personal configuration RESOURCES_DISABLE_MAIL_ON_NEW_REQUEST.';
    }

    protected function up()
    {
        $db = DBManager::get();
        $db->exec(
            "INSERT INTO `config`
            (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
            VALUES
            ('RESOURCES_DISABLE_MAIL_ON_NEW_REQUEST', '0', 'boolean', 'user', 'resources',
            UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
            'Schaltet den Versand von Mails bei neuen Raumanfragen aus.')"
        );
    }

    protected function down()
    {
        $db = DBManager::get();
        $db->exec(
            "DELETE FROM `config_values`
            WHERE `field` = 'RESOURCES_DISABLE_MAIL_ON_NEW_REQUEST'"
        );
        $db->exec(
            "DELETE FROM `config`
            WHERE `field` = 'RESOURCES_DISABLE_MAIL_ON_NEW_REQUEST'"
        );
    }
}

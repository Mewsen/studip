<?php
class NotificationPositioning extends Migration
{
    public function description()
    {
        return 'Adds a personal config option to select the on-screen position of system notifications';
    }

    protected function up()
    {
        DBManager::get()->execute(
            "INSERT IGNORE INTO `config` VALUES (:field, :value, :type, :range, :section, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :desc)",
            [
                'field' => 'SYSTEM_NOTIFICATIONS_PLACEMENT',
                'value' => 'topcenter',
                'type' => 'string',
                'range' => 'user',
                'section' => '',
                'desc' => 'Wo sollen Systembenachrichtigungen im Fenster angezeigt werden? Gültige Werte sind "topcenter" und "bottomright"'
            ]
        );
    }

    protected function down()
    {
        DBManager::get()->execute(
            "DELETE FROM `config_values` WHERE `field` = :field",
            ['field' => 'SYSTEM_NOTIFICATIONS_PLACEMENT']
        );
        DBManager::get()->execute(
            "DELETE FROM `config` WHERE `field` = :field",
            ['field' => 'SYSTEM_NOTIFICATIONS_PLACEMENT']
        );
    }
};

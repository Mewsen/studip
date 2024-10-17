<?php
final class AddPersonalNotificationConfigurations extends Migration
{
    public function description()
    {
        return 'Adds missing configurations for "PERSONAL_NOTIFICATIONS_DEACTIVATED" and "PERSONAL_NOTIFICATIONS_AUDIO_DEACTIVATED"';
    }

    protected function up()
    {
        $query = "INSERT IGNORE INTO `config`
                  (`field`, `value`, `type`, `range`, `mkdate`, `chdate`, `description`)
                  VALUES (:field, :value, 'boolean', 'user', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)";
        $statement = DBManager::get()->prepare($query);

        $statement->execute([
            ':field' => 'PERSONAL_NOTIFICATIONS_DEACTIVATED',
            ':value' => '0',
            ':description' => 'Deaktiviert die persönlichen Benachrichtigungen',
        ]);

        $statement->execute([
            ':field' => 'PERSONAL_NOTIFICATIONS_AUDIO_DEACTIVATED',
            ':value' => '0',
            ':description' => 'Deaktiviert das Abspielen von Tönen für die persönlichen Benachrichtigungen',
        ]);
    }

    protected function down()
    {
        $query = "DELETE FROM `config`
                  WHERE `field` IN (
                    'PERSONAL_NOTIFICATIONS_DEACTIVATED',
                    'PERSONAL_NOTIFICATIONS_AUDIO_DEACTIVATED'
                  )";
        DBManager::get()->execute($query);
    }
}

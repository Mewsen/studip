<?php

final class DisableAudioNotificationsForAll extends Migration
{
    public function description()
    {
        return 'Disable audio notifications for all users so that asking for audio permissions is triggered on re-activating';
    }

    protected function up()
    {
        // Deactive audio feedback per default setting.
        DBManager::get()->execute(
            "INSERT IGNORE INTO `config`
            (`field`, `value`, `type`, `range`, `mkdate`, `chdate`, `description`)
            VALUES
            (:field, :value, :type, :range, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)",
            [
                'field' => 'PERSONAL_NOTIFICATIONS_AUDIO_DEACTIVATED',
                'value' => 1,
                'type' => 'boolean',
                'range' => 'user',
                'description' => 'Audio-Feedback zu Benachrichtigungen abschalten'
            ]
        );

        // Query default language
        $query = "SELECT IFNULL(`config_values`.`value`, `config`.`value`)
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'DEFAULT_LANGUAGE'";
        $default_language = DBManager::get()->fetchColumn($query);

        // Send notifications to users to inform of the change
        $query = "SELECT IF(`user_info`.`preferred_language` = '', ?, `user_info`.`preferred_language`),
                         `user_id`
                  FROM `auth_user_md5`
                  JOIN `user_info` USING (`user_id`)
                  LEFT JOIN `config_values`
                    ON `range_id` = `user_id`
                       AND `field` = 'PERSONAL_NOTIFICATIONS_AUDIO_DEACTIVATED'
                       AND `value` = '1'
                  WHERE `config_values`.`range_id` IS NULL";
        DBManager::get()->fetchGroupedPairs(
            $query,
            [$default_language],
            function ($user_ids, $language) {
                $message = 'Aus technischen Gründen wurde das Audio-Feedback zu '
                         . 'Benachrichtigungen in Ihrem Konto deaktiviert. Bitte '
                         . 'aktivieren Sie dieses erneut, wenn Sie es weiterhin '
                         . 'nutzen möchten.';
                if (str_starts_with($language, 'en')) {
                    $message = 'For technical reasons, the audio feedback for '
                             . 'notifications has been deactivated in your account. '
                             . 'Please reactivate it if you wish to continue using it.';
                }

                // Create notification
                $query = "INSERT INTO `personal_notifications`
                          (`url`, `text`, `avatar`, `mkdate`)
                          VALUES (?, ?, ?, UNIX_TIMESTAMP())";
                DBManager::get()->execute($query, [
                    URLHelper::getURL('dispatch.php/settings/general'),
                    $message,
                    Icon::create('audio2')->asImagePath()
                ]);
                $id = DBManager::get()->lastInsertId();

                // Assign users
                $query = "INSERT INTO `personal_notifications_user`
                          (`personal_notification_id`, `user_id`)
                          VALUES (?, ?)";
                foreach ($user_ids as $user_id) {
                    DBManager::get()->execute($query, [$id, $user_id]);
                }
            }
        );

        // Reset all users to default.
        DBManager::get()->exec("DELETE FROM `config_values` WHERE `field` = 'PERSONAL_NOTIFICATIONS_AUDIO_DEACTIVATED'");
    }
}

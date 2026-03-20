<?php

final class Step5405 extends Migration {
    public function description()
    {
        return 'Add and modify LTI Tables';
    }

    public function up()
    {
        DBManager::get()->exec("
            ALTER TABLE `lti_tools` MODIFY `id` INT UNSIGNED NOT NULL AUTO_INCREMENT
        ");

        DBManager::get()->execute("
            ALTER TABLE `lti_tools` RENAME TO `lti_registrations`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registrations` CHANGE `lti_version` `version` ENUM('1.1', '1.3a') NOT NULL DEFAULT '1.3a'
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registrations`
            ADD COLUMN `description` TEXT NOT NULL AFTER `name`,
            ADD COLUMN `role` ENUM('tool', 'platform') NOT NULL DEFAULT 'tool' AFTER `description`,
            ADD COLUMN `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'inactive' AFTER `role`,
            ADD COLUMN `user_id` CHAR(32) COLLATE latin1_bin NOT NULL AFTER `range_id`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registrations` MODIFY `mkdate` INT UNSIGNED DEFAULT NULL AFTER `user_id`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registrations` MODIFY `chdate` INT UNSIGNED DEFAULT NULL AFTER `mkdate`
        ");

        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `lti_publications` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `publication_key` VARCHAR(64) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'inactive',
                `version` ENUM('1.1', '1.3a') NOT NULL DEFAULT '1.3a',
                `range_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                `user_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                `mkdate` INT UNSIGNED DEFAULT NULL,
                `chdate` INT UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_lti_publications_key` (`publication_key`),
                KEY `idx_lti_publications_range_id` (`range_id`),
                KEY `idx_lti_publications_user_id` (`user_id`)
            )
        ");

        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `lti_publication_users` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `publication_id` INT UNSIGNED NOT NULL,
                `user_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                `mkdate` INT UNSIGNED DEFAULT NULL,
                `chdate` INT UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_lti_pub_users_publication_id` (`publication_id`),
                CONSTRAINT `fk_lti_pub_users_publication`
                    FOREIGN KEY (`publication_id`)
                    REFERENCES `lti_publications` (`id`)
                    ON DELETE CASCADE,
                CONSTRAINT `fk_lti_pub_users_user`
                    FOREIGN KEY (`user_id`)
                    REFERENCES `auth_user_md5` (`user_id`)
                    ON DELETE CASCADE
            )
        ");

        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `lti_user_identity_mappings` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                `registration_id` INT UNSIGNED DEFAULT NULL,
                `external_user_id` VARCHAR(255) NOT NULL,
                `external_email` VARCHAR(255),
                `context` ENUM('deep_linking','resource_link') NOT NULL DEFAULT 'resource_link',
                `mkdate` INT UNSIGNED DEFAULT NULL,
                `chdate` INT UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_lti_user_identity_mappings_registration_id` (`registration_id`),
                KEY `idx_lti_user_identity_mappings_user_id` (`user_id`),
                CONSTRAINT `fk_lti_user_identity_mappings_registration`
                    FOREIGN KEY (`registration_id`)
                    REFERENCES `lti_registrations` (`id`)
                    ON DELETE SET NULL,
                CONSTRAINT `fk_lti_user_identity_mappings_user`
                    FOREIGN KEY (`user_id`)
                    REFERENCES `auth_user_md5` (`user_id`)
                    ON DELETE CASCADE
            )
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` CHANGE `tool_id` `registration_id` INT UNSIGNED NOT NULL
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` MODIFY COLUMN `name` VARCHAR(255) AFTER `id`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` MODIFY COLUMN `purpose` ENUM('general','deep_linking') NOT NULL DEFAULT 'general' AFTER `name`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` ADD COLUMN `deployment_key` VARCHAR(255) AFTER `registration_id`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` ADD COLUMN `client_id` VARCHAR(64) NOT NULL AFTER `deployment_key`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` ADD COLUMN `is_default` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `purpose`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` ADD KEY `idx_lti_deployments_registration_id` (`registration_id`)
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` ADD CONSTRAINT `fk_lti_deployments_registration`
                FOREIGN KEY (`registration_id`)
                REFERENCES `lti_registrations` (`id`)
                ON DELETE CASCADE
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_tool_privacy_settings` RENAME TO `lti_registration_privacy_settings`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registration_privacy_settings` DROP PRIMARY KEY
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registration_privacy_settings` CHANGE `tool_id` `registration_id` INT UNSIGNED NOT NULL AFTER `user_id`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registration_privacy_settings` ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY AFTER `user_id`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registration_privacy_settings` MODIFY COLUMN `user_id` CHAR(32) COLLATE latin1_bin NOT NULL AFTER `id`
        ");

        DBManager::get()->exec("
           ALTER TABLE `lti_registration_privacy_settings` ADD INDEX `idx_user_id` (`user_id`)
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_resource_links` ADD COLUMN `custom_parameters` TEXT AFTER `launch_url`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_resource_links` MODIFY `mkdate` INT UNSIGNED DEFAULT NULL AFTER `options`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_resource_links` MODIFY `chdate` INT UNSIGNED DEFAULT NULL AFTER `mkdate`
        ");

        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `lti_configs` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `configurable_id` INT UNSIGNED NOT NULL,
                `configurable_type` VARCHAR(255) NOT NULL,
                `name` VARCHAR(100) NOT NULL,
                `value` TEXT NOT NULL,
                `mkdate` INT UNSIGNED DEFAULT NULL,
                `chdate` INT UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_lti_configs_configurable_id` (`configurable_id`)
            )
        ");

        $addConfig = DBManager::get()->prepare(
            "INSERT INTO `config`
            (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
            VALUES
            (:field, :value, :type, :range, :section, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)"
        );

        $addConfig->execute([
            'field'       => 'ENABLE_SHARING_COURSES_AS_LTI_TOOLS',
            'value'       => '0',
            'type'        => 'boolean',
            'range'       => 'global',
            'section'     => 'LTI',
            'description' => 'Sollen Veranstaltungen als LTI-Tools freigegeben werden können?'
        ]);

        $addConfig->execute([
            'field'       => 'SHARE_COURSE_AS_LTI_TOOL',
            'value'       => '0',
            'type'        => 'boolean',
            'range'       => 'course',
            'section'     => 'LTI',
            'description' => 'Soll die Veranstaltung als LTI-Tool freigegeben werden?'
        ]);

        $this->migrateData();
    }

    public function down()
    {
        DBManager::get()->exec("
            DROP TABLE IF EXISTS
                `lti_configs`,
                `lti_user_identity_mappings`,
                `lti_registrations`,
                `lti_publication_users`,
                `lti_publications`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` DROP KEY `idx_lti_deployments_registration_id`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` DROP FOREIGN KEY `fk_lti_deployments_registration`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` CHANGE `registration_id` `tool_id` INT UNSIGNED NOT NULL
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments`
                DROP COLUMN `deployment_key`,
                DROP COLUMN `client_id`,
                DROP COLUMN `is_default`;
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` CHANGE `registration_id` `tool_id` INT UNSIGNED NOT NULL
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registration_privacy_settings` CHANGE `registration_id` `tool_id` INT UNSIGNED NOT NULL AFTER `user_id`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registration_privacy_settings` DROP COLUMN `id`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registration_privacy_settings` DROP INDEX `idx_user_id`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_registration_privacy_settings` RENAME TO `lti_tool_privacy_settings`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_resource_links`
                DROP COLUMN `color`,
                DROP COLUMN `icon`;
        ");

        DBManager::get()->exec(
            "DELETE FROM `config_values` WHERE `field` IN
                (
                    'ENABLE_SHARING_COURSES_AS_LTI_TOOLS',
                    'SHARE_COURSE_AS_LTI_TOOL',
                    'LTI_TOOL_ENTRY_POINT'
                )"
        );

        DBManager::get()->exec(
            "DELETE FROM `config` WHERE `field` IN
                (
                    'ENABLE_SHARING_COURSES_AS_LTI_TOOLS',
                    'SHARE_COURSE_AS_LTI_TOOL'
                )"
        );
    }

    private function migrateData(): void
    {
        $db = DBManager::get();

        $ltiTools = $db->fetchAll("SELECT * FROM `lti_registrations`");

        foreach ($ltiTools as $tool) {
            $configs = array_filter([
                'launch_container' => 'window',
                'launch_url' => $tool['launch_url'],
                'jwks_url' => $tool['jwks_url'],
                'jwks_key_id' => $tool['jwks_key_id'],
                'deep_linking_url' => $tool['deep_linking_url'],
                'data_protection_notes' => $tool['data_protection_notes'],
                'privacy_policy_url' => $tool['privacy_policy_url'],
                'terms_of_use_url' => $tool['terms_of_use_url'],
                'consumer_key' => $tool['consumer_key'],
                'consumer_secret' => $tool['consumer_secret'],
                'custom_parameters' => $tool['custom_parameters'],
                'send_lis_person' => $tool['send_lis_person'],
                'allow_custom_url' => $tool['allow_custom_url'],
                'oauth_signature_method' => $tool['oauth_signature_method'],
                'auth_init_url' => $tool['oidc_init_url']
            ], static fn($value) => $value !== null);

            foreach ($configs as $configKey => $configValue) {
                $db->prepare("
                        INSERT INTO `lti_configs`
                        (`configurable_id`, `configurable_type`, `name`, `value`, `mkdate`, `chdate`)
                        VALUES
                        (:configurable_id, :configurable_type, :name, :value, :mkdate, :chdate)
                    ")->execute([
                        'configurable_id' => (int) $tool['id'],
                        'configurable_type' => 'Lti\Registration',
                        'name' => $configKey,
                        'value' => $configValue,
                        'mkdate' => $tool['mkdate'],
                        'chdate' => $tool['chdate']
                    ]);
            }
        }

        $db->exec("
            UPDATE `lti_registrations` SET `status` = 'active'
        ");

        $db->exec("
            UPDATE `lti_deployments` SET `name` = 'Standard-Deployment', `is_default`= 1, `deployment_key` = `id`, `client_id` = `registration_id`
        ");

        $db->exec("
            DELETE FROM lti_deployments WHERE purpose = 'deep_linking'
        ");

        $db->prepare("
            DELETE FROM `lti_resource_links` WHERE `options` = :options
        ")
        ->execute([
            'options' => '{"unfinished_deep_linking":"true"}'
        ]);

        $columnsToRemove = [
            'launch_url',
            'jwks_url',
            'jwks_key_id',
            'deep_linking_url',
            'data_protection_notes',
            'privacy_policy_url',
            'terms_of_use_url',
            'consumer_key',
            'consumer_secret',
            'custom_parameters',
            'send_lis_person',
            'allow_custom_url',
            'oauth_signature_method',
            'oidc_init_url',
            'deep_linking',
            'oauth2_client_id'
        ];

        foreach ($columnsToRemove as $column) {
            $db->exec("
                ALTER TABLE `lti_registrations` DROP COLUMN `$column`
            ");
        }
    }
}

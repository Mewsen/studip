<?php

final class Step5405 extends Migration {
    public function description()
    {
        return 'Add and modify LTI Tables';
    }

    public function up()
    {
        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `lti_registrations` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `role` ENUM('tool', 'platform') NOT NULL DEFAULT 'tool',
                `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'inactive',
                `version` ENUM('1.1', '1.3a') NOT NULL DEFAULT '1.3a',
                `range_id` CHAR(32) COLLATE latin1_bin NOT NULL Default 'global',
                `user_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                `mkdate` INT UNSIGNED DEFAULT NULL,
                `chdate` INT UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_lti_registrations_range_id` (`range_id`),
                KEY `idx_lti_registrations_user_id` (`user_id`)
            )
        ");

        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `lti_registration_configs` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `registration_id` INT UNSIGNED NOT NULL,
                `name` VARCHAR(100) NOT NULL,
                `value` TEXT NOT NULL,
                `mkdate` INT UNSIGNED DEFAULT NULL,
                `chdate` INT UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_lti_reg_configs_registration_id` (`registration_id`),
                CONSTRAINT `fk_lti_reg_configs_registration`
                    FOREIGN KEY (`registration_id`)
                    REFERENCES `lti_registrations` (`id`)
                    ON DELETE CASCADE
            )
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
            CREATE TABLE IF NOT EXISTS `lti_publication_configs` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `publication_id` INT UNSIGNED NOT NULL,
                `name` VARCHAR(100) NOT NULL,
                `value` TEXT NOT NULL,
                `mkdate` INT UNSIGNED DEFAULT NULL,
                `chdate` INT UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_lti_pub_users_publication_id` (`publication_id`),
                CONSTRAINT `fk_lti_pub_users_publication`
                    FOREIGN KEY (`publication_id`)
                    REFERENCES `lti_publications` (`id`)
                    ON DELETE CASCADE
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
            ALTER TABLE `lti_resource_links`
                ADD COLUMN `custom_parameters` TEXT AFTER `options`,
                ADD COLUMN `launch_type` ENUM('default','deep_linking') NOT NULL DEFAULT 'default' AFTER `custom_parameters`,
                ADD COLUMN `launch_container` ENUM('window','iframe') NOT NULL DEFAULT 'window' AFTER `launch_type`,
                ADD COLUMN `color` VARCHAR(7) AFTER `launch_container`,
                ADD COLUMN `icon` VARCHAR(100) AFTER `color`
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
    }

    public function down()
    {
        DBManager::get()->exec("
            DROP TABLE IF EXISTS
                `lti_registration_configs`,
                `lti_registrations`,
                `lti_publication_configs`,
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
                DROP COLUMN `launch_type`,
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
}

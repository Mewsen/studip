<?php

final class Step5405 extends Migration {
    public function description()
    {
        return 'Add Lti Registration Tables';
    }

    public function up()
    {
        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `lti_registrations` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `data_protection_notes` TEXT NOT NULL,
                `terms_of_use_url` VARCHAR(2048) NOT NULL,
                `privacy_policy_url` VARCHAR(2048) NOT NULL,
                `role` ENUM('tool', 'platform') NOT NULL DEFAULT 'tool',
                `client_id` VARCHAR(64) NOT NULL,
                `state` TINYINT UNSIGNED NOT NULL DEFAULT 0,
                `version` ENUM('1.1', '1.3a') NOT NULL DEFAULT '1.3a',
                `mkdate` INT UNSIGNED DEFAULT NULL,
                `chdate` INT UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`id`)
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
                INDEX `idx_registration_id` (`registration_id`),
                CONSTRAINT `fk_lti_configs_registration`
                    FOREIGN KEY (`registration_id`)
                    REFERENCES `lti_registrations` (`id`)
                    ON DELETE CASCADE
            )
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` CHANGE `tool_id` `registration_id` INT UNSIGNED NOT NULL;
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` ADD INDEX `idx_registration_id` (`registration_id`);
            ALTER TABLE `lti_deployments` ADD CONSTRAINT `fk_deployment_registration`
                FOREIGN KEY (`registration_id`)
                REFERENCES `lti_registrations` (`id`)
                ON DELETE CASCADE;
        ");
    }

    public function down()
    {
        DBManager::get()->exec("
            DROP TABLE IF EXISTS
                `lti_registration_configs`,
                `lti_registrations`
        ");

        DBManager::get()->exec("
            ALTER TABLE `lti_deployments` DROP INDEX `idx_registration_id`;
            ALTER TABLE `lti_deployments` DROP FOREIGN KEY `fk_deployment_registration`;
            ALTER TABLE `lti_deployments` CHANGE `registration_id` `tool_id` INT UNSIGNED NOT NULL;
        ");
    }
}

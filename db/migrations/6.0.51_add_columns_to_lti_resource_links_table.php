<?php


class AddColumnsToLtiResourceLinksTable extends Migration
{
    public function description()
    {
        return 'Add missing columns to the lti_resource_links table.';
    }

    protected function up()
    {
        $db = DBManager::get();

        //Clone the launch_url and options column from lti_deployments:
        $db->exec(
            "ALTER TABLE `lti_resource_links`
            ADD COLUMN `title` VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN `description` TEXT NULL,
            ADD COLUMN `launch_url` VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN `options` TEXT"
        );

        $db->exec(
            "UPDATE `lti_resource_links`
            JOIN `lti_deployments`
            ON `lti_deployments`.`id` = `lti_resource_links`.`deployment_id`
            SET
            `lti_resource_links`.`title`       = `lti_deployments`.`title`,
            `lti_resource_links`.`description` = `lti_deployments`.`description`,
            `lti_resource_links`.`launch_url`  = `lti_deployments`.`launch_url`,
            `lti_resource_links`.`options`     = `lti_deployments`.`options`"
        );

        $db->exec(
            "ALTER TABLE `lti_deployments`
            DROP COLUMN `title`,
            DROP COLUMN `description`,
            DROP COLUMN `launch_url`,
            DROP COLUMN `options`,
            ADD COLUMN `purpose` ENUM ('general', 'deep_linking') NOT NULL DEFAULT 'general',
            ADD COLUMN `name` VARCHAR(255) NOT NULL DEFAULT ''"
        );
    }

    protected function down()
    {
        $db = DBManager::get();

        $db->exec(
            "ALTER TABLE `lti_deployments`
            DROP COLUMN `name`,
            DROP COLUMN `purpose`,
            ADD COLUMN `title` VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN `description` TEXT NULL,
            ADD COLUMN `launch_url` VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN `options` TEXT"
        );

        $db->exec(
            "UPDATE `lti_deployments`
            JOIN `lti_resource_links`
            ON `lti_deployments`.`id` = `lti_resource_links`.`deployment_id`
            SET
            `lti_deployments`.`title`       = `lti_resource_links`.`title`,
            `lti_deployments`.`description` = `lti_resource_links`.`description`,
            `lti_deployments`.`launch_url`  = `lti_resource_links`.`launch_url`,
            `lti_deployments`.`options`     = `lti_resource_links`.`options`"
        );

        $db->exec(
            "ALTER TABLE `lti_resource_links`
            DROP COLUMN `title`,
            DROP COLUMN `description`,
            DROP COLUMN launch_url,
            DROP COLUMN `options`"
        );
    }
}

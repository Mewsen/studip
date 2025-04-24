<?php


class AddLtiResourceLinksTable extends Migration
{
    public function description()
    {
        return 'Creates the lti_resource_links table and moves colums from the lti_deployments table into it.';
    }

    protected function up()
    {
        $db = DBManager::get();

        //Create the lti_resource_links table:
        $db->exec(
            "CREATE TABLE IF NOT EXISTS lti_resource_links (
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                deployment_id INT(11) NOT NULL,
                course_id CHAR(32) NOT NULL,
                position INT(11) NOT NULL DEFAULT 0,
                mkdate INT(11) NOT NULL DEFAULT 0,
                chdate INT(11) NOT NULL DEFAULT 0
            )"
        );
        $db->exec("ALTER TABLE `lti_resource_links` ADD INDEX (`deployment_id`)");

        //Migrate the contents of lti_deployments:
        $db->exec(
            "INSERT INTO `lti_resource_links` (`deployment_id`, `course_id`, `position`, `mkdate`, `chdate`)
            SELECT `id`, `course_id`, `position`, `mkdate`, `chdate` FROM `lti_deployments`"
        );

        //Remove columns from lti_deployments:
        $db->exec("ALTER TABLE `lti_deployments` DROP COLUMN `course_id`, DROP COLUMN `position`");
    }

    protected function down()
    {
        $db = DBManager::get();

        //Add columns to lti_deployments:
        $db->exec(
            "ALTER TABLE `lti_deployments`
             ADD COLUMN `course_id` CHAR(32) NOT NULL,
             ADD COLUMN `position` INT(11) NOT NULL DEFAULT 0"
        );

        //Migrate the content of lti_resource_links:
        $db->exec(
            "UPDATE `lti_deployments` JOIN `lti_resource_links`
            ON `lti_deployments`.`id` = `lti_resource_links`.`deployment_id`
            SET
            `lti_deployments`.`course_id` = `lti_resource_links`.`course_id`,
            `lti_deployments`.`position`  = `lti_resource_links`.`position`"
        );

        //Remove the lti_resource_links table:
        $db->exec("DROP TABLE `lti_resource_links`");
    }
}

<?php
class Step4269 extends Migration
{
    public function description()
    {
        return "Adds ilias workgroup member request table.";
    }

    public function up()
    {
        $db = \DBManager::get();

        $db->exec(
            "CREATE TABLE `ilias_workgroup_request`(
                 `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                 `workgroup_id` INT(11) NOT NULL,
                 `ilias_index` VARCHAR(32) NOT NULL,
                 `user_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                 `valid_until` INT(11) UNSIGNED NOT NULL,
                 `mkdate` INT(11) UNSIGNED NOT NULL,
                 `chdate` INT(11) UNSIGNED NOT NULL,
                 PRIMARY KEY(`id`),
                 UNIQUE KEY `workgroup_user` (`ilias_index`,`user_id`,`workgroup_id`)
             )"
        );
    }

    public function down()
    {
        $db = \DBManager::get();
        $db->exec('DROP TABLE IF EXISTS `ilias_workgroup_request`');
    }
}
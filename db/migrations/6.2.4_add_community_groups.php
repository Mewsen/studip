<?php

final class AddCommunityGroups extends Migration
{
    public function description()
    {
        return '';
    }

    public function up()
    {
        $db = DBManager::get();
        $db->exec("CREATE TABLE IF NOT EXISTS `community_groups` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `creator_id` CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
                `is_private` TINYINT(1) NOT NULL,
                `status` ENUM('active', 'archived', 'deleted') COLLATE latin1_bin NOT NULL,
                `mkdate` INT(11) UNSIGNED NOT NULL,
                `chdate` INT(11) UNSIGNED NOT NULL,
                PRIMARY KEY(`id`),
                INDEX `creator_idx` (`creator_id`),
                INDEX `status_idx` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $db->exec("CREATE TABLE IF NOT EXISTS `community_group_participants` (
                `group_id` INT(11) NOT NULL,
                `user_id` CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
                `role` ENUM('moderator', 'follower') COLLATE latin1_bin NOT NULL,
                `status` ENUM('member', 'pending', 'banned') COLLATE latin1_bin NOT NULL,
                `mkdate` INT(11) UNSIGNED NOT NULL,
                `chdate` INT(11) UNSIGNED NOT NULL,
                PRIMARY KEY (`group_id`,`user_id`),
                INDEX `user_idx` (`user_id`),
                INDEX `status_idx` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $db->exec("CREATE TABLE IF NOT EXISTS `community_group_pinboard_items` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `group_id` INT(11) NOT NULL,
                `owner_id` CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
                `item_type` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
                `payload` MEDIUMTEXT COLLATE latin1_bin NOT NULL,
                `file_ref_id` CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
                `position` INT(11) NOT NULL,
                `mkdate` INT(11) UNSIGNED NOT NULL,
                `chdate` INT(11) UNSIGNED NOT NULL,
                PRIMARY KEY(`id`),
                INDEX `group_idx` (`group_id`),
                INDEX `owner_idx` (`owner_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    public function down()
    {
        $db = DBManager::get();
        $db->exec("DROP TABLE IF EXISTS `community_groups`");
        $db->exec("DROP TABLE IF EXISTS `community_group_participants`");
        $db->exec("DROP TABLE IF EXISTS `community_group_pinboard_items`");
    }
}
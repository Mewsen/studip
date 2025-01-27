<?php
class AddPeerReviewTables extends Migration
{
    public function description()
    {
        return "Adds the Courseware peer review tables.";
    }

    public function up()
    {
        $db = \DBManager::get();

        $db->exec(
            "CREATE TABLE `cw_peer_review_processes`(
                 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                 `task_group_id` INT(11) NOT NULL,
                 `owner_id` CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
                 `configuration` MEDIUMTEXT NOT NULL,
                 `review_start` INT(11) UNSIGNED NOT NULL,
                 `review_end` INT(11) UNSIGNED NOT NULL,
                 `paired_at` INT(11) UNSIGNED NULL,
                 `mkdate` INT(11) UNSIGNED NOT NULL,
                 `chdate` INT(11) UNSIGNED NOT NULL,
                 PRIMARY KEY(`id`),
                 INDEX index_task_group_id(`task_group_id`),
                 INDEX index_owner_id(`owner_id`)
             )"
        );

        $db->exec(
            "CREATE TABLE `cw_peer_reviews`(
                 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                 `process_id` INT(11) UNSIGNED NOT NULL,
                 `task_id` INT(11) UNSIGNED NOT NULL,
                 `submitter_id` CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
                 `reviewer_id` CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
                 `reviewer_type` ENUM('autor', 'group') COLLATE latin1_bin,
                 `assessment` TEXT,
                 `mkdate` INT(11) UNSIGNED NOT NULL,
                 `chdate` INT(11) UNSIGNED NOT NULL,
                 PRIMARY KEY(`id`),
                 INDEX index_process_id(`process_id`),
                 INDEX index_task_id(`task_id`),
                 INDEX index_submitter_id(`submitter_id`),
                 INDEX index_reviewer_id(`reviewer_id`)
             )"
        );
    }

    public function down()
    {
        $db = \DBManager::get();
        $db->exec('DROP TABLE IF EXISTS `cw_peer_reviews`');
        $db->exec('DROP TABLE IF EXISTS `cw_peer_review_processes`');
    }
}

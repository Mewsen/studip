<?php

final class AddForumPostingLogsTable extends Migration
{
    public function up()
    {
        \DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `forum_posting_logs` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `posting_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                `user_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                `action` ENUM('create', 'edit', 'delete') NOT NULL DEFAULT 'create',
                `mkdate` INT(11) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            )
        ");
    }

    public function down()
    {
        \DBManager::get()->exec("
            DROP TABLE IF EXISTS `forum_posting_logs`
        ");
    }
}

<?php

final class AddForumPostingLogsTable extends Migration
{
    public function up()
    {
        \DBManager::get()->exec("
            ALTER TABLE `forum_postings`
            ADD COLUMN `editor_id` CHAR(32) COLLATE latin1_bin NOT NULL AFTER `user_id`
        ");

        \DBManager::get()->exec("
            UPDATE forum_postings
                SET content = REGEXP_REPLACE(
                    content,
                    '<admin_msg autor=\"[^\"]*\" chdate=\"[^\"]*\">',
                    ''
                )
        ");
    }

    public function down()
    {
        \DBManager::get()->exec("
            ALTER TABLE `forum_postings` DROP COLUMN `editor_id`
        ");
    }
}

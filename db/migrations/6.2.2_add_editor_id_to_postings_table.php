<?php

final class AddEditorIdToPostingsTable extends Migration
{
    public function up()
    {
        DBManager::get()->exec("
            ALTER TABLE `forum_postings`
            ADD COLUMN `editor_id` CHAR(32) COLLATE latin1_bin NOT NULL AFTER `user_id`
        ");
    }

    public function down()
    {
        DBManager::get()->exec("
            ALTER TABLE `forum_postings` DROP COLUMN `editor_id`
        ");
    }
}

<?php

final class AddUserIdToForumDiscussions extends Migration
{
    public function up()
    {
        DBManager::get()->exec("ALTER TABLE forum_discussions Add COLUMN user_id CHAR(32) COLLATE latin1_bin NOT NULL AFTER topic_id");
        DBManager::get()->exec("
            UPDATE forum_discussions AS discussions
            SET user_id = (
                SELECT postings.user_id
                FROM forum_postings AS postings
                WHERE postings.discussion_id = discussions.discussion_id
                ORDER BY mkdate ASC
                LIMIT 1
            );
        ");
    }

    public function down()
    {
        DBManager::get()->exec("ALTER TABLE forum_discussions DROP COLUMN user_id");
    }
}

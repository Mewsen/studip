<?php

final class AddUniqueConstraintToForumPostingReactions extends Migration
{
    protected function up()
    {
        // remove duplicate reactions
        DBManager::get()->exec("
            DELETE t1
            FROM forum_posting_reactions AS t1
            JOIN forum_posting_reactions AS t2
              ON t1.posting_id = t2.posting_id
                AND t1.user_id   = t2.user_id
                AND t1.emoji     = t2.emoji
                AND t1.id        > t2.id;
        ");

        DBManager::get()->exec(
            "ALTER TABLE forum_posting_reactions ADD CONSTRAINT unique_posting_user_emoji UNIQUE (posting_id, user_id, emoji)"
        );
    }

    protected function down()
    {
        DBManager::get()->exec("ALTER TABLE forum_posting_reactions DROP INDEX unique_posting_user_emoji");
    }
}

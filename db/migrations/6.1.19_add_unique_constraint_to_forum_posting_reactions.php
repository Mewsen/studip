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

        // Migration was once defined as 6.2.2 thus we need to check if the
        // constraint already exists and cleanup table schema_version.

        if (!$this->hasConstraint()) {
            DBManager::get()->exec(
                "ALTER TABLE forum_posting_reactions ADD CONSTRAINT unique_posting_user_emoji UNIQUE (posting_id, user_id, emoji)"
            );
        }

        if (!file_exists(__DIR__ . '/6.2.1_add_booking_text_to_resource_requests.php')) {
            $query = "DELETE FROM schema_version
                      WHERE `domain` = 'studip'
                        AND `branch` = '6.2'
                        AND `version` = '2';";
            DBManager::get()->exec($query);
        }
    }

    protected function down()
    {
        DBManager::get()->exec("ALTER TABLE forum_posting_reactions DROP INDEX unique_posting_user_emoji");
    }

    private function hasConstraint(): bool
    {
        $query = "SHOW INDEX
                  FROM forum_posting_reactions
                  WHERE Key_name = 'unique_posting_user_emoji'";
        return (bool) DBManager::get()->fetchColumn($query);
    }
}

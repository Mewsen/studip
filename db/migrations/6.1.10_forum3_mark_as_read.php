<?php
final class Forum3MarkAsRead extends Migration
{
    public function description()
    {
        return 'Marks discussions as read when they are older than the last visit in the forum';
    }

    public function up()
    {
        $statement = DBManager::get()->prepare("
            SELECT `pluginid`
            FROM `plugins`
            WHERE `pluginclassname` = 'CoreForum'
        ");
        $statement->execute();
        $pluginid = $statement->fetch(PDO::FETCH_COLUMN);

        if ($pluginid) {
            $statement = DBManager::get()->prepare("
                INSERT IGNORE INTO `forum_posting_reads` (`discussion_id`, `user_id`, `read_index`, `chdate`)
                SELECT `forum_discussions`.`discussion_id`,
                       `object_user_visits`.`user_id`,
                       `object_user_visits`.`visitdate`,
                       UNIX_TIMESTAMP()
                FROM `object_user_visits`
                JOIN `forum_topics` ON (`forum_topics`.`range_id` = `object_user_visits`.`object_id`)
                JOIN `forum_discussions` ON (`forum_discussions`.`topic_id` = `forum_topics`.`topic_id`)
                WHERE `object_user_visits`.`plugin_id` = :pluginid
            ");
            $statement->execute(['pluginid' => $pluginid]);
        }
    }

    public function down()
    {
        DBManager::get()->exec("
            DELETE FROM `forum_posting_reads`
        ");
    }
}

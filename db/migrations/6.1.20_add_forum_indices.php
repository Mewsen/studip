<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/6453
 */
final class AddForumIndices extends Migration
{
    use DatabaseMigrationTrait;

    public function description()
    {
        return 'Adds missing indices to forum tables';
    }

    protected function up()
    {
        // Index für Range Id und dazugehörige Topic Ids
        $query = "ALTER TABLE `forum_topics`
                  ADD INDEX `range_id` (`range_id`)";
        DBManager::get()->exec($query);

        // Index für relevante Posts
        $query = "ALTER TABLE `forum_postings`
                  DROP INDEX `discussion_id`,
                  ADD INDEX `discussion_id_and_mkdate` (`discussion_id`, `mkdate`)";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        $query = "ALTER TABLE `forum_postings`
                  DROP INDEX `discussion_id_and_mkdate`,
                  ADD INDEX `discussion_id` (`discussion_id`)";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `forum_topics`
                  DROP INDEX `range_id`";
        DBManager::get()->exec($query);
    }
}

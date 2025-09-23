<?php
final class CleanupWikiPageParentId extends Migration
{
    public function description()
    {
        return 'Remove invalid parent_id from wiki_pages';
    }

    protected function up()
    {
        $query = "UPDATE `wiki_pages` AS w0
                  LEFT JOIN `wiki_pages` AS w1
                    ON (w0.`parent_id` = w1.`page_id`)
                  SET w0.`parent_id` = NULL
                  WHERE w1.`page_id` IS NULL";
        DBManager::get()->execute($query);
    }
}

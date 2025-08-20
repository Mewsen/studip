<?php
final class RemoveTableWikiLinks extends Migration
{
    public function description()
    {
        return 'Removes the table "wiki_links"';
    }

    protected function up()
    {
        $query = "DROP TABLE `wiki_links`";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        $query = "CREATE TABLE `wiki_links` (
                    `range_id` CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
                    `from_page_id` INT(10) UNSIGNED NOT NULL,
                    `to_page_id` INT(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (`range_id`,`to_page_id`,`from_page_id`)
                  )";
        DBManager::get()->exec($query);
    }
}

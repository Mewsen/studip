<?php

class AddIndexWikipagesParentid extends Migration
{

    public function description()
    {
        return 'Adds index for parent_id to wiki_pages. @see: #5770';
    }

    public function up()
    {
        DBManager::get()->exec("ALTER TABLE `wiki_pages` ADD INDEX parent_id (`parent_id`)");
    }

    public function down()
    {
        DBManager::get()->exec("ALTER TABLE `wiki_pages` DROP INDEX `parent_id`");
    }
}

<?php

class AddIndexBlubberCommentsMkdate extends Migration
{

    public function description()
    {
        return 'Adds index for mkdate to blubber_comments';
    }

    public function up()
    {
        DBManager::get()->exec("ALTER TABLE `blubber_comments` ADD INDEX mkdate (`mkdate`)");
    }

    public function down()
    {
        DBManager::get()->exec("ALTER TABLE `blubber_comments` DROP INDEX `mkdate`");
    }
}

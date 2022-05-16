<?php

class ExtendedAutoinsert extends Migration
{
    public function description()
    {
        return 'Provide new options for autoinserting users into courses.';
    }

    public function up()
    {
        DBManager::get()->exec("ALTER TABLE `auto_insert_sem`
            CHANGE `domain_id` `range_id` VARCHAR(45) NOT NULL DEFAULT '',
            ADD `range_type` ENUM('degree','domain','institute','semester','subject') NOT NULL DEFAULT 'domain'
                AFTER `range_id`");
    }

    public function down()
    {
        DBManager::get()->exec("ALTER TABLE `auto_insert_sem`
            CHANGE `range_id` `domain_id` VARCHAR(45) NOT NULL DEFAULT '',
            DROP `range_type`");
    }
}

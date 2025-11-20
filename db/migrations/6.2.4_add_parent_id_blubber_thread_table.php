<?php

final class AddParentIdBlubberThreadTable extends Migration
{
    public function description()
    {
        return 'Adds the parent_id field to blubber thread table.';
    }

    protected function up()
    {
        $db = DBManager::get();
        $db->exec(
            "ALTER TABLE `blubber_threads`
            ADD COLUMN `parent_id` CHAR(32) COLLATE latin1_bin DEFAULT NULL AFTER `thread_id`"
        );
    }

    protected function down()
    {
        $db = DBManager::get();
        $db->exec(
            "ALTER TABLE `blubber_threads`
            DROP COLUMN `parent_id`"
        );
    }
}

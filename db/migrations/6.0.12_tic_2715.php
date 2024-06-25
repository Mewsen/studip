<?php
class Tic2715 extends Migration
{
    public function description ()
    {
        return 'Add a column to mark resources as requestable via the booking plan, default is 1';
    }

    public function up()
    {
        $query = 'ALTER TABLE `resources`
                  ADD `booking_plan_request` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER `lockable`';
        DBManager::get()->exec($query);
    }

    public function down()
    {
        $query = 'ALTER TABLE `resources` DROP `booking_plan_request`';
        DBManager::get()->exec($query);
    }
}

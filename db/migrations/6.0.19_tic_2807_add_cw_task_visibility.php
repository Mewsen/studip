<?php


class Tic2807AddCWTaskVisibility extends Migration
{
    public function description()
    {
        return 'Adds a visibility column to courseware tasks.';
    }

    protected function up()
    {
        DBManager::get()->exec(
            "ALTER TABLE `cw_tasks`
            ADD COLUMN `visible` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `renewal_date`"
        );
    }

    protected function down()
    {
        DBManager::get()->exec(
            "ALTER TABLE `cw_tasks`
            DROP COLUMN `visible`"
        );
    }
}

<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/4157
 */
return new class extends Migration
{
    public function description()
    {
        return 'Adds missing indices for columns "seminar_id" and "metadate_id" for table "schedule_seminare"';
    }

    protected function up()
    {
        $query = "ALTER TABLE `schedule_seminare`
                  ADD INDEX `seminar_id` (`seminar_id`),
                  ADD INDEX `metadate_id` (`metadate_id`)";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        $query = "ALTER TABLE `schedule_seminare`
                  DROP INDEX `seminar_id`,
                  DROP INDEX `metadate_id`";
        DBManager::get()->exec($query);
    }
};

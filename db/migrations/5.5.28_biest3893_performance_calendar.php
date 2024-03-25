<?php

final class Biest3893PerformanceCalendar extends Migration
{
    public function description()
    {
        return 'add index to calendar tables';
    }

    public function up()
    {
        $db = DBManager::get();
        $db->exec("DELETE FROM `calendar_date_exceptions` WHERE `date` = '1970-01-01'");
        $db->exec("ALTER TABLE `calendar_date_exceptions` ADD INDEX(`calendar_date_id`)");
        $db->exec("ALTER TABLE `calendar_dates` ADD INDEX `repetition_type` (`repetition_type`, `repetition_end`)");
        $db->exec("ALTER TABLE `calendar_dates` ADD INDEX `begin` (`begin`)");
        try {
            $db->exec("ALTER TABLE `calendar_dates` DROP INDEX `uid`");
        } catch (PDOException $exception) {}
    }

    public function down()
    {
    }
}

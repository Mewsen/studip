<?php


class MultipleRoomsPerCourseDate extends Migration
{
    public function description()
    {
        return 'Changes the database schema to allow booking multiple rooms for a course date.';
    }

    protected function up()
    {
        $db = DBManager::get();

        $db->exec(
            "INSERT INTO `config`
            (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
            VALUES
            (
                'ROOM_PERMISSIONS_FOR_MULTIPLE_BOOKINGS_PER_COURSE_DATE',
                 'admin',
                 'string',
                 'global',
                 'resources',
                 UNIX_TIMESTAMP(),
                 UNIX_TIMESTAMP(),
                 'Ab welcher Raumverwaltungs-Rechtestufe soll es möglich sein, mehrere Räume für einen Veranstaltungstermin zu buchen?'
            )"
        );

        $db->exec(
            "CREATE TABLE IF NOT EXISTS ex_termin_rooms
            (
                ex_termin_id CHAR(32) NOT NULL,
                room_id CHAR(32) NOT NULL,
                mkdate INT(10) UNSIGNED NOT NULL DEFAULT '0',
                chdate INT(10) UNSIGNED NOT NULL DEFAULT '0',
                PRIMARY KEY (ex_termin_id, room_id)
            )"
        );

        $db->exec(
            "INSERT INTO `ex_termin_rooms` (`ex_termin_id`, `room_id`, `mkdate`, `chdate`)
            SELECT `termin_id` AS ex_termin_id, `resource_id` AS room_id, `chdate` AS mkdate, `chdate` AS chdate
            FROM `ex_termine`
            WHERE `resource_id` <> ''"
        );

        $db->exec("ALTER TABLE `ex_termine` DROP COLUMN `resource_id`");

        $db->exec("ALTER TABLE `separable_rooms` ADD COLUMN `description` TEXT NOT NULL");
    }

    protected function down()
    {
        $db = DBManager::get();

        $db->exec("ALTER TABLE `separable_rooms` DROP COLUMN `description`");
        $db->exec("ALTER TABLE `ex_termine` ADD COLUMN `resource_id` CHAR(32) NOT NULL");
        $db->exec("DROP TABLE IF EXISTS ex_termin_rooms");
        $db->exec(
            "DELETE FROM `config`
            WHERE `field` = 'ROOM_PERMISSIONS_FOR_MULTIPLE_BOOKINGS_PER_COURSE_DATE'"
        );
    }
}

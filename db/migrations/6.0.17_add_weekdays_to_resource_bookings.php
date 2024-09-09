<?php


class AddWeekdaysToResourceBookings extends Migration
{
    public function description()
    {
        return 'Adds the weekdays column to the resource_bookings table.';
    }

    protected function up()
    {
        DBManager::get()->exec(
            "ALTER TABLE `resource_bookings`
            ADD COLUMN weekdays VARCHAR(7) COLLATE `latin1_bin` NOT NULL DEFAULT ''"
        );
    }

    protected function down()
    {
        DBManager::get()->exec(
            "ALTER TABLE `resource_bookings`
            DROP COLUMN weekdays"
        );
    }
}

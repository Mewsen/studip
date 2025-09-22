<?php


class AddBookingTextToResourceRequests extends Migration
{
    public function description()
    {
        return 'Adds the booking_text field to resource requests.';
    }

    protected function up()
    {
        $db = DBManager::get();
        $db->exec(
            "ALTER TABLE `resource_requests`
            ADD COLUMN booking_text TEXT"
        );
    }

    protected function down()
    {
        $db = DBManager::get();
        $db->exec(
            "ALTER TABLE `resource_requests`
            DROP COLUMN booking_text"
        );
    }
}

<?php
final class CleanupConsultationBookings extends Migration
{
    public function description()
    {
        return 'Removes orphaned entries from consultation_bookings';
    }

    protected function up()
    {
        $query = "DELETE `consultation_bookings`
                  FROM `consultation_bookings`
                  LEFT JOIN `auth_user_md5` USING (`user_id`)
                  WHERE `auth_user_md5`.`user_id` IS NULL";
        DBManager::get()->exec($query);
    }
}

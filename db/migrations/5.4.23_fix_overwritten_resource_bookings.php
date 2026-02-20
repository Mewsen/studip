<?php


class FixOverwrittenResourceBookings extends Migration
{
    public function description()
    {
        return 'BIESt 6284: Migration to fix existing bookings with repetitions that have been overwritten by a lock booking.';
    }

    protected function up()
    {
        $db = DBManager::get();
        $check_exists_stmt = $db->prepare(
            "SELECT 1
                 FROM `resource_booking_intervals`
                 WHERE `booking_id` = :booking_id
                 AND `begin` = :begin
                 AND `end` = :end"
        );
        $create_exception_stmt = $db->prepare(
            "INSERT INTO `resource_booking_intervals`
                (`interval_id`, `resource_id`, `booking_id`, `begin`, `end`, `takes_place`, `mkdate`, `chdate`)
                VALUES
                (:interval_id, :resource_id, :booking_id, :begin, :end, '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())"
        );

        $fix_callable = function (ResourceBooking $booking) use ($check_exists_stmt, $create_exception_stmt) {
            //Check if the booking has deleted intervals and recreate them with takes_place set to zero.
            $all_time_intervals = $booking->calculateTimeIntervals();

            foreach ($all_time_intervals as $interval) {
                $check_exists_stmt->execute([
                    'booking_id' => $booking->id,
                    'begin'      => $interval['begin'],
                    'end'        => $interval['end']
                ]);
                $interval_exists = (bool) $check_exists_stmt->fetchColumn();
                if (!$interval_exists) {
                    //Create the interval:
                    $create_exception_stmt->execute([
                        'interval_id' => md5(uniqid('5.4.23_fix_overwritten_resource_bookings')),
                        'resource_id' => $booking->resource_id,
                        'booking_id'  => $booking->id,
                        'begin'       => $interval['begin'],
                        'end'         => $interval['end']
                    ]);
                }
            }
        };

        ResourceBooking::findEachBySQL(
            $fix_callable,
            "`repeat_end` IS NOT NULL"
        );
    }

    protected function down()
    {
        //You don't want to make data inconsistent again, don't you?
    }
}

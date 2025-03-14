<?php

/**
 * CalendarCourseExDate is a specialisation of CourseExDate for
 * cancelled course dates that are displayed in the personal calendar.
 *
 * @property string $id alias column for termin_id
 * @property string $termin_id database column
 * @property string $range_id database column
 * @property string $autor_id database column
 * @property string $content database column
 * @property int $date database column
 * @property int $end_time database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property int $date_typ database column
 * @property string|null $raum database column
 * @property string|null $metadate_id database column
 * @property string $resource_id database column
 * @property User $author belongs_to User
 * @property Course $course belongs_to Course
 * @property SeminarCycleDate|null $cycle belongs_to SeminarCycleDate
 * @property-read mixed $topics additional field
 * @property-read mixed $statusgruppen additional field
 * @property-read mixed $dozenten additional field
 * @property-read mixed $room_booking additional field
 * @property-read mixed $room_request additional field
 */
class CalendarCourseExDate extends CourseExDate
{
    public static function getEvents(DateTime $begin, DateTime $end, string $range_id): array
    {
        return parent::findBySQL(
            "JOIN `seminar_user`
               ON `seminar_user`.`seminar_id` = `ex_termine`.`range_id`
             WHERE `seminar_user`.`user_id` = :user_id
               AND `seminar_user`.`bind_calendar` = '1'
               AND `ex_termine`.`date` BETWEEN :begin AND :end
               AND `ex_termine`.`content` <> ''
               AND (
                   IFNULL(`ex_termine`.`metadate_id`, '') = ''
                   OR `ex_termine`.`metadate_id` NOT IN (
                       SELECT `metadate_id`
                       FROM `schedule_courses`
                       WHERE `user_id` = :user_id
                         AND `visible` = 0
                 )
             )
             ORDER BY date",
            [
                'begin'   => $begin->getTimestamp(),
                'end'     => $end->getTimestamp(),
                'user_id' => $range_id
            ]
        );
    }
}

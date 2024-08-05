<?php

/**
 * CalendarCourseDate is a specialisation of CourseDate for
 * course dates that are displayed in the personal calendar.
 */
class CalendarCourseDate extends CourseDate
{
    public static function getEvents(DateTime $begin, DateTime $end, string $range_id): array
    {
        $events = [];
        parent::findEachBySQL(
            function ($e) use (&$events, $range_id) {
                if (self::checkRelated($e, $range_id)) {
                    $events[] = $e;
                }
            },
            "JOIN `seminar_user`
               ON `seminar_user`.`seminar_id` = `termine`.`range_id`
             WHERE `seminar_user`.`user_id` = :user_id
               AND `seminar_user`.`bind_calendar` = '1'
               AND `termine`.`date` BETWEEN :begin AND :end
               AND (
                   IFNULL(`termine`.`metadate_id`, '') = ''
                   OR `termine`.`metadate_id` NOT IN (
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
        return $events;
    }

    /**
     * Checks if given user is the responsible lecturer or is member of a
     * related group.
     *
     * @global object $perm The global perm object.
     * @param CalendarCourseDate $event The course event to check against.
     * @param string $user_id The id of the user.
     * @return boolean
     */
    protected static function checkRelated(CalendarCourseDate $event, string $user_id): bool
    {
        $check_related = false;
        $permission = $GLOBALS['perm']->get_studip_perm($event->range_id, $user_id);
        switch ($permission) {
            case 'dozent' :
                $related_persons = $event->dozenten->pluck('user_id');
                if (count($related_persons) > 0) {
                    $check_related = in_array($user_id, $related_persons);
                } else {
                    $check_related = true;
                }
                break;
            case 'tutor' :
                $check_related = true;
                break;
            default :
                $group_ids = $event->statusgruppen->pluck('statusgruppe_id');
                if (count($group_ids) > 0) {
                    $member = StatusgruppeUser::findBySQL(
                        'statusgruppe_id IN(?) AND user_id = ?',
                        [$group_ids, $user_id]);
                    $check_related = count($member) > 0;
                } else {
                    $check_related = true;
                }
        }
        return $check_related;
    }
}

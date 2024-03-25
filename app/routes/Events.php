<?php
namespace RESTAPI\Routes;

use Config;
use Resource;
use Room;
use Seminar;
use Issue;


/**
 * @author     André Klaßen <andre.klassen@elan-ev.de>
 * @author     <mlunzena@uos.de>
 * @license    GPL 2 or later
 * @deprecated Since Stud.IP 5.0. Will be removed in Stud.IP 6.0.
 *
 * @condition course_id ^[a-f0-9]{1,32}$
 * @condition user_id ^[a-f0-9]{1,32}$
 * @condition semester_id ^[a-f0-9]{1,32}$
 */
class Events extends \RESTAPI\RouteMap
{

    /**
     * returns all upcoming events within the next two weeks for a given user
     *
     * @get /user/:user_id/events
     */
    public function getEvents($user_id)
    {
        if ($user_id !== $GLOBALS['user']->id) {
            $this->error(401);
        }

        $start = new \DateTime();
        $end   = clone $start;
        $end   = $end->add(new \DateInterval('P2W'));

        $list = array_merge(
            \CalendarCourseDate::getEvents($start, $end, $user_id),
            \CalendarCourseExDate::getEvents($start, $end, $user_id)
        );

        $json = [];
        $events = array_slice($list, $this->offset, $this->limit); ;
        foreach ($events as $event) {

            $course_uri = $this->urlf('/course/%s', [htmlReady($event->range_id)]);

            $json[] = [
                'event_id'    => $event->id,
                'course'      => $course_uri,
                'start'       => $event->date,
                'end'         => $event->end_time,
                'title'       => $event->getTitle(),
                'description' => $event->getDescription() ?: '',
                'categories'  => $event->getTypeName(),
                'room'        => $event->getRoomName(),
                'canceled'    => $event instanceof \CourseExDate || holiday($event->date),
            ];
        }

        $this->etag(md5(serialize($json)));

        return $this->paginated($json, count($list), compact('user_id'));
    }

    /**
     *  returns an iCAL Export of all events for a given user
     *
     * @get /user/:user_id/events.ics
     */
    public function getEventsICAL($user_id)
    {
        if ($user_id !== $GLOBALS['user']->id) {
            $this->error(401);
        }
        $end = new \DateTime();
        $end->setTimestamp(\CalendarDate::NEVER_ENDING);
        $start = new \DateTime();
        $start->modify('-4 week');
        $ical_export = new \ICalendarExport();
        $ical = $ical_export->exportCalendarDates($user_id, $start, $end)
            . $ical_export->exportCourseDates($user_id, $start, $end)
            . $ical_export->exportCourseExDates($user_id, $start, $end);
        $content = $ical_export->writeHeader() . $ical . $ical_export->writeFooter();

        $this->contentType('text/calendar');
        $this->headers([
            'Content-Length'      => strlen($content),
            'Content-Disposition' => 'attachment; ' . encode_header_parameter('filename', 'studip.ics'),
        ]);
        $this->halt(200, $this->response->headers, function () use ($content) {
            echo $content;
        });
    }


    /**
     * returns events for a given course
     *
     * @get /course/:course_id/events
     */
    public function getEventsForCourse($course_id)
    {
        if (!$GLOBALS['perm']->have_studip_perm('user', $course_id, $GLOBALS['user']->id)) {
            $this->error(401);
        }

        $seminar = new Seminar($course_id);
        $dates = getAllSortedSingleDates($seminar);
        $total = sizeof($dates);

        $events = [];
        foreach (array_slice($dates, $this->offset, $this->limit) as $date) {

            // get issue titles
            $issue_titles = [];
            if (is_array($issues = $date->getIssueIDs())) {
                foreach ($issues as $is) {
                    $issue = new Issue(['issue_id' => $is]);
                    $issue_titles[] = $issue->getTitle();
                }
            }

            $room = self::getRoomForSingleDate($date);
            $events[] = [
                'event_id'    => $date->getSingleDateID(),
                'start'       => $date->getStartTime(),
                'end'         => $date->getEndTime(),
                'title'       => $date->toString(),
                'description' => implode(', ', $issue_titles),
                'categories'  => $date->getTypeName() ?: '',
                'room'        => $room ?: '',
                'deleted'     => $date->isExTermin(),
                'canceled'    => $date->isHoliday() ?: false,
            ];
        }

        $this->etag(md5(serialize($events)));

        return $this->paginated($events, $total, compact('course_id'));
    }

    private static function getRoomForSingleDate($val) {

        /* css-Klasse auswählen, sowie Template-Feld für den Raum mit Text füllen */
        if (Config::get()->RESOURCES_ENABLE) {

            if ($val->getResourceID()) {
                $resObj = Resource::find($val->getResourceID());
                if ($resObj) {
                    $room_object = $resObj->getDerivedClassInstance();
                    if ($room_object instanceof Room) {
                        $room = _("Raum: ");
                        $room .= $room_object->getActionURL('booking_plan');
                    }
                }
            } else {
                $room = _("keine Raumangabe");

                if ($val->isExTermin()) {
                    if ($name = $val->isHoliday()) {
                        $room = '('.$name.')';
                    } else {
                        $room = '('._('fällt aus').')';
                    }
                }

                else {
                    if ($val->getFreeRoomText()) {
                        $room = '('.htmlReady($val->getFreeRoomText()).')';
                    }
                }
            }
        } else {
            $room = '';
            if ($val->getFreeRoomText()) {
                $room = '('.htmlReady($val->getFreeRoomText()).')';
            }
        }

        return html_entity_decode(strip_tags($room));
    }

}

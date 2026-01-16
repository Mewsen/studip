<?php

/**
 * ajax.php - contains Resources_AjaxController
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      David Siegfried <ds.siegfried@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */


require_once(__DIR__ . '/BookingPlanDataHelper.php');

class Resources_AjaxController extends AuthenticatedController
{
    use BookingPlanDataHelper;

    public function toggle_marked_action($request_id)
    {
        $request = ResourceRequest::find($request_id);

        if (!$request) {
            throw new Exception('Resource request object not found!');
        }

        $current_user = User::findCurrent();

        if ($request->isReadOnlyForUser($current_user)) {
            throw new AccessDeniedException();
        }

        //Switch to the next marking state or return to the unmarked state
        //if the next marking state would be after the last defined
        //marking state.
        $request->marked = ($request->marked + 1) % ResourceRequest::MARKING_STATES;
        $request->store();

        $this->render_json($request->toArray());
    }

    public function get_resource_booking_intervals_action($booking_id)
    {
        $booking = ResourceBooking::find($booking_id);
        if (!$booking) {
            throw new Exception('Resource booking object not found!');
        }

        $resource = $booking->resource->getDerivedClassInstance();
        if (!$resource->bookingPlanVisibleForUser(User::findCurrent())) {
            throw new AccessDeniedException();
        }

        //Get begin and end:
        $begin_str = Request::get('begin');
        $end_str   = Request::get('end');
        $begin     = null;
        $end       = null;
        if ($begin_str && $end_str) {
            //Try the ISO format first: YYYY-MM-DDTHH:MM:SS±ZZ:ZZ
            $begin = DateTime::createFromFormat(DateTime::RFC3339, $begin_str);
            $end   = DateTime::createFromFormat(DateTime::RFC3339, $end_str);
            if (!($begin instanceof DateTime) || !($end instanceof DateTime)) {
                $tz = new DateTime();
                $tz = $tz->getTimezone();
                //Try the ISO format without timezone:
                $begin = DateTime::createFromFormat('Y-m-d\TH:i:s', $begin_str, $tz);
                $end   = DateTime::createFromFormat('Y-m-d\TH:i:s', $end_str, $tz);
            }
        }

        $sql      = "booking_id = :booking_id ";
        $sql_data = ['booking_id' => $booking->id];
        if ($begin instanceof DateTime && $end instanceof DateTime) {
            $sql               .= "AND begin >= :begin AND end <= :end ";
            $sql_data['begin'] = $begin->getTimestamp();
            $sql_data['end']   = $end->getTimestamp();
        }
        if (Request::submitted('exclude_cancelled_intervals')) {
            $sql .= "AND takes_place = '1' ";
        }
        $sql       .= "ORDER BY begin ASC, end ASC";
        $intervals = ResourceBookingInterval::findBySql($sql, $sql_data);

        $result = [];
        foreach ($intervals as $interval) {
            $result[] = $interval->toRawArray();
        }

        $this->render_json($result);
    }

    public function toggle_takes_place_field_action($interval_id)
    {
        $interval = ResourceBookingInterval::find($interval_id);
        if (!$interval) {
            throw new Exception('ResourceBookingInterval object not found!');
        }

        //Get the resource and check the permissions of the user:
        $resource = $interval->resource;
        if (!$resource) {
            throw new Exception('ResourceBookingInterval not linked with a resource!');
        }

        $resource = $resource->getDerivedClassInstance();

        if (!$resource->userHasPermission(User::findCurrent(), 'autor', [$interval->begin, $interval->end])) {
            throw new Exception('You do not have sufficient permissions to modify the interval!');
        }

        if (
            !$interval->takes_place
            && $resource->isAssigned(new DateTime('@' . $interval->begin), new DateTime('@' . $interval->end))
        ) {
            throw new Exception('Already booked');
        }
        //Switch the takes_place field:
        $interval->takes_place = $interval->takes_place ? '0' : '1';

        if ($interval->store()) {
            $this->render_json([
                'takes_place' => $interval->takes_place
            ]);
        } else {
            $this->set_status(500);
            $this->render_text('Error while storing the interval!');
        }
    }

    public function get_semester_booking_plan_action($resource_id)
    {
        $resource = Resource::find($resource_id);
        if (!$resource) {
            throw new Exception('Resource object not found!');
        }

        $resource = $resource->getDerivedClassInstance();

        $current_user = User::findCurrent();

        if (!$resource->bookingPlanVisibleForUser($current_user)) {
            throw new AccessDeniedException();
        }

        $display_requests     = Request::get('display_requests');
        $display_all_requests = Request::get('display_all_requests');

        $begin = new DateTime();
        $end   = new DateTime();

        $semester_id = Request::get('semester_id');

        $semester = $semester_id ? Semester::find($semester_id) : Semester::findCurrent();
        if (!$semester) {
            throw new Exception('No semester found!');
        }

        if (Request::get('semester_timerange') !== 'fullsem') {
            $begin->setTimestamp($semester->vorles_beginn);
            $end->setTimestamp($semester->vorles_ende);
        } else {
            $begin->setTimestamp($semester->beginn);
            $end->setTimestamp($semester->ende);
        }

        //Get parameters:
        $booking_types = Request::getArray('booking_types');

        $begin_timestamp = $begin->getTimestamp();
        $end_timestamp   = $end->getTimestamp();

        //Get the event data sources:
        $bookings = ResourceBooking::findByResourceAndTimeRanges(
            $resource,
            [
                [
                    'begin' => $begin_timestamp,
                    'end'   => $end_timestamp
                ]
            ],
            $booking_types
        );

        $requests = [];
        if ($display_all_requests || $display_requests) {
            $requests_sql = "JOIN seminar_cycle_dates AS scd USING (metadate_id)
                             WHERE resource_id = :resource_id
                               AND closed = 0";
            $requests_sql_params = [
                'begin'       => $begin_timestamp,
                'end'         => $end_timestamp,
                'resource_id' => $resource->id
            ];
            if (!$display_all_requests) {
                $requests_sql .= " AND user_id = :user_id ";
                $requests_sql_params['user_id'] = $current_user->id;
            }

            $requests = ResourceRequest::findBySql(
                $requests_sql,
                $requests_sql_params
            );
        }

        $merged_objects = [];
        $meta_dates     = [];

        foreach ($bookings as $booking) {
            $booking->resource  = $resource;
            $irrelevant_booking = $booking->getRepetitionType() !== 'weekly'
                                  && (
                                      !Request::get('display_single_bookings')
                                      || $booking->end < strtotime('today')
                                  );
            if ($booking->getAssignedUserType() === 'course' && in_array($booking->assigned_course_date->metadate_id, $meta_dates)) {
                $irrelevant_booking = true;
            };
            if (!$irrelevant_booking) {
                //It is an booking with repetitions that has to be included
                //in the semester plan.
                if (in_array($booking->getRepetitionType(), ['single', 'weekly'])) {
                    $event_list = $booking->convertToEventData(
                        [
                            ResourceBookingInterval::build(
                                [
                                    'interval_id' => md5(uniqid()),
                                    'begin'       => $booking->begin - $booking->preparation_time,
                                    'end'         => $booking->end
                                ]
                            )
                        ],
                        $current_user
                    );
                } else {
                    $event_list = $booking->getFilteredEventData(null, null, null, strtotime('today'), $end_timestamp);
                }
                foreach ($event_list as $event_data) {
                    if ($booking->getAssignedUserType() === 'course' && $booking->assigned_course_date->metadate_id) {
                        $index = sprintf(
                            '%s_%s_%s',
                            $booking->assigned_course_date->metadate_id,
                            $event_data->begin->format('NHis'),
                            $event_data->end->format('NHis')
                        );
                        $meta_dates[] = $booking->assigned_course_date->metadate_id;
                    } else {
                        $index = sprintf(
                            '%s_%s_%s',
                            $booking->id,
                            $event_data->begin->format('NHis'),
                            $event_data->end->format('NHis')
                        );
                    }

                    //Strip some data that cannot be used effectively in here:
                    $event_data->api_urls = [];
                    $event_data->editable = false;

                    $merged_objects[$index] = $event_data;
                }
            }
        }

        $relevant_request = false;
        foreach ($requests as $request) {
            if ($request->cycle instanceof SeminarCycleDate) {
                $cycle_dates = $request->cycle->getAllDates();
                foreach ($cycle_dates as $cycle_date) {
                    $relevant_request = $semester->beginn <= $cycle_date->date
                        && $semester->ende >= $cycle_date->date;
                    if ($relevant_request) {
                        //We have found a date for the current semester
                        //that makes the request relevant.
                        break;
                    }
                }
                if (!$relevant_request) {
                    continue;
                }
                $event_data_list = $request->getFilteredEventData(
                    $current_user->id
                );

                foreach ($event_data_list as $event_data) {
                    $index = sprintf(
                        '%s_%s_%s',
                        $request->metadate_id,
                        $event_data->begin->format('NHis'),
                        $event_data->end->format('NHis')
                    );

                    //Strip some data that cannot be used effectively in here:
                    $event_data->view_urls = [];
                    $event_data->api_urls  = [];

                    $merged_objects[$index] = $event_data;
                }
            }
        }

        //Convert the merged events to Fullcalendar events:
        $data = [];
        foreach ($merged_objects as $obj) {
            $data[] = $obj->toFullCalendarEvent();
        }

        $this->render_json($data);
    }

    public function get_booking_plan_action($resource_id)
    {
        $this->renderBookingPlanData($resource_id);
    }

    public function get_clipboard_booking_plan_action($clipboard_id)
    {
        $clipboard = \Clipboard::find($clipboard_id);

        if (!empty($_SESSION['selected_clipboard_id'])) {
            $clipboard = Clipboard::find($_SESSION['selected_clipboard_id']);
        }
        if (!$clipboard) {
            throw new Exception('Clipboard object not found!');
        }

        $current_user = User::findCurrent();

        //Permission check:
        if ($clipboard->user_id !== $current_user->id) {
            throw new AccessDeniedException();
        }

        $display_requests     = Request::bool('display_requests');
        $display_all_requests = Request::bool('display_all_requests');

        //Try the ISO format first: YYYY-MM-DDTHH:MM:SS±ZZ:ZZ
        $start = Request::getDateTime('start', DateTime::RFC3339);
        $end   = Request::getDateTime('end', DateTime::RFC3339);

        if (!$start || !$end) {
            $start_str = Request::get('start');
            $end_str   = Request::get('end');
            $start = new \DateTime();
            $end   = new \DateTime();
            //Assume the local timezone and use the Y-m-d format:
            $date_regex = '/[0-9]{4}-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1])/';
            if (preg_match($date_regex, $start_str)) {
                //$begin is specified in the date formay YYYY-MM-DD:
                $start_parts = explode('-', $start_str);
                $start->setDate(
                    $start_parts[0],
                    $start_parts[1],
                    $start_parts[2]
                );
                $start->setTime(0,0,0);
            } else {
                $start->setTimestamp($start_str);
            }
            //Now we do the same for $end_timestamp:
            if (preg_match($date_regex, $end_str)) {
                //$begin is specified in the date formay YYYY-MM-DD:
                $end_parts = explode('-', $end_str);
                $end->setDate(
                    $end_parts[0],
                    $end_parts[1],
                    $end_parts[2]
                );
                $end->setTime(23,59,59);
            } else {
                $end->setTimestamp($end_str);
            }
        }

        $rooms = Room::findMany($clipboard->getAllRangeIds('Room'));

        $booking_types = Request::getArray('booking_types');

        //Room permission check:
        $plan_objects = [];
        foreach ($rooms as $room) {
            if ($room->bookingPlanVisibleForuser($current_user)) {
                $plan_objects = array_merge(
                    $plan_objects,
                    \ResourceManager::getBookingPlanObjects(
                        $room,
                        [
                            [
                                'begin' => $start->getTimestamp(),
                                'end'   => $end->getTimestamp()
                            ]
                        ],
                        $booking_types,
                        $display_all_requests ? 'all' : $display_requests
                    )
                );
            }
        }

        $this->render_json(\Studip\Fullcalendar::createData($plan_objects, $start, $end));
    }

    public function get_clipboard_semester_plan_action($clipboard_id = null)
    {
        if (!$clipboard_id) {
            throw new Exception('ID of clipboard has not been provided!');
        }

        $clipboard = Clipboard::find($clipboard_id);

        if (!empty($_SESSION['selected_clipboard_id'])) {
            $clipboard = Clipboard::find($_SESSION['selected_clipboard_id']);
        }
        if (!$clipboard) {
            throw new Exception('Clipboard object not found!');
        }
        $current_user = User::findCurrent();

        //Permission check:
        if ($clipboard->user_id !== $current_user->id) {
            throw new AccessDeniedException();
        }

        $display_requests     = Request::bool('display_requests');
        $display_all_requests = Request::bool('display_all_requests');

        $begin = new DateTime();
        $end   = new DateTime();

        $semester_id = Request::get('semester_id');
        $semester    = $semester_id ? Semester::find($semester_id) : Semester::findCurrent();

        if (!$semester) {
            throw new Exception('No semester found!');
        }

        if (Request::get('semester_timerange') === 'vorles') {
            $begin->setTimestamp($semester->vorles_beginn);
            $end->setTimestamp($semester->vorles_ende);
        } else {
            $begin->setTimestamp($semester->beginn);
            $end->setTimestamp($semester->ende);
        }

        $rooms = Room::findMany($clipboard->getAllRangeIds('Room'));

        //Get parameters:
        $booking_types = Request::getArray('booking_types');

        //Get the event data sources:
        $plan_objects = [];

        foreach ($rooms as $room) {
            if ($room->bookingPlanVisibleForuser($current_user)) {
                $plan_objects = array_merge(
                    $plan_objects,
                    ResourceManager::getBookingPlanObjects(
                        $room,
                        [
                            [
                                'begin' => $begin->getTimestamp(),
                                'end'   => $end->getTimestamp()
                            ]
                        ],
                        $booking_types,
                        $display_all_requests ? 'all' : $display_requests
                    )
                );
            }
        }

        $merged_objects   = [];
        $meta_dates       = [];
        $relevant_request = false;
        foreach ($plan_objects as $plan_object) {
            if ($plan_object instanceof ResourceBooking) {
                $irrelevant_booking = $plan_object->getRepetitionType() !== 'weekly'
                                      || (
                                          $plan_object->getAssignedUserType() === 'course'
                                          && in_array($plan_object->assigned_course_date->metadate_id, $meta_dates)
                                      );
                if ($irrelevant_booking) {
                    continue;
                }

                //It is a booking with repetitions that has to be included
                //in the semester plan.

                $real_begin = $plan_object->begin;
                if ($plan_object->preparation_time > 0) {
                    $real_begin -= $plan_object->preparation_time;
                }
                $event_data = $plan_object->convertToEventData(
                    [
                        ResourceBookingInterval::build(
                            [
                                'interval_id' => md5(uniqid()),
                                'begin'       => $real_begin,
                                'end'         => $plan_object->end
                            ]
                        )
                    ],
                    $current_user
                );

                //Merge event data from the same booking that have the
                //same weekday and begin and end time into one event.
                //If no repetition interval is set and the booking belongs
                //to a course date, use the corresponding metadate ID or the
                //course date ID in the index. Otherwise use the booking's
                //ID (specified by event_data->object_id).
                foreach ($event_data as $event) {
                    if ($plan_object->getAssignedUserType() === 'course') {
                        $index = sprintf(
                            '%s_%s_%s',
                            $plan_object->assigned_course_date->metadate_id,
                            $event->begin->format('NHis'),
                            $event->end->format('NHis')
                        );
                        $meta_dates[] = $plan_object->assigned_course_date->metadate_id;
                    } else {
                        $index = sprintf(
                            '%s_%s_%s',
                            $plan_object->id,
                            $event->begin->format('NHis'),
                            $event->end->format('NHis')
                        );
                    }

                    //Strip some data that cannot be used effectively in here:
                    $event->api_urls = [];

                    $merged_objects[$index] = $event;
                }
            } else if ($plan_object instanceof ResourceRequest) {
                if ($plan_object->cycle instanceof SeminarCycleDate) {
                    $cycle_dates = $plan_object->cycle->getAllDates();
                    foreach ($cycle_dates as $cycle_date) {
                        $relevant_request = $semester->beginn <= $cycle_date->date
                            && $semester->ende >= $cycle_date->date;
                        if ($relevant_request) {
                            //We have found a date for the current semester
                            //that makes the request relevant.
                            break;
                        }
                    }
                    if (!$relevant_request) {
                        continue;
                    }
                    $event_data_list = $plan_object->getFilteredEventData(
                        $current_user->id
                    );

                    foreach ($event_data_list as $event_data) {
                        $index = sprintf(
                            '%s_%s_%s',
                            $plan_object->metadate_id,
                            $event_data->begin->format('NHis'),
                            $event_data->end->format('NHis')
                        );

                        //Strip some data that cannot be used effectively in here:
                        $event_data->view_urls = [];
                        $event_data->api_urls  = [];

                        $merged_objects[$index] = $event_data;
                    }
                }
            }
        }

        //Convert the merged events to Fullcalendar events:
        $data = [];
        foreach ($merged_objects as $obj) {
            $data[] = $obj->toFullCalendarEvent();
        }

        $this->render_json($data);
    }

    public function move_booking_action($booking_id): void
    {
        $booking = ResourceBooking::find($booking_id);
        if (!$booking) {
            $this->notFound('Resource booking object not found!');
            return;
        }

        $current_user = User::findCurrent();

        if ($booking->isReadOnlyForUser($current_user)) {
            throw new AccessDeniedException();
        }

        $resource_id = Request::get('resource_id');
        $interval_id = Request::get('interval_id');

        $begin = $this->convertDatetime(Request::get('begin'));
        $end = $this->convertDatetime(Request::get('end'));

        //Check if a specific interval has been moved:
        if ($interval_id) {
            $interval = ResourceBookingInterval::findOneBySql(
                'interval_id = ? AND booking_id = ?',
                [$interval_id, $booking->id]
            );
            if (!$interval) {
                $this->notFound('Resource booking interval not found!');
                return;
            }
            $interval_begin = new DateTime();
            $interval_begin->setTimestamp($interval->begin);
            $interval_end = new DateTime();
            $interval_end->setTimestamp($interval->end);

            //Calculate the difference from the interval time range
            //to the time range from the request. That difference
            //is then applied to the booking.
            $begin_diff = $interval_begin->diff($begin);
            $end_diff = $interval_end->diff($end);

            $new_booking_begin = new DateTime();
            $new_booking_begin->setTimestamp($booking->begin);
            $new_booking_end = new DateTime();
            $new_booking_end->setTimestamp($booking->end);

            $new_booking_begin = $new_booking_begin->add($begin_diff);
            $new_booking_end = $new_booking_end->add($end_diff);
            //We must substract the preparation time to the begin timestamp
            //to get the real begin:
            $real_begin = clone $new_booking_begin;
            if ($booking->preparation_time > 0) {
                $real_begin->sub(new DateInterval('PT' . ($booking->preparation_time / 60 ) . 'M'));
            }
            $booking->begin = $real_begin->getTimestamp();
            $booking->end = $new_booking_end->getTimestamp();
        } else {
            //We must substract the preparation time to the begin timestamp
            //to get the real begin:
            $real_begin = clone $begin;
            if ($booking->preparation_time > 0) {
                $real_begin->sub(new DateInterval('PT' . ($booking->preparation_time / 60 ) . 'M'));
            }
            $booking->begin = $real_begin->getTimestamp();
            $booking->end = $end->getTimestamp();
        }
        if ($resource_id) {
            //The resource-ID has changed:
            //The booking was moved from one resource to another.
            $booking->resource_id = $resource_id;
        }

        //Update the booking_user_id field:
        $booking->booking_user_id = User::findCurrent()->id;

        try {
            $booking->store();

            if (Request::bool('quiet')) {
                $this->render_nothing();
            } else {
                $this->render_json($booking->toRawArray());
            }
        } catch (Exception $e) {
            $this->set_status(500);
            $this->render_text($e->getMessage());
        }
    }

    public function move_request_action($request_id): void
    {
        $request = ResourceRequest::find($request_id);
        if (!$request) {
            $this->notFound('Resource request object not found!');
            return;
        }

        $current_user = User::findCurrent();

        if ($request->isReadOnlyForUser($current_user)) {
            throw new AccessDeniedException();
        }

        $request->begin = $this->convertDatetime(Request::get('begin'));
        $request->end = $this->convertDatetime(Request::get('end'));

        try {
            $request->store();
            $this->renderObject($request);
        } catch (\Exception $e) {
            $this->set_status(500);
            $this->render_text($e->getMessage());
        }
    }

    private function notFound(string $message = ''): void
    {
        $this->set_status(404);
        $this->render_text($message);
    }

    private function renderObject(SimpleORMap $object): void
    {
        if (Request::bool('quiet')) {
            $this->render_nothing();
        } else{
            $this->render_json($object->toArray());
        }
    }

    /**
     * Tries the ISO format first: YYYY-MM-DDTHH:MM:SS±ZZ:ZZ
     */
    private function convertDatetime(?string $input): ?Datetime
    {
        if (!$input) {
            return null;
        }

        return DateTime::createFromFormat(DateTime::RFC3339, $input)
            ?? DateTime::createFromFormat(
                   'Y-m-d\TH:i:s',
                   $input,
                   (new DateTime())->getTimezone()
               );
    }
}

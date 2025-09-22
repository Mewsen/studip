<?php

trait BookingPlanDataHelper
{
    public function renderBookingPlanData($resource_id)
    {
        $resource = Resource::find($resource_id);
        if (!$resource) {
            throw new Exception('Resource object not found!');
        }

        $resource = $resource->getDerivedClassInstance();

        $current_user  = User::findCurrent();
        $nobody_access = true;

        if ($current_user instanceof User) {
            $nobody_access = false;
            if (!$resource->bookingPlanVisibleForUser($current_user)) {
                throw new AccessDeniedException();
            }
        } else if ($resource instanceof Room) {
            if (!$resource->bookingPlanVisibleForUser($current_user)) {
                throw new AccessDeniedException();
            }
        }

        $display_requests = $current_user && Request::bool('display_requests');
        $display_all_requests = Request::bool('display_all_requests');

        $begin_date = Request::get('start');
        $end_date   = Request::get('end');
        if (!$begin_date || !$end_date) {
            //No time range specified.
            throw new Exception('The parameters "start" and "end" are missing!');
        }

        $begin = DateTime::createFromFormat(DateTime::RFC3339, $begin_date);
        $end   = DateTime::createFromFormat(DateTime::RFC3339, $end_date);

        if (!($begin instanceof DateTime) || !($end instanceof DateTime)) {
            $begin = new DateTime();
            $end   = new DateTime();
            //Assume the local timezone and use the Y-m-d format:
            $date_regex = '/[0-9]{4}-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1])/';
            if (preg_match($date_regex, $begin_date)) {
                //$begin is specified in the date format YYYY-MM-DD:
                $begin_str = explode('-', $begin_date);
                $begin->setDate(
                    intval($begin_str[0]),
                    intval($begin_str[1]),
                    intval($begin_str[2])
                );
                $begin->setTime(0, 0, 0);
            } else {
                $begin->setTimestamp($begin_date);
            }
            //Now we do the same for $end_timestamp:
            if (preg_match($date_regex, $end_date)) {
                //$begin is specified in the date formay YYYY-MM-DD:
                $end_str = explode('-', $end_date);
                $end->setDate(
                    intval($end_str[0]),
                    intval($end_str[1]),
                    intval($end_str[2])
                );
                $end->setTime(23, 59, 59);
            } else {
                $end->setTimestamp($end_date);
            }
        }

        //Get parameters:
        $booking_types = [];
        if (!$nobody_access) {
            $booking_types = explode(',', Request::get('booking_types'));
        }

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
        if ($display_all_requests) {
            $requests = ResourceRequest::findByResourceAndTimeRanges(
                $resource,
                [
                    [
                        'begin' => $begin_timestamp,
                        'end'   => $end_timestamp
                    ]
                ],
                0
            );
        } else if ($display_requests) {
            //Get the users own request only:
            $requests = ResourceRequest::findByResourceAndTimeRanges(
                $resource,
                [
                    [
                        'begin' => $begin_timestamp,
                        'end'   => $end_timestamp
                    ]
                ],
                0,
                [],
                'user_id = :user_id',
                ['user_id' => $current_user->id]
            );
        }

        $objects    = array_merge($bookings, $requests);
        $event_data = Studip\Fullcalendar::createData($objects, $begin_timestamp, $end_timestamp);

        if ($nobody_access) {
            //For nobody users, the code stops here since
            //nobody users are not allowed to include additional objects.
            $this->render_json($event_data);
            return;
        }

        //Check if there are additional objects to be displayed:
        $additional_objects        = Request::getArray('additional_objects');
        $additional_object_colours = Request::getArray('additional_object_colours');
        if ($additional_objects) {
            foreach ($additional_objects as $object_class => $object_ids) {
                if (
                    !is_a($object_class, SimpleORMap::class, true)
                    || !is_a($object_class, Studip\Calendar\EventSource::class, true)
                ) {
                    continue;
                }

                $special_colours = [];
                if ($additional_object_colours[$object_class]) {
                    $special_colours = $additional_object_colours[$object_class];
                }

                $additional_objects = $object_class::findMany($object_ids);
                foreach ($additional_objects as $additional_object) {
                    $event_data = $additional_object->getFilteredEventData(
                        $current_user->id,
                        null,
                        null,
                        $begin,
                        $end
                    );

                    if ($special_colours) {
                        foreach ($event_data as $data) {
                            $data->text_colour       = $special_colours['fg'];
                            $data->background_colour = $special_colours['bg'];
                            $data->editable          = false;
                            $event_data[]            = $data->toFullcalendarEvent();
                        }
                    }
                }
            }
        }
        $this->render_json($event_data);
    }
}

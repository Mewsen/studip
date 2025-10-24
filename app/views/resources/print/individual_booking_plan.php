<? if (Request::get("allday")) {
        $min_time = '00:00:00';
        $max_time = '24:00:00';
    } else {
        $min_time = Config::get()->RESOURCES_BOOKING_PLAN_START_HOUR . ':00';
        $max_time = Config::get()->RESOURCES_BOOKING_PLAN_END_HOUR . ':00';
    } ?>
<section class="individual-booking-plan">
    <?= \Studip\VueApp::create('StudipTintableCalendar')
        ->withProps(
            [
                'config' => [
                    'eventSources' => [
                        [
                            'url' => URLHelper::getURL(
                                'dispatch.php/resources/ajax/get_semester_booking_plan/' . $resource->id
                            ),
                            'method' => 'GET',
                            'extraParams' => [
                                'booking_types' => [
                                    ResourceBooking::TYPE_NORMAL,
                                    ResourceBooking::TYPE_RESERVATION,
                                    ResourceBooking::TYPE_LOCK,
                                ],
                            ]
                        ]
                    ],
                    'slotMinTime' => ($min_time),
                    'slotMaxTime' => ($max_time),
                    'headerToolbar' => [
                        'left' => '',
                        'center' => '',
                        'right' => ''
                    ],
                    'allDaySlot' => false,
                    'initialView' =>
                        in_array(Request::get("defaultView"), ['dayGridMonth','timeGridWeek','timeGridDay'])
                            ? Request::get("defaultView")
                            : 'timeGridWeek',
                    'initialDate' => Request::get("defaultDate"),
                    'editable' => false
                ]
            ]
        ) ?>
</section>

<? if (Request::get("allday")) {
        $min_time = '00:00:00';
        $max_time = '24:00:00';
    } else {
        $min_time = Config::get()->RESOURCES_BOOKING_PLAN_START_HOUR . ':00';
        $max_time = Config::get()->RESOURCES_BOOKING_PLAN_END_HOUR . ':00';
    } ?>
<section class="individual-booking-plan">
    <?= \Studip\Fullcalendar::create(
        _('Individueller Belegungsdruck'),
        [
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
                        'semester_id' => $semester->id,
                        'semester_timerange' => Request::get('semester_timerange', 'vorles'),
                    ]
                ]
            ],
            'slotMinTime' => ($min_time),
            'slotMaxTime' => ($max_time),
            'headerToolbar' => [
                'start' => '',
                'center' => '',
                'end' => ''
            ],
            'allDaySlot' => false,
            'initialView' => \Studip\Fullcalendar::VIEW_WEEK,
            'initialDate' => ((Request::get('semester_timerange') === 'fullsem') ? date('Y-m-d', $semester->beginn) : date('Y-m-d', $semester->vorles_beginn)),
            'display_holidays' => false,
            'display_vacations' => false,
            'editable' => false,
            'event_colour_picker' => true
        ]
    ) ?>
</section>

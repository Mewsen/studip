<?php
/**
 * @var string $plan_title
 * @var array $events
 * @var array $eventless_courses
 * @var Admin_CourseplanningController $controller
 */

$min_time = Config::get()->INSTITUTE_COURSE_PLAN_START_HOUR . ':00';
$max_time = Config::get()->INSTITUTE_COURSE_PLAN_END_HOUR . ':00';
?>

<?= Studip\Fullcalendar::create($plan_title, [
    'slotMinTime' => $min_time,
    'slotMaxTime' => $max_time,
    'allDaySlot' => false,
    'nowIndicator' => false,
    'slotDuration' => '01:00:00',
    'slotLabelInterval' => '01:00',
    'slotLabelFormat' => ['hour' => '2-digit', 'minute' => '2-digit'],
    'headerToolbar' => [
        'start' => '',
        'end' => ''
    ],
    'dayHeaderFormat' => ['weekday' => 'long'],
    'views' => [
        \Studip\Fullcalendar::VIEW_WEEK => [
            'dayHeaderFormat' => ['weekday' => 'short'],
            'weekends'        => true,
            'titleFormat'     => [],
            'weekText'   => ''
        ]
    ],
    'initialView' => \Studip\Fullcalendar::VIEW_WEEK,
    'display_holidays' => false,
    'display_vacations' => false,
    'eventSources' => [compact('events')],
    'slotEventOverlap' => false,
    'displayEventTime' => false,
    'editable' => true,
    'droppable' => true, // this allows things to be dropped onto the calendar
    'external_droppable_container_id' => 'droppable-course-container',
    'external_droppable_event_selector' => 'td.draggable-course'
], [
    'class' => 'institute-plan'
]) ?>

<br>

<? if (count($eventless_courses)) : ?>
<table class="default course-planning" id="droppable-course-container">
    <tr>
        <th><?= _('Veranstaltungen ohne Termine') ?></th>
    </tr>
    <? foreach ($eventless_courses as $cid => $cname) : ?>
        <tr>
            <?
            $event_object = [
                'title'           => $cname,
                'duration'        => '02:00',
                'studip_api_urls' => [
                    'receive' => $controller->link_for('admin/courseplanning/add_event/' . $cid)
                ]
            ];
            ?>
            <td class="draggable-course" data-event="<?= htmlReady(json_encode($event_object)) ?>">
                <?= htmlReady($cname) ?>
            </td>
        </tr>
    <? endforeach ?>
</table>
<? endif ?>

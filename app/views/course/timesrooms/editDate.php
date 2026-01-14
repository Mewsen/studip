<?php
/**
 * @var Course_TimesroomsController $controller
 * @var CourseDate $date
 * @var int $preparation_time
 * @var int $subsequent_time
 * @var int $max_preparation_time
 * @var array $selected_room_ids
 * @var array $available_lecturers
 * @var array $available_groups
 * @var array $assigned_lecturers
 * @var array $assigned_groups
 * @var bool $allow_multiple_room_bookings
 */
?>
<form class="default" method="post"
      action="<?= $controller->link_for('course/timesrooms/saveDate/' . $date->termin_id) ?>">
    <?= CSRFProtection::tokenTag() ?>
    <?= Studip\VueApp::create('CourseDateFormContent')
        ->withProps([
            'course_date'                  => $date->toRawArray(['termin_id', 'date', 'end_time', 'date_typ', 'raum', 'number_of_participants', 'content']),
            'initial_preparation_time'     => $preparation_time,
            'initial_subsequent_time'      => $subsequent_time,
            'max_preparation_time'         => $max_preparation_time,
            'date_types'                   => $date_types ?? [],
            'room_management_enabled'      => Config::get()->RESOURCES_ENABLE,
            'selected_rooms'               => $selected_room_ids ?? [],
            'available_lecturers'          => $available_lecturers ?? [],
            'available_groups'             => $available_groups ?? [],
            'selected_lecturers'           => $assigned_lecturers,
            'selected_groups'              => $assigned_groups,
            'allow_multiple_room_bookings' => $allow_multiple_room_bookings
        ]) ?>
    <footer data-dialog-button>
        <?= \Studip\Button::createAccept(_('Speichern'), 'save') ?>
        <? if (Request::bool('fromDialog')) : ?>
            <?= Studip\LinkButton::create(
                _('Zurück zur Übersicht'),
                $controller->url_for(
                    'course/timesrooms',
                    ['fromDialog' => 1, 'contentbox_open' => $date->metadate_id]
                ),
                ['data-dialog' => 'size=big']) ?>
        <? endif ?>
        <?= \Studip\Button::createCancel(_('Abbrechen'), 'abort') ?>
    </footer>
</form>

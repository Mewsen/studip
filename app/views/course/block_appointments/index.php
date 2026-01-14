<?php
/**
 * @var Course_BlockAppointmentsController $controller
 * @var string $course_id
 * @var int $preparation_time
 * @var int $subsequent_time
 * @var int $max_preparation_time
 * @var array $date_types
 * @var array $dow
 * @var int $selected_date_type
 * @var array $lecturers
 * @var bool $allow_multiple_room_bookings
 * @var string $selected_date_type
 * @var array $selected_lecturer_ids
 * @var array $available_lecturers
 * @var array $assigned_lecturers
 */
?>
<form <?= Request::isXhr() ? 'data-dialog="size=big"' : '' ?>
    class="default"
    action="<?= $controller->link_for('course/block_appointments/save/' . $course_id) ?>"
    method="post">
    <?= CSRFProtection::tokenTag() ?>
    <?= Studip\VueApp::create('CourseBlockAppointments')
        ->withProps([
            'initial_preparation_time'     => $preparation_time,
            'initial_subsequent_time'      => $subsequent_time,
            'max_preparation_time'         => $max_preparation_time,
            'room_management_enabled'      => Config::get()->RESOURCES_ENABLE,
            'allow_multiple_room_bookings' => $allow_multiple_room_bookings ?? false,
            'date_types'                   => $date_types ?? [],
            'available_lecturers'          => $available_lecturers ?? [],
            'selected_lecturers'           => $selected_lecturer_ids
        ]) ?>
    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'save') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('course/timesrooms/index'), ['data-dialog' => 'size=big']) ?>
    </footer>
</form>

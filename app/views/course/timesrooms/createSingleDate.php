<?php
/**
 * @var Course_TimesroomsController $controller
 * @var array $date_types
 * @var array $available_lecturers
 * @var array $available_groups
 * @var bool $allow_multiple_room_bookings
 */
?>
<form class="default" method="post"
      action="<?= $controller->link_for('course/timesrooms/saveDate') ?>">
    <?= CSRFProtection::tokenTag() ?>
    <?= Studip\VueApp::create('CourseDateFormContent')
        ->withProps([
            'course_date'                  => null,
            'preparation_time'             => 0,
            'subsequent_time'              => 0,
            'date_types'                   => $date_types ?? [],
            'room_management_enabled'      => Config::get()->RESOURCES_ENABLE,
            'available_lecturers'          => $available_lecturers ?? [],
            'available_groups'             => $available_groups ?? [],
            'allow_multiple_room_bookings' => $allow_multiple_room_bookings
        ]) ?>
    <footer data-dialog-button>
        <?= \Studip\Button::createAccept(_('Speichern'), 'save') ?>
        <? if (Request::bool('fromDialog')) : ?>
            <?= Studip\LinkButton::create(
                _('Zurück zur Übersicht'),
                $controller->url_for(
                    'course/timesrooms',
                    ['fromDialog' => 1]
                ),
                ['data-dialog' => 'size=big']) ?>
        <? endif ?>
        <?= \Studip\Button::createCancel(_('Abbrechen'), 'abort') ?>
    </footer>
</form>

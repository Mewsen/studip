<?php
/**
 * @var Trails_Controller $controller The controller.
 * @var array $selected_course_ids The IDs of the selected courses.
 * @var string $selected_semester_id The ID of the selected semester.
 * @var array $available_semester_data The data of all available semesters.
 */
?>
<form class="default" method="post" action="<?= $controller->link_for('calendar/calendar/add_courses') ?>">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset class="simplevue">
        <legend><?= _('Veranstaltungen für den Kalender auswählen') ?></legend>
        <my-courses-coloured-table name="courses"
                                   :selected_course_ids="<?= htmlReady(json_encode($selected_course_ids)) ?>"
                                   :default_semester_id="'<?= htmlReady($selected_semester_id) ?>'"
                                   :semester_data="<?= htmlReady(json_encode($available_semester_data)) ?>"
        ></my-courses-coloured-table>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::create(_('Übernehmen'), 'add') ?>
        <?= \Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('calendar/calendar')) ?>
    </div>
</form>

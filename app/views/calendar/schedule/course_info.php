<?php
/**
 * @var AuthenticatedController $controller
 * @var Course $course
 * @var CourseMember $membership
 * @var ScheduleCourseDate $schedule_course_entry
 */
?>
<? if ($course) : ?>
    <h2><?= htmlReady($course->getFullName()) ?></h2>
    <form class="default" method="post" data-dialog="reload-on-close"
          action="<?= $controller->link_for('calendar/schedule/course_info/' . $course->id) ?>">
        <?= CSRFProtection::tokenTag() ?>
        <? if ($membership) : ?>
            <fieldset>
                <legend><?= _('Farbe') ?></legend>
                <table class="default mycourses-group-selector">
                    <tr>
                        <?= $this->render_partial(
                            'my_courses/group_selector',
                            [
                                'course_id'         => $course->id,
                                'selected_group_id' => $membership->gruppe
                            ]
                        ) ?>
                    </tr>
                </table>
            </fieldset>
        <? endif ?>
        <fieldset>
            <legend><?= _('Informationen') ?></legend>
            <section>
                <h3><?= _('Veranstaltungsnummer') ?></h3>
                <p><?= htmlReady($course->veranstaltungsnummer) ?></p>
                <h3><?= _('Lehrende') ?></h3>
                <ul class="default">
                    <?
                    $lecturers = CourseMember::findByCourseAndStatus($course->id, 'dozent');
                    ?>
                    <? foreach ($lecturers as $lecturer) : ?>
                        <li>
                            <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $lecturer->username]) ?>">
                                <?= htmlReady($lecturer->user->getFullName()) ?>
                            </a>
                        </li>
                    <? endforeach ?>
                </ul>
                <h3><?= _('Veranstaltungszeiten') ?></h3>
                <?= $course->getAllDatesInSemester()->toHtml() ?>
            </section>
        </fieldset>
        <div data-dialog-button>
            <?= \Studip\Button::create(
                _('Speichern'),
                'save',
                ['formaction' => $controller->url_for('calendar/schedule/save_course_info/' . $course->id)]
            ) ?>
            <? if ($schedule_course_entry && !$schedule_course_entry->visible) : ?>
                <?= \Studip\Button::create(
                    _('Veranstaltung einblenden'),
                    'show',
                    ['formaction' => $controller->url_for('calendar/schedule/show_course/' . $course->id)]
                ) ?>
            <? else : ?>
                <?= \Studip\Button::create(
                    _('Veranstaltung ausblenden'),
                    'hide',
                    ['formaction' => $controller->url_for('calendar/schedule/hide_course/' . $course->id)]
                ) ?>
            <? endif ?>
            <?= \Studip\LinkButton::create(
                _('Direkt zur Veranstaltung'),
                URLHelper::getURL('dispatch.php/course/overview', ['cid' => $course->id])
            ) ?>
        </div>
    </form>
<? endif ?>

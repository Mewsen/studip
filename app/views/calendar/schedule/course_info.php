<?php
/**
 * @var AuthenticatedController $controller
 * @var SeminarCycleDate $cycle_date
 * @var Course $course
 * @var CourseMember $membership
 * @var ScheduleCourseDate $schedule_course_entry
 */
?>
<? if ($cycle_date && $course) : ?>
    <form class="default" method="post" data-dialog="reload-on-close"
          action="<?= $controller->link_for('calendar/schedule/course_info/' . $course->id) ?>">
        <?= CSRFProtection::tokenTag() ?>
        <? if ($membership) : ?>
            <fieldset>
                <legend><?= _('Farbe') ?></legend>
                <?= Studip\VueApp::create('ColourSelector')
                    ->withProps([
                        'autofocus' => true,
                        'colours' => collect()->range(0, 8)->map(
                            fn($group) => [
                                'id' => $group,
                                'class' => 'gruppe' . $group,
                                'label' => sprintf(_('Gruppe %u zuordnen'), $group + 1),
                            ]
                        )->values(),
                        'input-name' => 'gruppe[' . htmlReady($course->id) . ']',
                        'model-value' => $membership->gruppe,
                    ]) ?>
            </fieldset>
        <? endif ?>
        <fieldset>
            <legend><?= _('Informationen') ?></legend>
            <section>
                <? if ($course->veranstaltungsnummer) : ?>
                    <h3><?= _('Veranstaltungsnummer') ?></h3>
                    <p><?= htmlReady($course->veranstaltungsnummer) ?></p>
                <? endif ?>
                <h3><?= _('Name') ?></h3>
                <p><?= htmlReady($course->getFullName('type-name')) ?></p>
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
                <?= $course->getAllDatesInSemester()->toHtml(false, true) ?>
            </section>
            <section>
                <?
                $enrolment_info = $course->getEnrolmentInformation($GLOBALS['user']->id);
                ?>
                <? if ($enrolment_info->isEnrolmentAllowed()) : ?>
                    <a href="<?= URLHelper::getLink('dispatch.php/course/overview', ['cid' => $course->id]) ?>">
                        <?= _('Direkt zur Veranstaltung') ?>
                        <?= Icon::create('link-intern')->asImg(Icon::SIZE_INLINE, ['class' => 'text-bottom']) ?>
                    </a>
                <? else : ?>
                    <a href="<?= URLHelper::getLink('dispatch.php/course/details', ['sem_id' => $course->id]) ?>">
                        <?= _('Direkt zur Veranstaltung') ?>
                        <?= Icon::create('link-intern')->asImg(Icon::SIZE_INLINE, ['class' => 'text-bottom']) ?>
                    </a>
                <? endif ?>
            </section>
        </fieldset>
        <div data-dialog-button>
            <?= \Studip\Button::createAccept(
                _('Speichern'),
                'save',
                ['formaction' => $controller->url_for('calendar/schedule/save_course_info/' . $course->id)]
            ) ?>
            <? if ($schedule_course_entry && !$schedule_course_entry->visible) : ?>
                <?= \Studip\Button::create(
                    _('Termin einblenden'),
                    'show',
                    ['formaction' => $controller->url_for('calendar/schedule/show_course/' . $cycle_date->id)]
                ) ?>
            <? else : ?>
                <?= \Studip\Button::create(
                    _('Termin ausblenden'),
                    'hide',
                    ['formaction' => $controller->url_for('calendar/schedule/hide_course/' . $cycle_date->id)]
                ) ?>
            <? endif ?>
            <?= \Studip\Button::createCancel(_('Abbrechen')) ?>
        </div>
    </form>
<? endif ?>

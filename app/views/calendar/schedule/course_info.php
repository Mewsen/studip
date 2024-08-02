<?
/**
 * @var AuthenticatedController $controller
 * @var Course $course
 * @var CourseMember $membership
 */
?>
<? if ($course) : ?>
    <h2><?= htmlReady($course->getFullName()) ?></h2>
    <form class="default" method="post" data-dialog="reload-on-close"
          action="<?= $controller->link_for('calendar/schedule/course_info/' . $course->id) ?>">
        <?= CSRFProtection::tokenTag() ?>
        <fieldset>
            <legend><?= _('Farbe') ?></legend>
            <table class="default">
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
                            <a href="<?php echo URLHelper::getLink('dispatch.php/profile', ['username' => $lecturer->username]) ?>">
                                <?php echo htmlReady($lecturer->user->getFullName()) ?>
                            </a>
                        </li>
                    <? endforeach ?>
                </ul>
                <h3><?= _('Veranstaltungszeiten') ?></h3>
                <?= $course->getAllDatesInSemester()->toHtml() ?>
            </section>
        </fieldset>
        <div data-dialog-button>
            <?= \Studip\LinkButton::create(
                _('Direkt zur Veranstaltung'),
                URLHelper::getURL('dispatch.php/course/overview', ['cid' => $course->id])
            ) ?>
            <?= \Studip\Button::create(_('Ausblenden'), 'hide') ?>
        </div>
    </form>
<? endif ?>

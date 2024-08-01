<?php
/**
 * @var AuthenticatedController $controller
 * @var Course $course
 * @var CourseMember $membership
 */
?>
<?php if ($course) : ?>
    <h2><?php echo htmlReady($course->getFullName()) ?></h2>
    <form class="default simplevue" method="post" data-dialog="reload-on-close"
          action="<?php echo $controller->link_for('calendar/schedule/course_info/' . $course->id) ?>">
        <?php echo CSRFProtection::tokenTag() ?>
        <fieldset>
            <legend><?php _('Farbe') ?></legend>
            TODO: Vue-Komponente für Farben.
        </fieldset>
        <fieldset>
            <legend><?php _('Informationen') ?></legend>
            <section>
                <h3><?php echo _('Veranstaltungsnummer') ?></h3>
                <p><?php echo htmlReady($course->veranstaltungsnummer) ?></p>
                <h3><?php echo _('Lehrende') ?></h3>
                <ul class="default">
                    <?php
                    $lecturers = CourseMember::findByCourseAndStatus($course->id, 'dozent');
                    ?>
                    <?php foreach ($lecturers as $lecturer) : ?>
                        <li>
                            <a href="<?php echo URLHelper::getLink('dispatch.php/profile', ['username' => $lecturer->username]) ?>">
                                <?php echo htmlReady($lecturer->user->getFullName()) ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
                <h3><?php echo _('Veranstaltungszeiten') ?></h3>
                <?php
                //TODO: replace with new code after the seminar class StEP is merged!
                $sem = new Seminar($course);
                echo $sem->getDatesTemplate(
                    'dates/seminar_html_location',
                    ['ort' => $course->ort]
                );
                ?>
            </section>
        </fieldset>
        <div data-dialog-button>
            <?php echo \Studip\LinkButton::create(
                _('Direkt zur Veranstaltung'),
                URLHelper::getURL('dispatch.php/course/overview', ['cid' => $course->id])
            ) ?>
            <?php echo \Studip\Button::create(_('Ausblenden'), 'hide') ?>
        </div>
    </form>
<?php endif ?>

<?= $this->render_partial("course/studygroup/_feedback") ?>

<form action="<?= $controller->update() ?>" method="post" class="default">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= _('Grunddaten') ?>
        </legend>

        <input type='submit' class="invisible" name="<?=_('Änderungen übernehmen') ?>" aria-hidden="true">
        <label>
            <span class="required"><?= _('Name') ?></span>
            <input type='text' name='groupname' value="<?= htmlReady($course->name) ?>">
        </label>

        <label>
            <?= _('Beschreibung') ?>
            <textarea name="groupdescription"><?= htmlReady($course->beschreibung) ?></textarea>
        </label>

        <? if ($GLOBALS['perm']->have_studip_perm('dozent', $course->id)) : ?>
            <?= $this->render_partial('course/studygroup/_replace_founder', ['tutors' => $tutors]) ?>
        <? endif ?>

        <label>
            <?= _('Zugang') ?>
            <select name="groupaccess">
                <option value="all" <? if (!$course->admission_prelim) echo 'selected'; ?>>
                    <?= _('Offen für alle') ?>
                </option>
                <option value="invite" <? if ($course->admission_prelim) echo 'selected'; ?>>
                    <?= _('Auf Anfrage') ?>
                </option>
            <? if (Config::get()->STUDYGROUPS_INVISIBLE_ALLOWED || !$course->visible): ?>
                <option value="invisible" <? if (!$course->visible) echo 'selected'; ?> <? if (!Config::get()->STUDYGROUPS_INVISIBLE_ALLOWED) echo 'disabled'; ?>>
                    <?= _('Unsichtbar') ?>
                </option>
            <? endif; ?>
            <? if (true) : ?>
                <? $courseset = CourseSet::getSetForCourse($sem_id) ?>
                <option value="top-course"<?= $courseset && $courseset->getId() === CourseSet::getConnectedcourseAdmissionSetId() ? ' selected' : '' ?>>
                    <?= _('Für Mitglieder der zugehörigen Lehrveranstaltung') ?>
                </option>
            <? endif ?>
            <? if (true) : ?>
                <? $courseset = CourseSet::getSetForCourse($sem_id) ?>
                <option value="top-course"<?= $courseset && $courseset->getId() === CourseSet::getConnectedcourseAdmissionSetId() ? ' selected' : '' ?>>
                    <?= _('Für Mitglieder der zugehörigen Lehrveranstaltung') ?>
                </option>
            <? endif ?>
            </select>
        </label>

    </fieldset>

    <fieldset>
        <legend><?= _('Erweiterte Einstellungen') ?></legend>

        <label>
            <?= _('Ablaufdatum') ?>
            <input type="text" name="expiration_date">
        </label>

        <label>
            <?= _('Schlagwörter') ?>
        </label>
    </fieldset>

    <footer>
        <?= Studip\Button::createAccept(_('Übernehmen'), ['title' => _("Änderungen übernehmen")]); ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), URLHelper::getURL('dispatch.php/course/go')); ?>
    </footer>
</form>

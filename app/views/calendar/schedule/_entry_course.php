<?php
$course = Course::find($show_entry['id']);
?>
<form class="default"
      action="<?= $controller->link_for('calendar/schedule/editseminar/' . $show_entry['id'] . '/' . $show_entry['cycle_id']) ?>"
      method="post" name="edit_entry">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= _('Stundenplaneintrag') ?>
        </legend>

        <?= $this->render_partial('calendar/schedule/_colorpicker.php', [
            'selected' => $show_entry['color'],
        ]) ?>

        <? if ($show_entry['type'] == 'virtual') : ?>
            <section>
                <span
                    style="color: red; font-weight: bold"><?= _('Dies ist lediglich eine vorgemerkte Veranstaltung') ?></span><br><br>
            </section>
        <? endif ?>

        <section>
            <strong><?= _('Veranstaltungsnummer') ?></strong><br>
            <?= htmlReady($course->veranstaltungsnummer) ?>
        </section>

        <section>
            <strong><?= _('Name') ?></strong><br>
            <?= htmlReady($course->name) ?>
        </section>

        <section>
            <strong><?= _('Lehrende') ?></strong><br>
            <?
            $pos = 0;
            $lecturers = CourseMember::findByCourseAndStatus($course->id, 'dozent');
            foreach ($lecturers as $lecturer) :?>
                <?= $pos > 0 ? ', ' : '' ?>
                <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $lecturer->user->username]) ?>">
                    <?= htmlReady($lecturer->user->getFullName()) ?>
                </a>
                <? $pos++ ?>
            <? endforeach ?>
        </section>

        <section>
            <strong><?= _('Veranstaltungszeiten') ?></strong><br>
            <?= $course->getAllDatesInSemester()->toHtml(true) ?><br>
        </section>

        <section>
            <?= Icon::create('link-intern') ?>
            <? if ($show_entry['type'] == 'virtual') : ?>
                <a href="<?= URLHelper::getLink('dispatch.php/course/details', ['sem_id' => $show_entry['id']]) ?>">
                    <?= _('Zur Veranstaltung') ?>
                </a>
                <br>
            <? else : ?>
                <a href="<?= URLHelper::getLink('seminar_main.php', ['auswahl' => $show_entry['id']]) ?>">
                    <?= _('Zur Veranstaltung') ?>
                </a>
                <br>
            <? endif ?>
        </section>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), ['style' => 'margin-right: 20px']) ?>

        <? if (!$show_entry['visible']) : ?>
            <?= Studip\LinkButton::create(
                _('Einblenden'),
                $controller->url_for(
                    'calendar/schedule/bind/' . $show_entry['id'] . '/' . $show_entry['cycle_id'] . '/',
                    ['show_hidden' => '1']
                ),
                ['style' => 'margin-right: 20px']) ?>
        <? else : ?>
            <?= Studip\LinkButton::create(
                $show_entry['type'] == 'virtual' ? _('Löschen') : _('Ausblenden'),
                $controller->url_for('calendar/schedule/unbind/' . $show_entry['id'] . '/' . $show_entry['cycle_id']),
                ['style' => 'margin-right: 20px']) ?>
        <? endif ?>

        <?= Studip\LinkButton::createCancel(
            _('Abbrechen'),
            $controller->url_for('calendar/schedule'),
            ['onclick' => "jQuery('#edit_sem_entry').fadeOut('fast'); STUDIP.Calendar.click_in_progress = false; return false"]) ?>
    </footer>
</form>

<?php
/**
 * @var Calendar_CalendarController $controller
 */
?>
<form class="default"
      method="post"
      data-dialog="size=auto"
      enctype="multipart/form-data"
      action="<?= $controller->link_for('calendar/calendar/import_file/') ?>">
    <input type="hidden" name="studip_ticket" value="<?= get_ticket() ?>">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= sprintf(_('Termine importieren')) ?>
        </legend>
        <label for="event-type">
            <input type="checkbox" name="import_privat" value="1" checked>
            <?= _('Öffentliche Termine als "privat" importieren') ?>
        </label>
        <label style="cursor: pointer;">
            <input required type="file" id="fileupload" name="importfile" accept=".ics,.ifb,.iCal,.iFBf"
                   style="display: none">
            <?= Icon::create('upload', Icon::ROLE_CLICKABLE, ['title' => _('Datei hochladen'), 'class' => 'text-bottom']) ?>
            <span class="required"><?= _('Datei zum Importieren wählen') ?></span>
        </label>
    </fieldset>
    <footer data-dialog-button>
        <?= \Studip\Button::create(_('Importieren'), 'import') ?>
        <?= \Studip\Button::createCancel(_('Abbrechen')) ?>
    </footer>
</form>

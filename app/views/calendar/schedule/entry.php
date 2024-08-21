<?php
/**
 * @var AuthenticatedController $controller
 * @var ScheduleEntry $entry The schedule entry to be created/modified.
 */
?>
<form class="default" method="post" action="<?= $controller->link_for('calendar/schedule/entry/' . ($entry->isNew() ? 'add' : $entry->id)) ?>"
      data-dialog="reload-on-close">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Zeit') ?></legend>
        <section class="flex-row">
        <label>
            <?= _('Wochentag') ?>
            <select name="dow">
                <option value="1" <?= $entry->dow === 1 ? 'selected' : '' ?>>
                    <?= _('Montag') ?>
                </option>
                <option value="2" <?= $entry->dow === 2 ? 'selected' : '' ?>>
                    <?= _('Dienstag') ?>
                </option>
                <option value="3" <?= $entry->dow === 3 ? 'selected' : '' ?>>
                    <?= _('Mittwoch') ?>
                </option>
                <option value="4" <?= $entry->dow === 4 ? 'selected' : '' ?>>
                    <?= _('Donnerstag') ?>
                </option>
                <option value="5" <?= $entry->dow === 5 ? 'selected' : '' ?>>
                    <?= _('Freitag') ?>
                </option>
                <option value="6" <?= $entry->dow === 6 ? 'selected' : '' ?>>
                    <?= _('Samstag') ?>
                </option>
                <option value="7" <?= $entry->dow === 7 ? 'selected' : '' ?>>
                    <?= _('Sonntag') ?>
                </option>
            </select>
        </label>
        <label>
            <?= _('Startuhrzeit') ?>
            <input type="text" class="has-time-picker" name="start"
                   value="<?= htmlReady($entry->getFormattedStart()) ?>">
        </label>
        <label>
            <?= _('Enduhrzeit') ?>
            <input type="text" class="has-time-picker" name="end"
                   value="<?= htmlReady($entry->getFormattedEnd()) ?>">
        </label>
        </section>
    </fieldset>
    <fieldset>
        <legend><?= _('Inhalt') ?></legend>
        <label>
            <?= _('Titel') ?>
            <input type="text" name="label" value="<?= htmlReady($entry->label) ?>">
        </label>
        <label>
            <?= _('Beschreibung') ?>
            <textarea name="content"><?= htmlReady($entry->content) ?></textarea>
        </label>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::create(_('Speichern'), 'save') ?>
        <? if (!$entry->isNew()) : ?>
            <?= \Studip\Button::create(_('Löschen'), 'delete') ?>
        <? endif ?>
        <?= \Studip\Button::createCancel(_('Abbrechen')) ?>
    </div>
</form>

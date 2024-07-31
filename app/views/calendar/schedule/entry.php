<?php
/**
 * @var AuthenticatedController $controller
 * @var ScheduleEntry $entry The schedule entry to be created/modified.
 */
?>
<form class="default" method="post" action="<?php echo $controller->link_for('calendar/schedule/entry/' . ($entry->isNew() ? 'add' : $entry->id)) ?>"
      data-dialog="reload-on-close">
    <?php echo CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?php echo _('Zeit') ?></legend>
        <section class="flex-row">
        <label>
            <select name="dow">
                <option value="1" <?php echo $entry->dow === 1 ? 'selected' : '' ?>>
                    <?php echo _('Montag') ?>
                </option>
                <option value="2" <?php echo $entry->dow === 2 ? 'selected' : '' ?>>
                    <?php echo _('Dienstag') ?>
                </option>
                <option value="3" <?php echo $entry->dow === 3 ? 'selected' : '' ?>>
                    <?php echo _('Mittwoch') ?>
                </option>
                <option value="4" <?php echo $entry->dow === 4 ? 'selected' : '' ?>>
                    <?php echo _('Donnerstag') ?>
                </option>
                <option value="5" <?php echo $entry->dow === 5 ? 'selected' : '' ?>>
                    <?php echo _('Freitag') ?>
                </option>
                <option value="6" <?php echo $entry->dow === 6 ? 'selected' : '' ?>>
                    <?php echo _('Samstag') ?>
                </option>
                <option value="7" <?php echo $entry->dow === 7 ? 'selected' : '' ?>>
                    <?php echo _('Sonntag') ?>
                </option>
            </select>
        </label>
        <label>
            <?php echo _('Startuhrzeit') ?>
            <input type="text" class="has-time-picker" name="start"
                   value="<?php echo htmlReady($entry->getFormattedStart()) ?>">
        </label>
        <label>
            <?php echo _('Enduhrzeit') ?>
            <input type="text" class="has-time-picker" name="end"
                   value="<?php echo htmlReady($entry->getFormattedEnd()) ?>">
        </label>
        </section>
    </fieldset>
    <fieldset>
        <legend><?php echo _('Inhalt') ?></legend>
        <label>
            <?php echo _('Titel') ?>
            <input type="text" name="label" value="<?= htmlReady($entry->label) ?>">
        </label>
        <label>
            <?php echo _('Beschreibung') ?>
            <textarea name="content"><?php echo htmlReady($entry->content) ?></textarea>
        </label>
    </fieldset>
    <div data-dialog-button>
        <?php echo \Studip\Button::create(_('Speichern'), 'save') ?>
        <?php echo \Studip\Button::createCancel(_('Abbrechen')) ?>
    </div>
</form>

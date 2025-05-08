<?php
/**
 * @var AuthenticatedController $controller
 * @var ScheduleEntry $entry The schedule entry to be created/modified.
 */
?>
<form class="default schedule-entry" method="post"
      action="<?= $controller->link_for('calendar/schedule/entry/' . ($entry->isNew() ? 'add' : $entry->id)) ?>"
      data-dialog="reload-on-close">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Farbe') ?></legend>
        <?= Studip\VueApp::create('ColourSelector')
            ->withProps([
                'autofocus' => true,
                'colours' => collect($GLOBALS['PERS_TERMIN_KAT'])->map(
                    fn($data, $id) => ['id' => $id, 'colour' => $data['bgcolor']]
                )->values(),
                'model-value' => $entry->colour_id,
            ]) ?>
    </fieldset>
    <fieldset>
        <legend><?= _('Zeit') ?></legend>
        <section class="hgroup nowrap">
            <label>
                <?= _('Wochentag') ?>
                <select name="dow" class="size-s">
                    <? foreach (range(1, 7) as $dow): ?>
                        <option value="<?= $dow ?>" <?= $entry->dow == $dow ? 'selected' : '' ?>>
                            <?= getWeekday($dow, false) ?>
                        </option>
                    <? endforeach ?>
                </select>
            </label>
            <label>
                <?= _('Anfang') ?>
                <input type="text" class="has-time-picker size-s" name="start"
                       value="<?= htmlReady($entry->getFormattedStart()) ?>">
            </label>
            <label>
                <?= _('Ende') ?>
                <input type="text" class="has-time-picker size-s" name="end"
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
        <?= \Studip\Button::createAccept(
            _('Speichern'),
            'save',
            ['formaction' => $controller->url_for('calendar/schedule/save_entry/' . ($entry->isNew() ? 'add' : $entry->id))]
        ) ?>
        <? if (!$entry->isNew()) : ?>
            <?= \Studip\Button::create(
                _('Löschen'),
                'delete',
                ['formaction' => $controller->url_for('calendar/schedule/delete_entry/' . $entry->id)]
            ) ?>
        <? endif ?>
        <?= \Studip\Button::createCancel(_('Abbrechen')) ?>
    </div>
</form>

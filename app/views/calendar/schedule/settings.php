<?php
/**
 * @var StudipController $controller
 * @var array $schedule_settings
 */
?>
<form class="default" method="post" action="<?= $controller->link_for('calendar/schedule/save_settings') ?>"
    <?= Request::isDialog() ? 'data-dialog="reload-on-close"' : '' ?>>
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Zeiten') ?></legend>
        <label>
            <?= _('Anfang') ?>
            <select name="start_time" aria-label="<?= _('Anfang des Stundenplans') ?>" class="size-s">
                <? for ($i = 0; $i < 24; $i += 1): ?>
                    <? $value = sprintf('%02u:00', $i); ?>
                    <option value="<?= htmlReady($value) ?>"
                        <?= $schedule_settings['start_time'] === $value ? 'selected' : '' ?>>
                        <?= studip_interpolate('%{time} Uhr', ['time' => $value]) ?>
                    </option>
                <? endfor ?>
            </select>
        </label>
        <label>
            <?= _('Ende') ?>
            <select name="end_time" aria-label="<?= _('Ende des Stundenplans') ?>" class="size-s">
                <? for ($i = 0; $i < 24; $i += 1): ?>
                    <? $value = sprintf('%02u:00', $i); ?>
                    <option value="<?= $value ?>"
                        <?= $schedule_settings['end_time'] === $value ? 'selected' : '' ?>>
                        <?= studip_interpolate('%{time} Uhr', ['time' => $value]) ?>
                    </option>
                <? endfor ?>
            </select>
        </label>
        <label>
            <input type="radio" name="weekdays" value="7"
                <?= $schedule_settings['weekdays'] === 7 ? 'checked' : '' ?>>
            <?= _('Alle Wochentage im Stundenplan anzeigen.') ?>
        </label>
        <label>
            <input type="radio" name="weekdays" value="5"
                <?= $schedule_settings['weekdays'] === 5 ? 'checked' : '' ?>>
            <?= _('Nur Montag bis Freitag im Stundenplan anzeigen.') ?>
        </label>
    </fieldset>
    <fieldset>
        <legend><?= _('Wochentage') ?></legend>
        <section class="hgroup">
            <? for ($i = 1; $i < 8; $i++) : ?>
                <label>
                    <input type="checkbox" name="visible_days[]" value="<?= $i ?>"
                        <?= in_array($i, $schedule_settings['visible_days']) ? 'checked' : '' ?>>
                    <?= getWeekday($i, false) ?>
                </label>
            <? endfor ?>
        </section>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::createAccept(_('Speichern')) ?>
        <?= \Studip\Button::createCancel(_('Abbrechen')) ?>
    </div>
</form>


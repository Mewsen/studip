<?php
/**
 * @var CronjobSchedule $schedule
 * @var Admin_Cronjobs_SchedulesController $controller
 */
?>
<dl class="cronjob">
    <dt><?= _('Titel') ?></dt>
    <dd><?= htmlReady($schedule->title) ?></dd>

<? if ($schedule->description): ?>
    <dt><?= _('Beschreibung') ?></dt>
    <dd><?= htmlReady($schedule->description) ?></dd>
<? endif; ?>

    <dt><?= _('Aktiv') ?></dt>
    <dd><?= $schedule->active ? _('Ja') : _('Nein') ?></dd>

<? if (count($schedule->parameters) > 0): ?>
    <dt><?= _('Parameter') ?></dt>
    <dd>
        <ul>
        <? foreach ($schedule->parameters as $key => $value): ?>
            <li><?= htmlReady($key) ?>: <?= htmlReady($value) ?></li>
        <? endforeach; ?>
        </ul>
    </dd>
<? endif; ?>

    <dt><?= _('Aufgabe') ?></dt>
    <dd><?= htmlReady($schedule->task->name) ?></dd>

    <dt><?= _('Ausführungsrhytmus') ?></dt>
    <dd>
        <?= $this->render_partial('admin/cronjobs/schedules/periodic-schedule', $schedule->toArray()) ?>
    </dd>

    <dt><?= _('Ausführungen') ?></dt>
    <dd><?= number_format($schedule->execution_count, 0, ',', '.') ?></dd>

    <? if ($schedule->active): ?>
        <dt><?= _('Nächste Ausführung') ?></dt>
        <dd><?= date('d.m.Y H:i:s', $schedule->next_execution) ?></dd>
    <? endif; ?>

    <? if ($schedule->execution_count > 0): ?>
        <dt><?= _('Letzte Ausführung') ?></dt>
        <dd><?= $schedule->last_execution ? date('d.m.Y H:i:s', $schedule->last_execution) : _('nie') ?></dd>

        <dt><?= _('Letztes Ergebnis') ?></dt>
        <dd><code><?= htmlReady($schedule->last_result) ?></code></dd>
    <? endif; ?>
</dl>

<div data-dialog-button>
    <?= Studip\LinkButton::create(
        _('Log anzeigen'),
        $controller->url_for('admin/cronjobs/logs/schedule', $schedule)
    ) ?>
    <?= Studip\LinkButton::create(
        _('Cronjob bearbeiten'),
        $controller->editURL($schedule)
    ) ?>
</div>

<table class="default">
    <colgroup>
        <col style="width: 15%">
        <col style="width: 45%">
        <col>
    </colgroup>
    <caption>
        <?= sprintf(_('Veranstaltungen mit regelmäßigen Zeiten am %s, %s Uhr'), htmlReady($day), htmlReady($timespan)) ?>
    </caption>
    <thead>
    <tr>
        <th><?= _('Nummer') ?></th>
        <th><?= _('Name') ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($courses as $course) : ?>
        <tr>
            <td><?= htmlReady($course->veranstaltungsnummer) ?></td>
            <td>
                <a href="<?= URLHelper::getLink('dispatch.php/course/details/', ['sem_id' => $course->id]) ?>">
                    <?= Icon::create('link-intern') ?>
                    <?= htmlReady($course->name) ?>
                </a>
            </td>
            <td class="schedule-adminbind">
                <? $cycles = CalendarScheduleModel::getSeminarCycleId($course->id, $start, $end, $day) ?>

                <? foreach ($cycles as $cycle) : ?>
                    <span><?= $cycle->toString() ?></span>

                    <? $visible = CalendarScheduleModel::isSeminarVisible($course->id, $cycle->getMetadateId()) ?>

                    <?= Studip\LinkButton::create(
                    _('Ausblenden'),
                    $controller->url_for('calendar/schedule/adminbind/' . $course->id . '/' . $cycle->getMetadateId() . '/0'),
                    [
                        'id' => $course->id . '_' . $cycle->getMetadateId() . '_hide',
                        'onclick' => "STUDIP.Schedule.instSemUnbind('" . $course->id . "','" . $cycle->getMetadateId() . "'); return false;",
                        'style' => ($visible ? '' : 'display: none')
                    ]) ?>

                    <?= Studip\LinkButton::create(
                    _('Einblenden'),
                    $controller->url_for('calendar/schedule/adminbind/' . $course->id . '/' . $cycle->getMetadateId() . '/1'),
                    [
                        'id' => $course->id . '_' . $cycle->getMetadateId() . '_show',
                        'onclick' => "STUDIP.Schedule.instSemBind('" . $course->id . "','" . $cycle->getMetadateId() . "'); return false;",
                        'style' => ($visible ? 'display: none' : '')
                    ]) ?>
                    <br>
                <? endforeach ?>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>
<br>

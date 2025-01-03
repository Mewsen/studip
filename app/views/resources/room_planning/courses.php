<table class="default">
    <thead>
        <tr>
            <th><?= _('Veranstaltungen in diesem Raum') ?></th>
        </tr>
    </thead>
    <tbody>
    <? foreach ($courses as $course): ?>
        <tr>
            <td>
                <a href="<?= URLHelper::getURL('dispatch.php/course/dates/current_day_dates', [
                    'cid' => $course['Seminar_id'],
                    'resource_id' => $resourceId
                ]) ?>">
                    <?= htmlReady($course['Name']) ?>
                </a>
            </td>
        </tr>
    <? endforeach; ?>
    <? if (count($courses) === 0) : ?>
        <tr>
            <td>
                <?= _('Es sind noch keine Veranstaltungen vorhanden.') ?>
            </td>
        </tr>
    <? endif ?>
    </tbody>
</table>

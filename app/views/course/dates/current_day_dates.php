<form class="default" action="<?= $controller->current_day_dates() ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <table class="dates default sortable-table" data-sortlist="[[0, 0]]" data-table-id="<?= htmlReady($course->id) ?>">
        <thead>
            <tr>
                <th><?= _('Zeit') ?></th>
                <th data-sort="text"><?= _('Raum') ?></th>
                <th data-sort="htmldata"><?= _('Anzahl der Teilnehmenden') ?></th>
            </tr>
        </thead>
        <tbody>
        <? foreach ($dates as $date): ?>
            <tr>
                <td class="date_name">
                    <?= Icon::create('date')->asImg(Icon::SIZE_INLINE, ['class' => 'text-bottom']) ?>
                    <?= htmlReady($date->getFullName(CourseDate::FORMAT_VERBOSE)) ?>
                </td>
                <td>
                    <? $rooms = $date->getRooms(); ?>
                    <? if ($rooms): ?>
                        <? foreach ($rooms as $room) : ?>
                            <a href="<?= $room->getActionLink('show') ?>" data-dialog>
                                <?= htmlReady($room->name) ?>
                            </a>
                        <? endforeach ?>
                    <? else: ?>
                        <?= htmlReady($date->raum) ?>
                    <? endif ?>
                </td>
                <td data-sort-value="<?= htmlReady($date->number_of_participants) ?>">
                    <input type="hidden" name="termin_id[]" value="<?= htmlReady($date->termin_id) ?>">
                    <input type="number" min="0" name="number_of_participants[]" value="<?= htmlReady($date->number_of_participants) ?>">
                </td>
            </tr>
        <? endforeach; ?>
        <?if (count($dates) === 0) : ?>
            <tr>
                <td colspan="3">
                    <?= _('Es sind noch keine laufenden Termine vorhanden.') ?>
                </td>
            </tr>
        <? endif ?>
        </tbody>
    </table>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern')) ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->indexURL()) ?>
    </footer>
</form>


<?php
/**
 * @var Admin_HolidaysController $controller
 * @var array<int, array{name: string, col: int}> $holidays
 * @var int[] $customized
 */
?>
<form action="<?= $controller->store_holidays() ?>" method="post" class="default">
    <?= CSRFProtection::tokenTag() ?>

    <table class="default">
        <caption><?= _('Feiertage') ?></caption>
        <colgroup>
            <col>
            <col style="width: 20%">
        </colgroup>
        <thead>
        <tr>
            <th><?= _('Feiertag') ?></th>
            <th style="text-align: center">
                <?= _('Als "gesetzlich" festlegen') ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($holidays as $id => $holiday): ?>
            <tr>
                <td>
                    <label for="holiday-<?= htmlReady($id) ?>" class="undecorated">
                        <?= htmlReady($holiday['name']) ?>
                    </label>
                </td>
                <td style="text-align: center">
                    <input type="checkbox"
                           id="holiday-<?= htmlReady($id) ?>"
                           name="holidays[]"
                           value="<?= htmlReady($id) ?>"
                           <? if ($holiday['col'] === Holidays::WEIGHT_PUBLIC_HOLIDAY || in_array($id, $customized)) echo 'checked'; ?>
                           <? if ($holiday['col'] === Holidays::WEIGHT_PUBLIC_HOLIDAY) echo 'disabled'; ?>>
                </td>
            </tr>
        <? endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <?= Studip\Button::createAccept(_('Speichern')) ?>
                </td>
            </tr>
        </tfoot>
    </table>
</form>

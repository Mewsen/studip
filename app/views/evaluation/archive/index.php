<?php
/**
 * @var Evaluation_ArchiveController $controller
 */

use Studip\Button;

?>
<form method="post">
    <?= CSRFProtection::tokenTag() ?>
    <table class="default sortable-table" id="evaluation_table">
        <caption><?= _('Archivierte Evaluationen') ?></caption>
        <thead>
        <tr>
            <th style="width: 20px">
                <input type="checkbox"
                       data-proxyfor="#evaluation_table > tbody input[type=checkbox]"
                       data-activates="#evaluation_table tfoot button">
            </th>
            <th data-sort="text"><?= _('Titel') ?></th>
            <th data-sort="digit"><?= _('Datum') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($controller->evaluations)) : ?>
            <?php foreach ($controller->evaluations as $evaluation) : ?>
                <tr>
                    <td>
                        <input type="checkbox" name="q[]" value="<?= htmlReady($evaluation->id) ?>">
                    </td>
                    <td><?= htmlReady($evaluation->title) ?></td>
                    <td data-text="<?= (int) $evaluation->chdate?>">
                        <?= date('d.m.Y H:i', $evaluation->chdate) ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php else : ?>
            <tr>
                <td colspan="3" style="text-align: center">
                    <?= _('Es stehen keine Evaluationen zur Verfügung.') ?>
                </td>
            </tr>
        <? endif ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3">
                <?= Button::create(_("Löschen"), "bulkdelete", [
                    'formaction' => $controller->bulk('delete'),
                    'data-confirm' => _("Wirklich löschen?")
                ]) ?>
                <?= Button::create(_("Exportieren"), "bulkexport", [
                    'formaction' => $controller->bulk('export')
                ]) ?>
            </td>
        </tr>
        </tfoot>
    </table>
</form>

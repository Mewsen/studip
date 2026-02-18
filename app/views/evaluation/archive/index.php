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
            <th data-sort="text"><?= _('Veranstaltung') ?></th>
            <th data-sort="digit"><?= _('Start') ?></th>
            <th data-sort="digit"><?= _('Ende') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($controller->eval_assignments)) : ?>
            <?php foreach ($controller->eval_assignments as $assignment) : ?>
                <tr>
                    <td>
                        <input type="checkbox" name="q[]" value="<?= htmlReady($assignment->id) ?>">
                    <td>
                        <?= htmlReady($assignment->questionnaire->title) /*TODO link to statistic*/ ?>
                    </td>
                    </td>
                    <td>
                        <?= htmlReady($assignment->course_metadata) /*TODO course name*/ ?>
                    </td>
                    <td data-text="<?= (int) $assignment->startdate?>">
                        <?= date('d.m.Y H:i', $assignment->startdate) ?>
                    </td>
                    <td data-text="<?= (int) $assignment->stopdate?>">
                        <?= date('d.m.Y H:i', $assignment->stopdate) ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php else : ?>
            <tr>
                <td colspan="5" style="text-align: center">
                    <?= _('Es stehen keine Evaluationen zur Verfügung.') ?>
                </td>
            </tr>
        <? endif ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="5">
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

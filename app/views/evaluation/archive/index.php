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
            <th style="width: 20px" scope="col">
                <input type="checkbox"
                       data-proxyfor="#evaluation_table > tbody input[type=checkbox]"
                       data-activates="#evaluation_table tfoot button">
            </th>
            <th data-sort="text" scope="col"><?= _('Titel') ?></th>
            <th data-sort="text" scope="col"><?= _('Veranstaltung') ?></th>
            <th data-sort="digit" scope="col"><?= _('Nr.') ?></th>
            <th data-sort="text" scope="col"><?= _('Einrichtung') ?></th>
            <th data-sort="text" scope="col"><?= _('Studiengang') ?></th>
            <th data-sort="text" scope="col"><?= _('Evaluierte') ?></th>
            <th data-sort="digit" scope="col"><?= _('Start') ?></th>
            <th data-sort="digit" scope="col"><?= _('Ende') ?></th>
            <th data-sort="digit" scope="col"><?= _('Rücklauf') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($controller->evaluations)) : ?>
            <?php foreach ($controller->evaluations as $evaluation) : ?>
                <tr>
                    <td>
                        <input type="checkbox" name="evaluations[]" value="<?= htmlReady($evaluation->getId()) ?>">
                    <td>
                        <a href="<?= $controller->link_for('questionnaire/evaluate/' . $evaluation->getId()) ?>"
                            data-dialog
                        >
                            <?= htmlReady($evaluation->title ?? '') ?>
                        </a>
                    </td>
                    </td>
                    <?php $assignment = $evaluation->eval_assignment ?>
                    <td>
                        <?= htmlReady($assignment->course_metadata['course_title'] ?? '') ?>
                    </td>
                    <td>
                        <?= htmlReady($assignment->course_metadata['sem_nr']) ?>
                    </td>
                    <td>
                        <?= Institute::find($assignment->institute_id) ?>
                    </td>
                    <td>
                        <?php if (isset($assignment->course_metadata['study_programs'])) : ?>
                            <?php foreach ($assignment->course_metadata['study_programs'] as $program) : ?>
                                <?= htmlReady($program) ?><br/>
                            <?php endforeach ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if (isset($assignment->course_metadata['evaluated_persons'])) : ?>
                            <?php foreach ($assignment->course_metadata['evaluated_persons'] as $person) : ?>
                                <?= htmlReady($person) ?><br/>
                            <?php endforeach ?>
                        <?php endif ?>
                    </td>
                    <td data-text="<?= $evaluation->startdate?>">
                        <?= date('d.m.Y H:i', $evaluation->startdate) ?>
                    </td>
                    <td data-text="<?= $evaluation->stopdate?>">
                        <?= date('d.m.Y H:i', $evaluation->stopdate) ?>
                    </td>
                    <td>
                        <?= $evaluation->countAnswers() ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php else : ?>
            <tr>
                <td colspan="10" style="text-align: center">
                    <?= _('Es stehen keine Evaluationen zur Verfügung.') ?>
                </td>
            </tr>
        <? endif ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10">
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

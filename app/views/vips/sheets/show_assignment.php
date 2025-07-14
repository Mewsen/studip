<?php
/**
 * @var Vips_SheetsController $controller
 * @var VipsAssignment $assignment
 * @var int $remaining_time
 * @var string $exam_terms
 * @var int $user_end_time
 * @var string $preview_exam_terms
 * @var bool $needs_code
 * @var string $access_code
 * @var string $solver_id
 */
?>
<? if ($assignment->type === 'exam' && isset($assignment_attempt) && $remaining_time > 0) : ?>
    <div id="exam_timer" data-time="<?= $remaining_time ?>">
        <?= _('Restzeit') ?>: <span class="time"><?= round($remaining_time / 60) ?></span> min
    </div>
<? endif ?>

<?= $contentbar->render() ?>

<h1 class="width-1200">
    <?= htmlReady($assignment->test->title) ?>
</h1>

<div class="width-1200" style="margin: 10px 0;">
    <?= formatReady($assignment->test->description) ?>
</div>

<? if ($assignment->isUnlimited()) : ?>
    <?= _('Start') ?>:
    <?= date('d.m.Y, H:i', $assignment->start) ?>
<? else: ?>
    <?= _('Zeitraum') ?>:
    <?= date('d.m.Y, H:i', $assignment->start) ?> &ndash;
    <?= date('d.m.Y, H:i', $assignment->end) ?>
<? endif ?>

<? if ($assignment->type === 'exam'): ?>
    <p style="font-weight: bold;">
        <? if ($exam_terms): ?>
            <?= sprintf(_('Bearbeitungszeit: %d Minuten.'), round($remaining_time / 60)) ?>
        <? elseif ($remaining_time > 0): ?>
            <?= sprintf(_('Sie haben noch %d Minuten Zeit.'), round($remaining_time / 60)) ?>
        <? else: ?>
            <?= _('Ihre Bearbeitungszeit ist abgelaufen.') ?>
        <? endif ?>
    </p>
<? elseif ($user_end_time && $remaining_time <= 0): ?>
    <p style="font-weight: bold;">
        <?= _('Die Bearbeitung ist bereits abgeschlossen.') ?>
    </p>
<? endif ?>

<? if ($preview_exam_terms): ?>
    <form class="default width-1200" style="margin-bottom: 1.5ex;">
        <input id="options-toggle" class="options-toggle" type="checkbox" value="on">
        <a class="caption" href="#" role="button" data-toggles="#options-toggle" aria-controls="options-panel" aria-expanded="false">
            <?= Icon::create('arr_1down')->asSvg(['class' => 'toggle-open']) ?>
            <?= Icon::create('arr_1right')->asSvg(['class' => 'toggle-closed']) ?>
            <?= _('Teilnahmebedingungen') ?>
        </a>

        <div class="toggle-box" id="options-panel">
            <div class="exercise_hint" style="display: block;">
                <?= formatReady($preview_exam_terms) ?>

                <label>
                    <input type="checkbox" value="1" disabled>
                    <?= _('Ich bestätige die vorstehenden Bedingungen zur Teilnahme an der Klausur') ?>
                </label>
            </div>
        </div>
    </form>
<? endif ?>

<? if ($exam_terms || $needs_code): ?>
    <form class="default width-1200" action="<?= $controller->link_for('vips/sheets/begin_assignment') ?>" method="POST">
        <?= CSRFProtection::tokenTag() ?>
        <input type="hidden" name="assignment_id" value="<?= $assignment->id ?>">

        <div class="exercise_hint" style="display: block;">
            <? if ($exam_terms): ?>
                <?= formatReady($exam_terms) ?>

                <label>
                    <input type="checkbox" name="terms_accepted" value="1" required>
                    <?= _('Ich bestätige die vorstehenden Bedingungen zur Teilnahme an der Klausur') ?>
                </label>
            <? endif ?>

            <? if ($needs_code): ?>
                <label>
                    <?= _('Es ist ein Zugangscode für den Zugriff auf die Klausur erforderlich:') ?>
                    <input type="text" name="access_code" value="<?= htmlReady($access_code) ?>" required>
                </label>
            <? endif ?>

            <?= Studip\Button::createAccept(_('Klausur starten'), 'begin_assignment') ?>
        </div>
    </form>
<? else: ?>
    <? if (count($assignment->test->exercise_refs)): ?>
        <table class="default dynamic_list width-1200">
            <thead>
                <tr>
                    <th style="width: 2em;">
                    </th>
                    <th style="width: 50%;">
                        <?= _('Aufgaben') ?>
                    </th>
                    <th style="width: 15%;">
                        <?= _('Abgabedatum') ?>
                    </th>
                    <th style="width: 15%;">
                        <?= _('Teilnehmende') ?>
                    </th>
                    <th style="width: 10%; text-align: center;">
                        <?= _('Bearbeitet') ?>
                    </th>
                    <th style="width: 10%; text-align: center;">
                        <?= _('Max. Punkte') ?>
                    </th>
                </tr>
            </thead>

            <tbody>
                <? foreach ($assignment->getExerciseRefs($solver_id) as $exercise_ref): ?>
                    <? $exercise = $exercise_ref->exercise ?>
                    <? $solution = $assignment->getSolution($solver_id, $exercise->id) ?>
                    <tr>
                        <td class="dynamic_counter" style="text-align: right;">
                        </td>
                        <td>
                            <a href="<?= $controller->link_for('vips/sheets/show_exercise', ['assignment_id' => $assignment->id, 'exercise_id' => $exercise->id, 'solver_id' => $solver_id]) ?>">
                                <?= htmlReady($exercise->title) ?>
                            </a>
                        </td>
                        <td>
                            <? if ($solution): ?>
                                <?= date('d.m.Y, H:i', $solution->mkdate) ?>
                            <? endif ?>
                        </td>
                        <td>
                            <? if ($solution): ?>
                                <?= htmlReady(get_fullname($solution->user_id, 'no_title')) ?>
                            <? endif ?>
                        </td>
                        <td style="text-align: center;">
                            <? if ($solution): ?>
                                <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asSvg(['title' => _('ja')]) ?>
                            <? else : ?>
                                <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asSvg(['title' => _('nein')]) ?>
                            <? endif ?>
                        </td>
                        <td style="text-align: center;">
                            <?= sprintf('%g', $exercise_ref->points) ?>
                        </td>
                    </tr>
                <? endforeach ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="5"></td>
                    <td style="text-align: center;">
                        <?= sprintf('%g', $assignment->test->getTotalPoints()) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    <? else : ?>
        <?= MessageBox::info(_('Keine Aufgaben gefunden.')) ?>
    <? endif ?>
<? endif ?>

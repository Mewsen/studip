<?php
/**
 * @var VipsAssignment $assignment
 * @var string $user_id
 * @var int $released
 * @var Vips_SolutionsController $controller
 * @var string $feedback
 */
?>
<h1 class="width-1200">
    <?= htmlReady($assignment->test->title) ?>
</h1>

<div class="width-1200" style="margin: 10px 0;">
    <?= formatReady($assignment->test->description) ?>
</div>

<table class="default dynamic_list collapsable width-1200">
    <caption>
        <?= _('Ergebnisse des Aufgabenblatts') ?>
    </caption>

    <thead>
        <tr>
            <th style="width: 2em;">
            </th>

            <th style="width: 60%;">
                <?= _('Aufgaben') ?>
            </th>

            <th style="width: 10%; text-align: center;">
                <?= _('Bearbeitet') ?>
            </th>

            <th style="width: 15%; text-align: center;">
                <?= _('Erreichte Punkte') ?>
            </th>

            <th style="width: 15%; text-align: center;">
                <?= _('Max. Punkte') ?>
            </th>
        </tr>
    </thead>

    <? foreach ($assignment->getExerciseRefs($user_id) as $exercise_ref) : ?>
        <? $solution = $assignment->getSolution($user_id, $exercise_ref->task_id); ?>
        <tbody class="collapsed">
            <tr class="header-row">
                <td class="dynamic_counter" style="text-align: right;">
                </td>
                <td>
                    <? if ($released >= VipsAssignment::RELEASE_STATUS_CORRECTIONS): ?>
                        <a href="<?= $controller->view_solution(['assignment_id' => $assignment->id, 'exercise_id' => $exercise_ref->task_id]) ?>">
                            <?= htmlReady($exercise_ref->exercise->title) ?>
                        </a>
                    <? elseif ($released == VipsAssignment::RELEASE_STATUS_COMMENTS && $solution && $solution->hasFeedback()) : ?>
                        <a class="toggler" href="#">
                            <?= htmlReady($exercise_ref->exercise->title) ?>
                        <a>
                    <? else: ?>
                        <?= htmlReady($exercise_ref->exercise->title) ?>
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
                    <?= sprintf('%g', $solution ? $solution->points : 0) ?>
                </td>
                <td style="text-align: center;">
                    <?= sprintf('%g', $exercise_ref->points) ?>
                </td>
            </tr>

            <? if ($released == VipsAssignment::RELEASE_STATUS_COMMENTS && $solution && $solution->hasFeedback()): ?>
                <tr>
                    <td>
                    </td>
                    <td colspan="4">
                        <?= formatReady($solution->feedback) ?>
                        <?= $this->render_partial('vips/solutions/feedback_files', compact('solution')) ?>
                    </td>
                </tr>
            <? endif ?>
        </tbody>
    <? endforeach ?>

    <tfoot>
        <tr style="font-weight: bold;">
            <td>
            </td>

            <td colspan="2" style="padding: 5px;">
                <?= _('Gesamtpunktzahl') ?>
            </td>

            <td style="text-align: center;">
                <?= sprintf('%g', $assignment->getUserPoints($user_id)) ?>
            </td>

            <td style="text-align: center;">
                <?= sprintf('%g', $assignment->test->getTotalPoints()) ?>
            </td>
        </tr>
    </tfoot>
</table>

<? if ($released >= VipsAssignment::RELEASE_STATUS_COMMENTS && $feedback != ''): ?>
    <div class="width-1200">
        <h3>
            <?= _('Kommentar zur Bewertung') ?>
        </h3>

        <?= formatReady($feedback) ?>
    </div>
<? endif ?>

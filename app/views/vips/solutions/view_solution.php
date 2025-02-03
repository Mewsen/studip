<?php
/**
 * @var Vips_SolutionsController $controller
 * @var VipsAssignment $assignment
 * @var Exercise $exercise
 * @var VipsSolution $solution
 * @var float $max_points
 */
?>
<div class="breadcrumb width-1200">
    <div style="display: inline-block; width: 20%;">
        <? if (isset($prev_exercise_id)) : ?>
            <a href="<?= $controller->view_solution(['assignment_id' => $assignment->id, 'exercise_id' => $prev_exercise_id]) ?>">
                <?= Icon::create('arr_1left') ?>
                <?= _('Vorige Aufgabe') ?>
            </a>
        <? endif ?>
    </div><!--
 --><div style="display: inline-block; text-align: center; width: 60%;">
        <a href="<?= $controller->student_assignment_solutions(['assignment_id' => $assignment->id]) ?>">
            &bull; <?= htmlReady($assignment->test->title) ?> &bull;
        </a>
    </div><!--
 --><div style="display: inline-block; text-align: right; width: 20%;">
        <? if (isset($next_exercise_id)) : ?>
            <a href="<?= $controller->view_solution(['assignment_id' => $assignment->id, 'exercise_id' => $next_exercise_id]) ?>">
                <?= _('Nächste Aufgabe') ?>
                <?= Icon::create('arr_1right') ?>
            </a>
        <? endif ?>
    </div>
</div>

<form class="default width-1200">
    <?= $this->render_partial('vips/exercises/correct_exercise') ?>

    <fieldset>
        <legend>
            <?= sprintf(_('Bewertung der Aufgabe &bdquo;%s&ldquo;'), htmlReady($exercise->title)) ?>
            <div style="float: right;">
                <? if ($solution->state): ?>
                    <?= _('Korrigiert') ?>
                <? elseif ($solution->id): ?>
                    <?= _('Unkorrigiert') ?>
                <? else: ?>
                    <?= _('Nicht abgegeben') ?>
                <? endif ?>
            </div>
        </legend>

        <? if ($solution->feedback != '') : ?>
            <div class="label-text">
                <?= _('Anmerkung des Korrektors') ?>

                <? if (isset($solution->grader_id) && $assignment->type === 'practice') : ?>
                    <? $corrector_full_name = get_fullname($solution->grader_id); ?>
                    (<a href="<?= URLHelper::getLink('dispatch.php/messages/write', ['rec_uname' => get_username($solution->grader_id)]) ?>"
                        title="<?= htmlReady(sprintf(_('Nachricht an „%s“ schreiben'), $corrector_full_name)) ?>" data-dialog><?= htmlReady($corrector_full_name) ?></a>)
                <? endif ?>
            </div>

            <div class="vips_output">
                <?= formatReady($solution->feedback) ?>
            </div>
        <? endif ?>

        <?= $this->render_partial('vips/solutions/feedback_files_table') ?>

        <div class="description">
            <?= sprintf(_('Erreichte Punkte: %g von %g'), $solution->points, $max_points) ?>
        </div>
    </fieldset>
</form>

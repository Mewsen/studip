<?php
/**
 * @var int $exercise_position
 * @var Exercise $exercise
 * @var float|int $max_points
 * @var VipsSolution $solution
 */
?>
<fieldset>
    <legend>
        <?= $exercise_position ?>.
        <?= htmlReady($exercise->title) ?>
        <div style="float: right;">
            <? if ($max_points == (int) $max_points): ?>
                <?= sprintf(ngettext('%d Punkt', '%d Punkte', $max_points), $max_points) ?>
            <? else: ?>
                <?= sprintf(_('%g Punkte'), $max_points) ?>
            <? endif ?>
        </div>
    </legend>

    <div class="description">
        <?= formatReady($exercise->description) ?>
    </div>

    <?= $this->render_partial('vips/exercises/show_exercise_hint') ?>
    <?= $this->render_partial('vips/exercises/show_exercise_files') ?>

    <?= $this->render_partial($exercise->getCorrectionTemplate($solution)) ?>

    <? if (!empty($exercise->options['comment']) && $solution->student_comment != '') : ?>
        <div class="label-text">
            <?= _('Bemerkungen zur Lösung') ?>
        </div>
        <div class="vips_output">
            <?= htmlReady($solution->student_comment, true, true) ?>
        </div>
    <? endif ?>
</fieldset>

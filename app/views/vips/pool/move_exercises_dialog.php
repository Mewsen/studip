<?php
/**
 * @var Vips_PoolController $controller
 * @var int[] $exercise_ids
 * @var Course[] $courses
 */
?>
<form class="default" action="<?= $controller->link_for('vips/pool/move_exercises') ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <? foreach ($exercise_ids as $exercise_id => $assignment_id): ?>
        <input type="hidden" name="exercise_ids[<?= $exercise_id ?>]" value="<?= $assignment_id ?>">
    <? endforeach ?>

    <label>
        <?= _('Aufgabenblatt auswählen') ?>

        <select name="assignment_id" class="vips_nested_select">
            <? $assignments = VipsAssignment::findByRangeId($GLOBALS['user']->id) ?>
            <? usort($assignments, fn($a, $b) => strcoll($a->test->title, $b->test->title)) ?>
            <? if ($assignments): ?>
                <optgroup label="<?= _('Persönliche Aufgabensammlung') ?>">
                    <? foreach ($assignments as $assignment): ?>
                        <option value="<?= $assignment->id ?>" <?= $assignment->id == $assignment_id ? 'selected' : '' ?>>
                            <?= htmlReady($assignment->test->title) ?>
                        </option>
                    <? endforeach ?>
                </optgroup>
            <? endif ?>

            <? foreach ($courses as $course): ?>
                <? $assignments = VipsAssignment::findByRangeId($course->id) ?>
                <? $assignments = array_filter($assignments, fn($a) => !$a->isLocked()) ?>
                <? usort($assignments, fn($a, $b) => strcoll($a->test->title, $b->test->title)) ?>
                <? if ($assignments): ?>
                    <optgroup label="<?= htmlReady($course->name . ' (' . $course->start_semester->name . ')') ?>">
                        <? foreach ($assignments as $assignment): ?>
                            <option value="<?= $assignment->id ?>" <?= $assignment->id == $assignment_id ? 'selected' : '' ?>>
                                <?= htmlReady($assignment->test->title) ?>
                            </option>
                        <? endforeach ?>
                    </optgroup>
                <? endif ?>
            <? endforeach ?>
        </select>
    </label>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Verschieben'), 'move') ?>
    </footer>
</form>

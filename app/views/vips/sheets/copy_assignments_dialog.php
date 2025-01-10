<?php
/**
 * @var Vips_SheetsController $controller
 * @var int[] $assignment_ids
 * @var Course[] $courses
 * @var string $course_id
 */
?>
<form class="default" action="<?= $controller->copy_assignments() ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <? foreach ($assignment_ids as $assignment_id): ?>
        <input type="hidden" name="assignment_ids[]" value="<?= $assignment_id ?>">
    <? endforeach ?>

    <label>
        <?= _('Ziel auswählen') ?>

        <select name="course_id" class="vips_nested_select">
            <option value="">
                <?= _('Persönliche Aufgabensammlung') ?>
            </option>

            <? foreach ($courses as $course): ?>
                <option value="<?= $course->id ?>" <?= $course->id == $course_id ? 'selected' : '' ?>>
                    <?= htmlReady($course->name) ?> (<?= htmlReady($course->start_semester->name) ?>)
                </option>
            <? endforeach ?>
        </select>
    </label>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Kopieren'), 'copy') ?>
    </footer>
</form>

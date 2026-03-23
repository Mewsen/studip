<?php
/**
 * @var Course $course
 */
?>
<label>
    <?php $message_new = sprintf(_('Veranstaltung %s auswählen'), htmlReady($course->name)) ?>
    <?php $message_existing = sprintf(_('%s hat im gewählten Semester bereits eine zugewiesene Evaluation'),
        htmlReady($course->name)) ?>
    <input name="evaluation_courses[]" type="checkbox" value="<?= htmlReady($course->id) ?>"
            <?= isset($GLOBALS['user']->cfg->MY_COURSES_SELECTED_CYCLE) &&
                QuestionnaireEvalAssignment::findBySQL('`course_id` = ? AND `semester_id` = ?',
                [$course->getId(), $GLOBALS['user']->cfg->MY_COURSES_SELECTED_CYCLE]) ?
                sprintf('disabled title="%s" aria-label="%1$s"', $message_existing)
                : sprintf('title="%s" aria-label="%1$s"', $message_new)
            ?>
    >
</label>

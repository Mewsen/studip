<?php
/**
 * @var Course $course
 */
?>
<label>
    <input name="evaluation_courses[]" type="checkbox" value="<?= htmlReady($course->id) ?>"
           aria-label="<?= htmlReady(_('Evaluation zuweisen')) ?>">
</label>

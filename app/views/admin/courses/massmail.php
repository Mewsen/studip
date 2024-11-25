<?php
/**
 * @var Course $course
 */
?>
<label>
    <input name="courses[]" type="checkbox" value="<?= htmlReady($course->id) ?>"
           aria-label="<?= htmlReady(sprintf(_('Nachricht an Teilnehmende der Veranstaltung %s senden'),
               $course->getFullName())) ?>">
</label>

<?php
/**
 * @var ?Semester $semester The selected semester.
 * @var \Studip\Fullcalendar $fullcalendar The fullcalendar instance to be rendered.
 */
?>
<? if ($semester) : ?>
    <h2>
        <?= studip_interpolate(
            _('Mein Stundenplan im %{semester}'),
            ['semester' => $semester->name]
        ) ?>
    </h2>
<? endif ?>
<?= $fullcalendar ?>

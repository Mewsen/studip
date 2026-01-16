<?php
/**
 * @var ?Semester $semester The selected semester.
 * @var \Studip\Fullcalendar $fullcalendar The fullcalendar instance to be rendered.
 */
?>
<? if ($semester) : ?>
    <h1 class="print-hidden">
        <?= studip_interpolate(
            _('Stundenplan %{semester}'),
            ['semester' => $semester->name]
        ) ?>
    </h1>
<? endif ?>
<?= $fullcalendar ?>

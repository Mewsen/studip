Eine Anfrage <?= $request->course instanceof Course
               ? 'für die Veranstaltung [' .$request->course->getFullName('number-name') . ']' . $request->course->getItemURL()
               : '' ?> wurde bearbeitet.

<? if ($request->course): ?>

Lehrende Person(en): <?= $lecturer_names ?>

<? endif ?>

Angefragter Raum: <?= $request->resource->name ?>


Gebuchte Räume: <?= $booked_rooms ?>


Art der Anfrage: <?= $request->getTypeString() ?>


Die folgenden Zeiträume wurden gebucht<?= $request->course->isToolActive('CoreSchedule')
               ? ' und im [Ablaufplan]' . str_replace( 'details/index', 'dates', $request->course->getItemURL()) . ' eingetragen'
               : '' ?>:
<? foreach ($metadates as $metadate) : ?>

- <?= $metadate->toString('full') ?>
<? endforeach ?>
<? foreach ($single_dates as $date) : ?>

    <? if($date instanceof CourseDate) : ?>
        - <?= $date->getFullName() ?>
    <? else : ?>
        - <?= $date->toString('default') ?>
    <? endif ?>
<? endforeach ?>
<? foreach ($booked_time_intervals as $interval) : ?>

- <?= $interval ?>
<? endforeach ?>
<? if ($request->comment) : ?>


Kommentar zur Anfrage:

<?= $request->comment ?>
<? endif ?>
<? if ($request->reply_comment): ?>


Kommentar der Raumverwaltung:

<?= $request->reply_comment ?>
<? endif ?>

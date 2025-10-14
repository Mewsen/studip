<?php
/**
 * @var int $booking_type
 * @var Resource $resource
 * @var ?Course $booking_course
 * @var ?User $deleting_user
 * @var int $begin
 * @var int $end
 */
?>
<? if (in_array($booking_type, [0, 3])) : ?>
<? if ($resource instanceof Room): ?>
Ihre Buchung des Raumes <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
 von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? else : ?>
Ihre Buchung der Ressource <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
 von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? endif ?>
<? elseif ($booking_type == 1) : ?>
<? if ($resource instanceof Room): ?>
Ihre Reservierung des Raumes <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? else : ?>
Ihre Reservierung der Ressource <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? endif ?>
<? elseif ($booking_type == 2) : ?>
<? if ($resource instanceof Room): ?>
Ihre Sperrbuchung des Raumes <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? else : ?>
Ihre Sperrbuchung der Ressource <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? endif ?>
<? endif ?>

<? if ($booking_course instanceof Course): ?>
<? if(in_array($booking_type, [0, 3])) : ?>
Es handelte sich um eine Buchung für die Veranstaltung <?= $booking_course->getFullName() ?>.
<? elseif ($booking_type == 1) : ?>
Es handelte sich um eine Reservierung für die Veranstaltung <?= $booking_course->getFullName() ?>.
<? elseif ($booking_type == 2) : ?>
Es handelte sich um eine Sperrbuchung für die Veranstaltung <?= $booking_course->getFullName() ?>.
<? endif ?>
<? endif ?>

<? if ($deleting_user && !in_array($deleting_user->id, ['nobody', 'form'])) : ?>
Die Löschung wurde von <?= $deleting_user->getFullName() ?> vorgenommen.
<? endif ?>

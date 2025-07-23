<? if (in_array($booking_type, [ResourceBooking::TYPE_NORMAL, ResourceBooking::TYPE_PLANNED])) : ?>
<? if ($resource instanceof Room): ?>
Ihre Buchung des Raumes <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
 von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? else : ?>
Ihre Buchung der Ressource <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
 von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? endif ?>
<? elseif ($booking_type == ResourceBooking::TYPE_RESERVATION) : ?>
<? if ($resource instanceof Room): ?>
Ihre Reservierung des Raumes <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? else : ?>
Ihre Reservierung der Ressource <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? endif ?>
<? elseif ($booking_type == ResourceBooking::TYPE_LOCK) : ?>
<? if ($resource instanceof Room): ?>
Ihre Sperrbuchung des Raumes <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? else : ?>
Ihre Sperrbuchung der Ressource <?= $resource->name ?> am <?= date('d.m.Y', $begin) ?>
von <?= date('H:i', $begin) ?> bis <?= date('H:i', $end) ?> Uhr wurde gelöscht.
<? endif ?>
<? endif ?>

<? if ($booking_course instanceof Course): ?>
<? if(in_array($booking_type, [ResourceBooking::TYPE_NORMAL, ResourceBooking::TYPE_PLANNED])) : ?>
Es handelte sich um eine Buchung für die Veranstaltung <?= $booking_course->getFullName() ?>.
<? elseif ($booking_type == ResourceBooking::TYPE_RESERVATION) : ?>
Es handelte sich um eine Reservierung für die Veranstaltung <?= $booking_course->getFullName() ?>.
<? elseif ($booking_type == ResourceBooking::TYPE_LOCK) : ?>
Es handelte sich um eine Sperrbuchung für die Veranstaltung <?= $booking_course->getFullName() ?>.
<? endif ?>
<? endif ?>

<? if ($deleting_user instanceof User) : ?>
Die Löschung wurde von <?= $deleting_user->getFullName() ?> vorgenommen.
<? endif ?>

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
<? if (in_array($booking_type, [ResourceBooking::TYPE_NORMAL, ResourceBooking::TYPE_PLANNED])) : ?>
<? if ($resource instanceof Room): ?>
Your booking of the room <?= $resource->name ?> on <?= date('d.m.Y', $begin) ?>
 from <?= date('H:i', $begin) ?> to <?= date('H:i', $end) ?> has been deleted.
<? else: ?>
Your booking of the resource <?= $resource->name ?> on <?= date('d.m.Y', $begin) ?>
 from <?= date('H:i', $begin) ?> to <?= date('H:i', $end) ?> has been deleted.
<? endif ?>
<? elseif ($booking_type == ResourceBooking::TYPE_RESERVATION) : ?>
<? if ($resource instanceof Room): ?>
Your reservation of the room <?= $resource->name ?> on <?= date('d.m.Y', $begin) ?>
from <?= date('H:i', $begin) ?> to <?= date('H:i', $end) ?> has been deleted.
<? else: ?>
Your reservation of the resource <?= $resource->name ?> on <?= date('d.m.Y', $begin) ?>
from <?= date('H:i', $begin) ?> to <?= date('H:i', $end) ?> has been deleted.
<? endif ?>
<? elseif ($booking_type == ResourceBooking::TYPE_LOCK) : ?>
<? if ($resource instanceof Room): ?>
Your lock booking of the room <?= $resource->name ?> on <?= date('d.m.Y', $begin) ?>
from <?= date('H:i', $begin) ?> to <?= date('H:i', $end) ?> has been deleted.
<? else: ?>
Your lock booking of the resource <?= $resource->name ?> on <?= date('d.m.Y', $begin) ?>
from <?= date('H:i', $begin) ?> to <?= date('H:i', $end) ?> has been deleted.
<? endif ?>
<? endif ?>

<? if ($booking_course instanceof Course): ?>
<? if (in_array($booking_type, [ResourceBooking::TYPE_NORMAL, ResourceBooking::TYPE_PLANNED])) : ?>
The deleted booking belonged to course <?= $booking_course->getFullName() ?>.
<? elseif ($booking_type == ResourceBooking::TYPE_RESERVATION) : ?>
The deleted reservation belonged to course <?= $booking_course->getFullName() ?>.
<? elseif ($booking_type == ResourceBooking::TYPE_LOCK) : ?>
The deleted lock booking belonged to course <?= $booking_course->getFullName() ?>.
<? endif ?>
<? endif ?>

<? if ($deleting_user && !in_array($deleting_user->id, ['nobody', 'form'])) : ?>
The deletion has been made by <?= $deleting_user->getFullName() ?>.
<? endif ?>

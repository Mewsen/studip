<?php
/**
 * @var CourseDateList $collection
 * @var bool $with_room_names
 * @var bool $with_cancelled_dates
 *
 * @var SeminarCycleDate $regular_date
 * @var CourseDate $single_date
 * @var CourseExDate $cancelled_date
 */
?>
<? if (!$collection->isEmpty()) : ?>
    <ul>
        <? foreach ($collection->getRegularDates() as $regular_date) : ?>
            <li><?= $regular_date->toString('long-start') ?></li>
        <? endforeach ?>
        <? foreach ($collection->getSingleDates() as $single_date) : ?>
            <li><?= $single_date->getFullName($with_room_names ? 'long-include-room' : 'long') ?></li>
        <? endforeach ?>
        <? if ($with_cancelled_dates) : ?>
            <? foreach ($collection->getCancelledDates() as $cancelled_date) : ?>
                <li><?= $cancelled_date->getFullName() ?></li>
            <? endforeach ?>
        <? endif ?>
    </ul>
<? endif ?>

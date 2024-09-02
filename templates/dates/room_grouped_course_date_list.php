<?php
/**
 * @var CourseDateList[] $grouped_dates The grouped dates to be displayed.
 * @var bool $with_cancelled_dates Whether to output cancelled dates (true) or not (false).
 */
?>
<? foreach ($grouped_dates as $room_name => $grouped_date) : ?>
    <?
    $room = Resource::findOneBySQL("name = :name", ['name' => $room_name]);
    if ($room instanceof Resource) {
        $room = $room->getDerivedClassInstance();
    }
    ?>
    <? if ($room instanceof Room) : ?>
        <h4>
            <a href="<?= $room->getActionLink() ?>" data-dialog>
                <?= htmlReady($room->name) ?>
            </a>
        </h4>
    <? else : ?>
        <h4><?= htmlReady($room_name) ?></h4>
    <? endif ?>
    <?= $grouped_date->toHtml(false, $with_cancelled_dates) ?>
<? endforeach ?>

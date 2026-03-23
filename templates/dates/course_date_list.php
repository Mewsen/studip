<?php
/**
 * @var CourseDateList $collection
 * @var bool $with_room_names
 * @var bool $with_cancelled_dates
 * @var bool $as_collapsible_list
 *
 * @var SeminarCycleDate $regular_date
 * @var CourseDate $single_date
 * @var CourseExDate $cancelled_date
 */
?>
<?php
$html_attributes = HTMLAttributes::from([
    'class' => 'list-unstyled',
]);

if ($as_collapsible_list) {
    $html_attributes['class'] = 'collapsible-list';
    $html_attributes['data-label'] = _('Termine');
}
?>
<? if (!$collection->isEmpty()) : ?>
    <ul <?= $html_attributes ?>>
        <? foreach ($collection->getRegularDates() as $regular_date) : ?>
            <li>
                <?= $regular_date->toString($with_room_names ? 'long-start' : 'long-start-without-room', true) ?>
            </li>
        <? endforeach ?>
        <? foreach ($collection->getSingleDates() as $single_date) : ?>
            <li>
                <?= htmlReady($single_date->getFullName('long')) ?>
                <? if ($with_room_names): ?>
                    <? $rooms = $single_date->getRooms() ?>
                    <? if ($rooms): ?>
                        <? foreach ($rooms as $room): ?>
                            <a href="<?= $room->getActionLink() ?>" data-dialog>
                                <?= htmlReady($room->name) ?>
                            </a>
                        <? endforeach ?>
                    <? else: ?>
                        <?= htmlReady($single_date->raum) ?>
                    <? endif ?>
                <? endif ?>
            </li>
        <? endforeach ?>
        <? if ($with_cancelled_dates) : ?>
            <? foreach ($collection->getCancelledDates() as $cancelled_date) : ?>
                <li>
                    <?= htmlReady($cancelled_date->getFullName()) ?>
                </li>
            <? endforeach ?>
        <? endif ?>
    </ul>
<? endif ?>

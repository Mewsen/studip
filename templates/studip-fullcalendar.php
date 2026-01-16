<?php
/**
 * @var array $attributes HTML attributes for the surrounding element.
 * @var string $title The title of the calendar.
 * @var array $config The calendar configuration.
 * @var string $extraClasses Extra CSS class for the fullcalendar element.
 * @var string $dialogSize The default size of dialogs when opened out of the calendar.
 * @var array $actionUrls URLs for different actions that usually trigger dialogs to be shown.
 * @var bool $displayHolidays Whether to show holidays in the calendar (true) or not (false).
 * @var bool $displayVacations Whether to show vacations in the calendar (true) or not (false).
 * @var string $externalDroppableContainerId The ID of the element that contains external droppable events.
 * @var string $externalDroppableEventSelector The selector for external droppable events.
 * @var bool $eventColourPicker Whether to show an event colour picker (true) or not (false).
 */
?>
<section class="studip-fullcalendar" <?= arrayToHtmlAttributes($attributes) ?>>
    <?= \Studip\VueApp::create('StudipCalendar')->withProps(
        [
            'title'             => $title,
            'config'            => $config,
            'extraClasses'     => $extraClasses,
            'dialogSize'       => $dialogSize,
            'actionUrls'       => $actionUrls,
            'displayHolidays'  => $displayHolidays,
            'displayVacations' => $displayVacations,
            'externalDroppableContainerId'   => $externalDroppableContainerId,
            'externalDroppableEventSelector' => $externalDroppableEventSelector,
            'eventColourPicker' => $eventColourPicker
        ]
    ) ?>
</section>

<?php
/**
 * @var array $attributes HTML attributes for the surrounding element.
 * @var string $title The title of the calendar.
 * @var array $config The calendar configuration.
 * @var string $dialog_size The default size of dialogs when opened out of the calendar.
 * @var array $action_urls URLs for different actions that usually trigger dialogs to be shown.
 * @var bool $display_holidays Whether to show holidays in the calendar (true) or not (false).
 * @var bool $display_vacations Whether to show vacations in the calendar (true) or not (false).
 */
?>
<section class="studip-fullcalendar" <?= arrayToHtmlAttributes($attributes) ?>>
    <?= \Studip\VueApp::create('StudipCalendar')->withProps(
        [
            'title'             => $title,
            'config'            => $config,
            'dialog_size'       => $dialog_size,
            'action_urls'       => $action_urls,
            'display_holidays'  => $display_holidays,
            'display_vacations' => $display_vacations
        ]
    ) ?>
</section>

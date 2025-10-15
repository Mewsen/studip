<?php
/**
 * @var array $attributes HTML attributes for the surrounding element.
 * @var string $title The title of the calendar.
 * @var array $config The calendar configuration.
 * @var string $dialog_size The default size of dialogs when opened out of the calendar.
 * @var array $action_urls URLs for different actions that usually trigger dialogs to be shown.
 */
?>
<section class="studip-fullcalendar" <?= arrayToHtmlAttributes($attributes) ?>>
    <?= \Studip\VueApp::create('StudipCalendar')->withProps(
        [
            'title'       => $title,
            'config'      => $config,
            'dialog_size' => $dialog_size,
            'action_urls' => $action_urls,
        ]
    ) ?>
</section>

<?php
/**
 * @var array $attributes HTML attributes for the surrounding element.
 * @var string $title The title of the calendar.
 * @var array $config The calendar configuration.
 */
?>
<section class="studip-fullcalendar" <?= arrayToHtmlAttributes($attributes) ?>>
    <?= \Studip\VueApp::create('StudipCalendar')->withProps(
        [
            'title' => $title,
            'calendar_options' => $config
        ]
    ) ?>
</section>

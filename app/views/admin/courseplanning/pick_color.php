<?php
/**
 * @var Admin_CourseplanningController $controller
 * @var string $metadate_id
 * @var string $from_action
 * @var string $weekday
 * @var string $semtype
 * @var array $available_colours
 * @var string $color
 */
?>
<form class="default" method="post" action="<?= $controller->pick_color($metadate_id, $from_action, $weekday) ?>" data-dialog="size=auto">

    <div id="event-colour-picker">
        <? foreach ($available_colours as $colour_index => $colour_value) : ?>
            <input type="radio" name="event_colour"
                   id="<?= htmlReady($colour_index) ?>"
                   value="<?= htmlReady($colour_index) ?>"
                   <?= $color === $colour_value ? 'checked' : '' ?>>
            <label for="<?= htmlReady($colour_index) ?>"
                   style="background-color: <?= htmlReady($colour_value) ?>">
            </label>
        <? endforeach ?>
    </div>

<? if (!empty($semtype)): ?>
    <label>
        <input name="event_colour_semtype" type="checkbox" value="1">
        <?= sprintf(_('Farbtyp für alle VA dieses Typs (%s) übernehmen'), htmlReady($semtype)) ?>
    </label>
<? endif; ?>

    <div data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'save') ?>
    </div>
</form>

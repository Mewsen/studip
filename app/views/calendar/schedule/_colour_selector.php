<?php
/**
 * @var string $selected_colour_id
 */
?>
<? foreach ($GLOBALS['PERS_TERMIN_KAT'] as $colour_id => $data) : ?>
    <td class="colour">
        <input type="radio" name="colour_id" value="<?= htmlReady($colour_id) ?>"
               aria-label="<?= sprintf(_('Farbe %s zuordnen'), htmlReady($colour_id)) ?>"
               <?= $selected_colour_id === $colour_id ? 'checked' : '' ?>
               id="colour-<?= htmlReady($colour_id) ?>">
        <label for="colour-<?= htmlReady($colour_id) ?>"
               style="background-color: <?= htmlReady($data['border_color']) ?>;">
            <span class="colour-id"></span>
            <span class="checked-icon"><?= Icon::create('accept', Icon::ROLE_INFO) ?></span>
        </label>
    </td>
<? endforeach ?>

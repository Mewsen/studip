<section id="color_picker">
    <?= _('Farbe des Termins') ?>
    <div>
    <? for ($index = 1; $index <= 18; $index++): ?>
        <span>
            <input type="radio" name="entry_color" value="<?= $index ?>" id="color-<?= $index ?>"
                   <? if ($index == $selected) echo 'checked'; ?>>
            <label class="undecorated schedule-category<?= $index ?>" for="color-<?= $index ?>"></label>
        </span>
    <? endfor; ?>
    </div>
</section>

<section id="color_picker">
    <?= _('Farbe des Termins') ?>
    <div>
    <? for ($index = 1; $index <= 18; $index++): ?>
        <span>
            <input type="radio" name="entry_color" value="<?= $index ?>" id="color-<?= $index ?>"
                   <?= $index === $selected ? 'checked' : '' ?>>
            <label class="undecorated schedule-category<?= $index ?> enter-accessible"
                   for="color-<?= $index ?>"
                   aria-label="<?= sprintf(_('Farbe %u zuordnen'), $index) ?>"
                   title="<?= sprintf(_('Farbe %u zuordnen'), $index) ?>"></label>
        </span>
    <? endfor; ?>
    </div>
</section>

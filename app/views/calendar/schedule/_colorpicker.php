<section id="color_picker">
    <?= _('Farbe des Termins') ?>
    <div>
    <? foreach ($GLOBALS['PERS_TERMIN_KAT'] as $index => $data): ?>
        <span>
            <input type="radio" name="entry_color" value="<?= $index ?>" id="color-<?= $index ?>"
                   <?= $index === $selected ? 'checked' : '' ?>>
            <label class="undecorated schedule-category<?= $index ?> enter-accessible"
                   for="color-<?= $index ?>"
                   aria-label="<?= sprintf(_('Farbe %u zuordnen'), $index) ?>"
                   title="<?= sprintf(_('Farbe %u zuordnen'), $index) ?>"></label>
        </span>
    <? endforeach; ?>
    </div>
</section>

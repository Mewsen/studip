<section id="autoinsert-subject" class="autoinsert-selection hidden-js">
    <h2>
        <?= _('Automatisches Eintragen mit Fach:') ?>
    </h2>

    <?php foreach ($subjects as $subject): ?>
        <section class="col-2">
            <label>
                <input type="checkbox" name="rechte[<?= $subject->id ?>][]" value="1">
                <?= htmlReady($subject->name) ?>
            </label>
        </section>
    <?php endforeach; ?>
</section>

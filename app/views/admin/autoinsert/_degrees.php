<section id="autoinsert-degree" class="autoinsert-selection hidden-js">
    <h2>
        <?= _('Automatisches Eintragen mit Abschluss:') ?>
    </h2>

    <?php foreach ($degrees as $degree): ?>
        <section class="col-2">
            <label>
                <input type="checkbox" name="rechte[<?= $degree->id ?>][]" value="1">
                <?= htmlReady($degree->name) ?>
            </label>
        </section>
    <?php endforeach; ?>
</section>

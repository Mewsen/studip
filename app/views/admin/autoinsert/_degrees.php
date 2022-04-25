<fieldset id="autoinsert-degree" class="autoinsert-selection hidden-js">
    <legend>
        <?= _('Automatisches Eintragen mit Abschluss:') ?>
    </legend>

    <?php foreach ($degrees as $degree): ?>
        <section class="col-2">
            <label>
                <input type="checkbox" name="rechte[degree][]" value="<?= $degree->id ?>">
                <?= htmlReady($degree->name) ?>
            </label>
        </section>
    <?php endforeach; ?>
</fieldset>

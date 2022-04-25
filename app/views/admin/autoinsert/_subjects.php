<fieldset id="autoinsert-subject" class="autoinsert-selection hidden-js">
    <legend>
        <?= _('Automatisches Eintragen mit Fach:') ?>
    </legend>

    <?php foreach ($subjects as $subject): ?>
        <section class="col-2">
            <label>
                <input type="checkbox" name="rechte[subject][]" value="<?= $subject->id ?>">
                <?= htmlReady($subject->name) ?>
            </label>
        </section>
    <?php endforeach; ?>
</fieldset>

<fieldset id="autoinsert-semester" class="autoinsert-selection hidden-js">
    <legend>
        <?= _('Automatisches Eintragen mit Fachsemester:') ?>
    </legend>

    <section class="col-2">
        <label>
            <?= _('Fachsemester') ?>
            <input type="number" min="1" max="<?= $maxsemester ?>" name="rechte[semester]">
        </label>
    </section>
</fieldset>

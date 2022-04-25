<fieldset id="autoinsert-institute" class="autoinsert-selection hidden-js">
    <legend>
        <?= _('Automatisches Eintragen mit Einrichtungszugehörigkeit:') ?>
    </legend>

    <?= Quicksearch::get('institute_id', new StandardSearch('Institut_id'))->withButton() ?>
    <label>
        <?= _('Berechtigung') ?>
    </label>
    <label class="col-2">
        <input type="checkbox" name="rechte[institute][]" value="autor">
        autor
    </label>
    <label class="col-2">
        <input type="checkbox" name="rechte[institute][]" value="tutor">
        tutor
    </label>
    <label class="col-2">
        <input type="checkbox" name="rechte[institute][]" value="dozent">
        dozent
    </label>
</fieldset>

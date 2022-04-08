<section id="autoinsert-institute" class="autoinsert-selection hidden-js">
    <h2>
        <?= _('Automatisches Eintragen mit Einrichtungszugehörigkeit:') ?>
    </h2>

    <?= Quicksearch::get('institute_id', new StandardSearch('Institut_id'))->withButton() ?>
    <label>
        <?= _('Berechtigung') ?>
    </label>
    <label class="col-2">
        <input type="checkbox" name="inst_perm[]" value="autor">
        autor
    </label>
    <label class="col-2">
        <input type="checkbox" name="inst_perm[]" value="tutor">
        tutor
    </label>
    <label class="col-2">
        <input type="checkbox" name="inst_perm[]" value="dozent">
        dozent
    </label>
</section>

<fieldset id="autoinsert-domain" class="autoinsert-selection">
    <legend>
        <?= _('Automatisches Eintragen mit Domänenzugehörigkeit:') ?>
    </legend>

    <?php foreach ($userdomains as $domain): ?>
        <h2>
            <?= htmlReady($domain['name']) ?>
        </h2>
        <section class="hgroup">
            <label>
                <input type="checkbox" name="rechte[domain][<?= $domain['id'] ?>][]" value="dozent">
                <?= _('Dozent') ?>
            </label>
            <label>
                <input type="checkbox" name="rechte[domain][<?= $domain['id'] ?>][]" value="tutor">
                <?= _('Tutor') ?>
            </label>
            <label>
                <input type="checkbox" name="rechte[domain][<?= $domain['id'] ?>][]" value="autor">
                <?= _('Autor') ?>
            </label>
        </section>
    <?php endforeach; ?>
</fieldset>

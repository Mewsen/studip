<section id="autoinsert-domain" class="autoinsert-selection">
    <h2>
        <?= _('Automatisches Eintragen mit Nutzerstatus:') ?>
    </h2>

    <?php foreach ($userdomains as $domain): ?>
        <h3>
            <?= htmlReady($domain['name']) ?>
        </h3>
        <section class="hgroup">
            <label>
                <input type="checkbox" name="rechte[<?= $domain['id'] ?>][]" value="dozent">
                <?= _('Dozent') ?>
            </label>
            <label>
                <input type="checkbox" name="rechte[<?= $domain['id'] ?>][]" value="tutor">
                <?= _('Tutor') ?>
            </label>
            <label>
                <input type="checkbox" name="rechte[<?= $domain['id'] ?>][]" value="autor">
                <?= _('Autor') ?>
            </label>
        </section>
    <?php endforeach; ?>
</section>

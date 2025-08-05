
<?php
/**
 * @var array $files
 * @var array $required
 * @var array $defaults
 */
?>
<p>
    <?= _('In diesem Schritt wählen Sie aus, welche Art von Installation erstellt werden soll: ein leeres System, '
        . 'in dem nur die für den Betrieb notwendigen Daten hinterlegt sind, oder ein Demosystem mit einigen '
        . 'Beispieldaten. Darüber hinaus geben Sie Weitere Grunddaten zur Installation an, die in der Datenbank '
        . 'gespeichert werden.') ?>
    <?= _('Die Grunddaten können Sie nach der Installation weiterhin bearbeiten.') ?>
</p>

<h3><?= _('SQL-Daten einspielen') ?></h3>
<?php $i = 0; foreach ($files as $file => $description): ?>
    <div class="type-text">
        <label class="plain">
            <input type="radio" name="files[]" value="<?= htmlReady($file) ?>" required class="required"
                <?= in_array($file, $_SESSION['STUDIP_INSTALLATION']['system']['files'] ?? []) ? ' checked' : '' ?>>
            <?= htmlReady($description) ?>
        </label>
    </div>
<?php $i += 1; endforeach; ?>

<h3><?= _('Daten zum System') ?></h3>
<div class="type-text required">
    <label for="system-name" class="vertical">
        <?= _('Name der Stud.IP-Installation') ?>
    </label>
    <input required type="text" id="system-name" name="system_name"
           value="<?= htmlReady(Request::get('system_name', $_SESSION['STUDIP_INSTALLATION']['system']['UNI_NAME_CLEAN'] ?? '')) ?>">
</div>

<div class="type-text required">
    <label for="system-id" class="vertical">
        <?= _('Id der Stud.IP-Installation') ?>
    </label>
    <input required type="text" id="system-id" name="system_id"
           value="<?= htmlReady(Request::get('system_id', $_SESSION['STUDIP_INSTALLATION']['system']['STUDIP_INSTALLATION_ID'] ?? '')) ?>"
           placeholder="<?= _('Eindeutiges, gängiges Kürzel Ihrer Einrichtung') ?>">
</div>

<div class="type-text required">
    <label for="system-url" class="vertical">
        <?= _('E-Mail-Adresse für Kontakt') ?>
    </label>
    <input required type="email" id="system-email" name="system_email"
           value="<?= htmlReady(Request::get('system_email', $_SESSION['STUDIP_INSTALLATION']['system']['UNI_CONTACT'] ?? '')) ?>">
</div>

<div class="type-text required">
    <label for="system-url" class="vertical">
        <?= _('URL der Stud.IP-Installation') ?>
    </label>
    <input required type="url" id="system-url" name="system_url"
           value="<?= htmlReady(Request::get('system_url', $_SESSION['STUDIP_INSTALLATION']['system']['ABSOLUTE_URI_STUDIP'] ?? $defaults['system_url'])) ?>"
           placeholder="https://">
</div>

<div class="type-text">
    <label for="system-host-url" class="vertical">
        <?= _('URL der betreibenden Einrichtung') ?>
    </label>
    <input type="url" id="system-host-url" name="system_host_url"
           value="<?= htmlReady(Request::get('system_host_url', $_SESSION['STUDIP_INSTALLATION']['system']['UNI_URL'] ?? '')) ?>"
           placeholder="https://">
</div>

<h3><?= _('Diese Stud.IP-Installation läuft im') ?></h3>
<div class="type-text">
    <label class="plain">
        <input type="radio" name="env" value="development"
               <?php if ($_SESSION['STUDIP_INSTALLATION']['env'] === 'development') echo 'checked'; ?>>
        <?= _('Entwicklungsmodus') ?>
    </label>
</div>

<div class="type-text">
    <label class="plain">
        <input type="radio" name="env" value="production"
               <?php if ($_SESSION['STUDIP_INSTALLATION']['env'] === 'production') echo 'checked'; ?>>
        <?= _('Produktivmodus') ?>
    </label>
</div>

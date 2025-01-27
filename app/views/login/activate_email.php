<?php if (isset($mail_explain)) : ?>
    <form action="<?= URLHelper::getLink() ?>" method="post" class="default">
        <fieldset>
        <legend>
            <?= _('Sie haben Ihre E-Mail-Adresse geändert.
        Um diese frei zu schalten müssen Sie den Ihnen an Ihre neue Adresse zugeschickten Aktivierungs Schlüssel im unten stehenden Eingabefeld eintragen.') ?>
        </legend>
        <?= CSRFProtection::tokenTag() ?>
            <label>
                <?=_('Aktivierungs Schlüssel')?>
                <input type="text" name="key">
            </label>
            <input name="uid" type="hidden" value="<?= htmlReady(Request::option('uid')) ?>">
        </fieldset>

        <footer><?= Studip\Button::createAccept() ?></footer>
    </form>
<?php endif; ?>
<?php if (isset($reenter_mail)) : ?>
    <form action="<?= URLHelper::getLink() ?>" method="post" class="default">
        <fieldset>
            <legend>
                <?= _('Sollten Sie keine E-Mail erhalten haben, können Sie sich einen neuen Aktivierungsschlüssel zuschicken lassen. Geben Sie dazu Ihre gewünschte E-Mail-Adresse unten an.') ?>
            </legend>
            <?= CSRFProtection::tokenTag() ?>
            <label>
                <?= _('Email') ?>
                <input type="email" name="email1" required>
            </label>
            <label>
                <?= _('Wiederholung') ?>
                <input type="email" name="email2" required>
            </label>
            <input name="uid" type="hidden" value="<?= htmlReady(Request::option('uid')) ?>">
        </fieldset>

        <footer><?= Studip\Button::createAccept() ?></footer>
    </form>
<?php endif; ?>

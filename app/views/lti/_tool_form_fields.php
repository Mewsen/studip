<label>
    <?= _('URL der Anwendung') ?>
    <?= tooltipIcon(_('Die Betreiber dieses Tools müssen Ihnen eine URL und Zugangsdaten (Consumer-Key und Consumer-Secret) mitteilen.')) ?>
    <input type="text" name="launch_url" value="<?= htmlReady($custom_launch_url ?: $tool->launch_url) ?>">
</label>

<label>
    <?= _('Login-URL') ?>
    <?= tooltipIcon(_('Die URL, mit der der Login via OpenID Connect stattfindet.')) ?>
    <input type="text" name="oidc_init_url" value="<?= htmlReady($tool->oidc_init_url ?? '') ?>">
</label>

<label>
    <?= _('JWKS-URL') ?>
    <?= tooltipIcon(_('Die URL, mit der der der Austausch von JSON web keys stattfinden kann.')) ?>
    <input type="text" name="jwks_url" value="<?= htmlReady($tool->jwks_url ?? '') ?>">
</label>

<label>
    <?= _('Consumer-Key des LTI-Tools') ?>
    <input type="text" name="consumer_key" value="<?= htmlReady($tool->consumer_key) ?>">
</label>

<label>
    <?= _('Consumer-Secret des LTI-Tools') ?>
    <input type="text" name="consumer_secret" value="<?= htmlReady($tool->consumer_secret) ?>">
</label>

<label>
    <?= _('LTI-Version') ?>
    <select name="lti_version">
        <option value="1.1" <?= empty($tool->lti_version) || $tool->lti_version === '1.1' ? 'selected' : '' ?>>
            1.0/1.1
        </option>
        <option value="1.3a" <?= !empty($tool->lti_version) && $tool->lti_version === '1.3a' ? 'selected' : '' ?>>
            1.3a
        </option>
    </select>
</label>

<label>
    <?= _('OAuth Signatur Methode des LTI-Tools') ?>
    <select name="oauth_signature_method">
        <option value="sha1">HMAC-SHA1</option>
        <option value="sha256" <?= empty($tool->oauth_signature_method) || $tool->oauth_signature_method === 'sha256' ? 'selected' : '' ?>>HMAC-SHA256</option>
    </select>
</label>

<label>
    <?= _('Schlüssel des LTI-Tools per URL laden') ?>
    <input type="url" name="keyset_url" value="TODO">
</label>

<label>
    <?= _('Öffentlicher Schlüssel des LTI-Tools') ?>
    <?
    $keyring = $tool->getKeyring();
    $public_key_string = '';
    if ($keyring) {
        $keychain = $keyring->toKeyChain();
        $public_key_string = $keychain->getPublicKey()->getContent();
    }
    ?>
    <textarea name="tool_public_key"><?= htmlReady($public_key_string) ?></textarea>
</label>

<label>
    <input type="checkbox" name="send_lis_person" value="1" <?= $tool->send_lis_person ? ' checked' : '' ?>>
    <?= _('Nutzerdaten an LTI-Tool senden') ?>
    <?= tooltipIcon(_('Nutzerdaten dürfen nur an das externe Tool gesendet werden, wenn es keine Datenschutzbedenken gibt. Mit Setzen des Hakens bestätigen Sie, dass die Übermittlung der Daten zulässig ist.')) ?>
</label>

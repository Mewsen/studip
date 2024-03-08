<?php
/**
 * @var \OAT\Library\Lti1p3Core\Platform\Platform $platform
 */
?>
<dl>
    <dt><?= _('Zielgruppe') ?></dt>
    <dd><?= htmlReady($platform->getAudience()) ?></dd>

    <dt><?= _('OAuth2 access token URL') ?></dt>
    <dd><?= htmlReady($platform->getOAuth2AccessTokenUrl()) ?></dd>

    <dt><?= _('OIDC authentication URL') ?></dt>
    <dd><?= htmlReady($platform->getOidcAuthenticationUrl()) ?></dd>

    <?
    $keyring = \Studip\LTI13a\PlatformManager::getPlatformKeyring();
    ?>
    <? if ($keyring) : ?>
        <dt><?= _('Öffentlicher Schlüssel') ?></dt>
        <dd><pre><?= htmlReady($keyring->public_key) ?></pre></dd>
    <? endif ?>
</dl>

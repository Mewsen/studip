<?php
/**
 * @var \OAT\Library\Lti1p3Core\Platform\Platform $platform
 */
?>
<dl>
    <dt><?= _('Plattform-ID') ?></dt>
    <dd>
        <a href="<?= htmlReady($platform->getAudience()) ?>">
            <?= htmlReady($platform->getAudience()) ?>
        </a>
    </dd>

    <dt><?= _('OAuth2 access token URL') ?></dt>
    <dd>
        <a href="<?= htmlReady($platform->getOAuth2AccessTokenUrl()) ?>">
            <?= htmlReady($platform->getOAuth2AccessTokenUrl()) ?>
        </a>
    </dd>

    <dt><?= _('OIDC authentication URL') ?></dt>
    <dd>
        <a href="<?= htmlReady($platform->getOidcAuthenticationUrl()) ?>">
            <?= htmlReady($platform->getOidcAuthenticationUrl()) ?>
        </a>
    </dd>

    <dt><?= _('JWKS URL') ?></dt>
    <dd>
        <a href="<?= URLHelper::getLink('dispatch.php/lti/auth/jwks', [], true) ?>">
            <?= URLHelper::getLink('dispatch.php/lti/auth/jwks', [], true) ?>
        </a>
    </dd>

    <dt><?= _('Client ID') ?></dt>
    <dd><?= htmlReady(Config::get()->STUDIP_INSTALLATION_ID) ?></dd>

    <?
    $keyring = \Studip\LTI13a\PlatformManager::getPlatformKeyring();
    if (!$keyring) {
        $keyring = \Studip\LTI13a\PlatformManager::generatePlatformKeyring();
    }
    ?>
    <? if ($keyring) : ?>
        <dt><?= _('Öffentlicher Schlüssel') ?></dt>
        <dd><pre><?= htmlReady($keyring->public_key) ?></pre></dd>
    <? endif ?>
</dl>

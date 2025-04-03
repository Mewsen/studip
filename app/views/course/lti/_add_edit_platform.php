<?php
/**
 * @var string $mode
 * @var AuthenticatedController $controller
 * @var LtiPlatform $platform
 */
?>
<form class="default" method="post"
      action="<?= $mode === 'edit'
          ? $controller->link_for('course/lti/edit_platform/' . $platform->id)
          : $controller->link_for('course/lti/add_platform')
      ?>">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('LTI-Plattform konfigurieren') ?></legend>
        <label>
            <?= _('Name') ?>
            <input type="text" name="name" value="<?= htmlReady($platform->name ?? '') ?>">
        </label>
        <label>
            <?= _('LTI Platform-ID') ?>
            <input type="url" name="url" value="<?= htmlReady($platform->url ?? '') ?>">
        </label>
        <label>
            <?= _('OAuth2 Access Token URL') ?>
            <input type="url" name="oauth2_access_token_url"
                   value="<?= htmlReady($platform->oauth2_access_token_url ?? '') ?>">
        </label>
        <label>
            <?= _('OIDC Init URL') ?>
            <input type="url" name="oidc_init_url"
                   value="<?= htmlReady($platform->oidc_init_url ?? '') ?>">
        </label>
        <label>
            <?= _('JWKS URL') ?>
            <input type="url" name="jwks_url"
                   value="<?= htmlReady($platform->jwks_url ?? '') ?>">
        </label>
        <label>
            <?= _('JWKS Schlüssel-ID') ?>
            <input type="text" name="jwks_key_id"
                   value="<?= htmlReady($platform->jwks_key_id ?? '') ?>">
        </label>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::create(_('Speichern'), 'save') ?>
        <?= \Studip\Button::createCancel(_('Abbrechen')) ?>
    </div>
</form>

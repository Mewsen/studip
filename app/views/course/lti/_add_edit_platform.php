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
        <label class="studiprequired">
            <span class="textlabel"><?= _('Name') ?></span>
            <span class="asterisk">*</span>
            <input type="text" name="name" required value="<?= htmlReady($platform->name ?? '') ?>">
        </label>
        <label class="studiprequired">
            <span class="textlabel"><?= _('LTI Platform-ID') ?></span>
            <span class="asterisk">*</span>
            <input type="url" name="url" required value="<?= htmlReady($platform->url ?? '') ?>">
        </label>
        <label class="studiprequired">
            <span class="textlabel"><?= _('OAuth2 Access Token URL') ?></span>
            <span class="asterisk">*</span>
            <input type="url" name="oauth2_access_token_url" required
                   value="<?= htmlReady($platform->oauth2_access_token_url ?? '') ?>">
        </label>
        <label class="studiprequired">
            <span class="textlabel"><?= _('OIDC Init URL') ?></span>
            <span class="asterisk">*</span>
            <input type="url" name="oidc_init_url" required
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

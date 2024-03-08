<form class="default" action="<?= $controller->link_for('course/lti/save_config') ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= _('Einstellungen') ?>
        </legend>

        <label>
            <span class="required">
                <?= _('Titel des Reiters') ?>
            </span>
            <input type="text" name="title" value="<?= htmlReady($title) ?>" required>
        </label>
    </fieldset>

    <fieldset>
        <legend><?= _('LTI Plattform-Konfiguration') ?></legend>
        <?= $this->render_partial(
            'lti/_platform_data',
            [
                'platform' => \Studip\LTI13a\PlatformManager::getPlatformConfiguration()
            ]
        ) ?>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'save') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('course/lti')) ?>
    </footer>
</form>

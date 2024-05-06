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
        <legend><?= _('Datenschutzhinweis beim Wechsel in ein LTI-Tool') ?></legend>
        <label>
            <?= _('Text des Datenschutzhinweises') ?>
            <textarea name="personal_data_warning"><?= htmlReady($personal_data_warning) ?></textarea>
        </label>
        <label>
            <input type="checkbox" value="1" name="reset_warning"
                   data-deactivates="textarea[name='personal_data_warning']">
            <?= _('Den systemweit konfigurierten Standardtext verwenden.') ?>
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

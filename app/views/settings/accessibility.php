<?php
/**
 * @var Settings_AccessibilityController $controller
 * @var UserConfig $config
 */
?>
<form method="post" action="<?= $controller->store() ?>" class="default">
    <?= CSRFProtection::tokenTag() ?>

    <fieldset>
        <legend id="accessibility"><?= _('Barrierefreiheitseinstellungen') ?></legend>

        <label>
            <input type="checkbox" name="enable_high_contrast"
                   value="1"
                <? if ($config->getValue('USER_HIGH_CONTRAST')) echo 'checked'; ?>>
            <?= _('Kontrastreiches Farbschema aktivieren') ?>
            <?= tooltipIcon(
                _('Mit dieser Einstellung wird ein Farbschema mit hohem Kontrast aktiviert.')
            ) ?>
        </label>

        <label for="a11y-reduce-default">
            <?= _('Bewegungen/Animationen reduzieren') ?>
            <?= tooltipIcon(
                _('Mit dieser Einstellung werden animierte Effekte in Stud.IP reduziert.')
            ) ?>
        </label>

        <label>
            <input type="radio" name="reduce_animations"
                   id="a11y-reduce-default"
                   value="default"
                   <? if ($config->getValue('A11Y_USER_REDUCE_ANIMATIONS') === 'default') echo 'checked'; ?>
            >
            <?= _('Standard (übernommen vom Betriebssystem)') ?>
        </label>

        <label>
            <input type="radio" name="reduce_animations"
                   value="yes"
                   <? if ($config->getValue('A11Y_USER_REDUCE_ANIMATIONS') === 'yes') echo 'checked'; ?>
            >
            <?= _('Bewegungen reduzieren') ?>
        </label>

        <label>
            <input type="radio" name="reduce_animations"
                   value="no"
                   <? if ($config->getValue('A11Y_USER_REDUCE_ANIMATIONS') === 'no') echo 'checked'; ?>
            >
            <?= _('Bewegungen erlauben') ?>
        </label>
    </fieldset>

    <footer>
        <?= \Studip\Button::create(_('Speichern')) ?>
    </footer>
</form>

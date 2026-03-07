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

        <label>
            <?= _('Bewegungen/Animationen reduzieren') ?>
            <?= tooltipIcon(
                _('Mit dieser Einstellung werden animierte Effekte in Stud.IP reduziert.')
            ) ?>
            <select name="reduce_animations">
                <option value="default"
                        <? if ($config->getValue('A11Y_USER_REDUCE_ANIMATIONS') === 'default') echo 'selected'; ?>
                >
                    <?= _('Standard (übernommen vom Betriebssystem)') ?>
                </option>
                <option value="yes"
                        <? if ($config->getValue('A11Y_USER_REDUCE_ANIMATIONS') === 'yes') echo 'selected'; ?>
                >
                    <?= _('Bewegungen reduzieren') ?>
                </option>
                <option value="no"
                        <? if ($config->getValue('A11Y_USER_REDUCE_ANIMATIONS') === 'no') echo 'selected'; ?>
                >
                    <?= _('Bewegungen erlauben') ?>
                </option>
            </select>
        </label>
    </fieldset>

    <footer>
        <?= \Studip\Button::create(_('Speichern')) ?>
    </footer>
</form>

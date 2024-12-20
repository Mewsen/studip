<?php
/**
 * @var Module_ModuleController $controller
 * @var array $content_languages
 * @var string $original_language
 * @var string $module_id
 * @var array $translations
 */
?>
<form class="default" action="<?= $controller->store_language($module_id) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= _('Wählen Sie die neue Sprache der Originalfassung') ?>
        </legend>
        <label>
            <?= _('Neue Originalsprache') ?>
            <select name="new_language">
                <? foreach ($content_languages as $code => $language) : ?>
                    <? if ($code !== $original_language) : ?>
                        <option value="<?= htmlReady($code) ?>"><?= htmlReady($language['name']) ?></option>
                    <? endif ?>
                <? endforeach ?>
            </select>
        </label>
        <? if (count($translations) > 1) : ?>
            <label>
                <input type="checkbox" name="swap_data" value="1">
                <?= _('Inhalte tauschen') ?>
            </label>
        <? endif ?>
    </fieldset>
    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Sprache ändern'))?>
    </footer>
</form>

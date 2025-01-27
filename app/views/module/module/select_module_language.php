<?php
/**
 * @var Module_ModuleController $controller
 * @var array $content_languages
 * @var string $default_language
 */
?>
<form class="default" action="<?= $controller->modulURL() ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= _('Wählen Sie die Sprache der Originalfassung') ?>
        </legend>
        <label>
            <?= _('Sprache') ?>
            <select name="display_language">
                <option value="<?= htmlReady($default_language) ?>">
                    <?= htmlReady($content_languages[$default_language]['name']) . ' (' ._('Standardsprache') . ')'?></option>
                <? foreach ($content_languages as $code => $language) : ?>
                    <? if ($code !== $default_language) : ?>
                        <option value="<?= htmlReady($code) ?>"><?= htmlReady($language['name']) ?></option>
                    <? endif ?>
                <? endforeach ?>
            </select>
        </label>
    </fieldset>
    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Modul anlegen'))?>
    </footer>
</form>

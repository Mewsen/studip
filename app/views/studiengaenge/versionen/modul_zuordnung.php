<?php
/**
 * @var Studiengaenge_VersionenController $controller
 * @var StgteilabschnittModul $zuordnung
 */
use Studip\Button, Studip\LinkButton;
?>
<? $perm = new MvvPerm($zuordnung) ?>
<form data-dialog="" class="default" action="<?= $controller->action_link('modul_zuordnung_store/' . $zuordnung->id) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Grunddaten') ?></legend>
        <label><?= _('Bezeichnung der Modulzuordnung') ?>
            <?= MvvI18N::input(
                'bezeichnung',
                $zuordnung->bezeichnung,
                ['maxlength' => '250']
            )->checkPermission($zuordnung) ?>
        </label>
    </fieldset>
    <fieldset>
        <legend><?= _('Weitere Angaben') ?></legend>
        <label for="zuordnung_flexnow_modul"><?= _('ID der Zuordnung aus Fremdsystem') ?>
            <input <?= $perm->disable('flexnow_modul') ?>
                    type="text" name="flexnow_modul" id="zuordnung_flexnow_modul"
                    maxlength="250"
                    value="<?= htmlReady($zuordnung->flexnow_modul) ?>">
        </label>
        <label for="zuordnung_modulcode"><?= _('Spezifischer Modulcode') ?>
            <input <?= $perm->disable('modulcode') ?>
                    type="text" name="modulcode" id="zuordnung_modulcode"
                    maxlength="250" value="<?= htmlReady($zuordnung->modulcode) ?>">
        </label>
    </fieldset>
    <? if (count($zuordnung->datafields)) : ?>
        <fieldset>
            <legend><?= _('Angaben zum Modul am Studiengangteilabschnitt') ?></legend>
            <? $default_language = array_keys($GLOBALS['CONTENT_LANGUAGES'])[0] ?>
            <? foreach ($zuordnung->datafields as $entry) : ?>
                <? $tdf = $entry->getTypedDatafield(); ?>
                <? if ($perm->haveDfEntryPerm($entry->datafield_id, MvvPerm::PERM_WRITE)) : ?>
                    <?= $tdf->getHTML('datafields') ?>
                <? else : ?>
                    <em><?= htmlReady($tdf->getName()) ?>:</em><br>
                    <?= $tdf->getDisplayValue() ?>
                <? endif; ?>
            <? endforeach; ?>
        </fieldset>
    <? endif; ?>
    <footer data-dialog-button>
        <?= Button::createAccept(
            _('Übernehmen'),
            'store',
            ['title' => _('Änderungen übernehmen')]
        ) ?>
        <?= LinkButton::createCancel(
            _('Abbrechen'),
            $controller->action_url(''),
            ['title' => _('zurück zur Übersicht')]
        ) ?>
    </footer>
</form>

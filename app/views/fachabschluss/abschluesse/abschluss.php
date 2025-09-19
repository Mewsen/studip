<?php
/**
 * @var Fachabschluss_AbschluesseController $controller
 * @var Abschluss $abschluss
 * @var AbschlussKategorie[] $abschluss_kategorien
 */
?>
<? use Studip\Button, Studip\LinkButton; ?>
<? $perm = MvvPerm::get($abschluss) ?>
<form class="default" action="<?= $controller->abschlussLink($abschluss->id) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Grunddaten') ?></legend>
        <label>
            <?= _('Name') ?>
            <?= MvvI18N::input(
                'name',
                $abschluss->name,
                [
                    'maxlength' => '255',
                    'minlength' => '4',
                    'required' => ''
                ]
            )->checkPermission($abschluss) ?>
        </label>
        <label>
            <?= _('Kurzname') ?>
            <?= MvvI18N::input(
                'name_kurz',
                $abschluss->name_kurz,
                ['maxlength' => '50']
            )->checkPermission($abschluss) ?>
        </label>
        <label>
            <?= _('Beschreibung') ?>
            <?= MvvI18N::textarea(
                'beschreibung',
                $abschluss->beschreibung,
                ['class' => 'wysiwyg']
            )->checkPermission($abschluss) ?>
    </fieldset>
    <fieldset>
        <legend><?= _('Abschluss-Kategorie wählen') ?></legend>
        <? if ($perm->haveFieldPerm('category_assignment')) : ?>
            <label><?= _('Abschluss-Kategorie') ?></label>
            <select id="abschluss_kategorie" name="kategorie_id" size="1">
                <option value="">-- <?= _('Bitte wählen') ?> --</option>
                <? foreach ($abschluss_kategorien as $kategorie) : ?>
                    <option
                        <?= ($kategorie->id === $abschluss->kategorie_id ? 'selected ' : '') ?>value="<?= $kategorie->id ?>"><?= htmlReady($kategorie->name) ?></option>
                <? endforeach; ?>
            </select>
            </label>
        <? else : ?>
            <?= htmlReady($abschluss->category->getDisplayName()) ?>
        <? endif; ?>
    </fieldset>
    <footer data-dialog-button>
        <? if ($abschluss->isNew()) : ?>
            <? if ($perm->havePermCreate()) : ?>
                <?= Button::createAccept(_('Anlegen'), 'store', ['title' => _('Abschluss anlegen')]) ?>
            <? endif; ?>
        <? else : ?>
            <? if ($perm->havePermWrite()) : ?>
                <?= Button::createAccept(_('Übernehmen'), 'store', ['title' => _('Änderungen übernehmen')]) ?>
            <? endif; ?>
        <? endif; ?>
        <?= LinkButton::createCancel(_('Abbrechen'), $controller->indexURL(), ['title' => _('Zurück zur Übersicht')]) ?>
    </footer>
</form>

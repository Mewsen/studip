<?php
/**
 * @var ResourceCategory[] $categories
 * @var Resources_BuildingController $controller
 */
?>
<? if ($categories) : ?>
<form method="get" action="<?= $controller->add() ?>" class="default"
      data-dialog="size=auto">
    <label>
        <?= _('Gebäudekategorie') ?>
        <select name="category_id" required>
            <option value=""><?= _('Bitte eine Gebäudekategorie auswählen:') ?></option>
            <? foreach ($categories as $category) : ?>
                <option value="<?= $category->id ?>">
                    <?= htmlReady($category->name) ?>
                </option>
            <? endforeach ?>
        </select>
    </label>
    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Auswählen')) ?>
    </footer>
<? endif ?>

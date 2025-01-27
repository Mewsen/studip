<?php
/**
 * @var Modul $modul
 * @var int $type
 * @var Shared_ModulController $controller
 */
?>

<? if (count($modul->deskriptoren) > 1): ?>
<div style="width: 100%; text-align: right;">
    <? foreach ($modul->deskriptoren->getAvailableTranslations($modul->original_language) as $language) : ?>
        <? $lang = $GLOBALS['CONTENT_LANGUAGES'][$language]; ?>
        <a data-dialog="size=auto;title='<?= htmlReady($modul->getDisplayName()) ?>'" href="<?= $controller->action_link('description/' . $modul->id . '/', ['display_language' => $language]) ?>">
            <?= Assets::img(MVV::getContentLanguageImagePath($language), ['alt' => $lang['name'], 'size' => 24]) ?>
        </a>
    <? endforeach; ?>
</div>
<? endif; ?>
<?= $this->render_partial('shared/modul/_modul') ?>
<? if ($type === 1) : ?>
    <?= $this->render_partial('shared/modul/_modullvs') ?>
    <?= $this->render_partial('shared/modul/_pruefungen') ?>
    <?= $this->render_partial('shared/modul/_regularien') ?>
<? endif;?>
<? if ($type === 2): ?>
    <?= $this->render_partial('shared/modul/_modullv') ?>
<? endif; ?>
<? if ($type === 3) : ?>
    <?= $this->render_partial('shared/modul/_modul_ohne_lv') ?>
<? endif; ?>

<?php
/**
 * @var ModulDeskriptor|ModulteilDeskriptor $descriptor
 * @var string $original_language
 * @var string $display_language
 * @var string $link
 */
?>

<? $languages = $descriptor->getAvailableTranslations($original_language) ?>
<? $content_languages = array_merge(array_flip($languages), $GLOBALS['CONTENT_LANGUAGES']) ?>
<? foreach ($content_languages as $code => $language) : ?>
<div style="padding-top:10px;">
    <a href="<?= URLHelper::getLink($link, ['display_language' => $code]) ?>">
        <?= Assets::img(MVV::getContentLanguageImagePath($code), ['alt' => $language['name'], 'size' => 24]) ?>
        <?= $language['name'] ?> (<?= ($code === $original_language ? _('Originalfassung') : '')
            . (in_array($code, $languages) ? '' : _('neu')) ?>)
        <?= $code === $display_language ? Icon::create('accept', Icon::ROLE_ACCEPT) : '' ?>
    </a>
</div>
<? endforeach ?>

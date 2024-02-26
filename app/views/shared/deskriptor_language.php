<? $table = get_class($modul) == 'Modul' ? 'mvv_modul_deskriptor' : 'mvv_modulteil_deskriptor'; ?>
<? $languages = $modul->deskriptoren->getAvailableTranslations(); ?>
<? foreach ($GLOBALS[strtoupper($table)]['SPRACHE']['values'] as $lang => $value) : ?>
<div style="padding-top:10px;">
    <a href="<?= URLHelper::getLink($link, ['display_language' => $lang]) ?>">
        <?= Assets::img(MVV::getContentLanguageImagePath($lang), ['alt' => $value['name'], 'size' => 24]) ?>
        <?= $value['name'] ?> (<?= in_array($lang, $languages) ? 'bearbeiten' : 'neu anlegen' ?>)
        <?= $lang == $sprache ? Icon::create('accept', 'accept', [])->asImg() : '' ?>
    </a>
</div>
<? endforeach; ?>

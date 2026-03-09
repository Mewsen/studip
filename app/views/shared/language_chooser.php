<div class="mvv-widget-right">
    <div class="mvv-inst-chooser">
        <select id="mvv-language-chooser-select" style="display: inline;" name="<?= $chooser_id ?>">
            <option class="mvv-inst-chooser-level" value="">-- <?= _('Bitte wählen') ?> --</option>
        <? foreach ($chooser_languages as $key => $language) : ?>
            <option class="" data-fb="<?= $key ?>" value="<?= $key ?>">
                <?= htmlReady($language['name']); ?>
            </option>
        <? endforeach; ?>
        </select>
        <span role="button" tabindex="0" class="mvv-inst-add-button"><?= Icon::create('arr_2up')->asImg(['title' => _('Sprache zuordnen')]) ?></span>
    </div>
    <?= $addition ?>
</div>

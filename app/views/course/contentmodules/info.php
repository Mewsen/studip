<? if ($plugin->getDescriptionMode() === 'replace_all') : ?>
    <?= formatReady($plugin->getPluginDescription()) ?>
<? else : ?>
    <div class="contentmodule_info">
        <div class="main_part">
            <div class="header">
                <div class="image">
                <? if ($metadata['icon']): ?>
                    <?= $metadata['icon']->copyWithRole(Icon::ROLE_INFO)->asImg(100) ?>
                <? endif; ?>
                </div>
                <div class="text">
                    <h1><?= htmlReady($metadata['displayname'] ?? $plugin->getPluginName()) ?></h1>
                <? if (!empty($metadata['summary'])): ?>
                    <strong>
                        <?= htmlReady($metadata['summary']) ?>
                    </strong>
                <? endif; ?>
                </div>
            </div>
            <?= Studip\VueApp::create('ContentModulesControl')->withProps([
                 'module_id' => (string) $plugin->getPluginId(),
            ]) ?>
            <? $keywords = preg_split( "/;/", $metadata['keywords'] ?? '', -1, PREG_SPLIT_NO_EMPTY) ?>
            <? if (count($keywords) > 0) : ?>
            <ul class="keywords">
                <? foreach ($keywords as $keyword) : ?>
                <li>
                    <?= htmlReady($keyword) ?>
                </li>
                <? endforeach ?>
            </ul>
            <? endif ?>
            <div class="description">
                <?= formatReady($plugin->getPluginDescription()) ?>
            </div>
        </div>
        <? if (isset($screenshots) && count($screenshots)) : ?>
        <ul class="screenshots clean">
            <? foreach ($screenshots as $screenshot) : ?>
            <li>
                <a href="<?= htmlReady($screenshot['source']) ?>"
                   data-lightbox="<?= htmlReady($metadata['displayname'] ?? $plugin->getPluginName()) ?>"
                   data-title="<?= htmlReady($screenshot['title']) ?>">
                    <img src="<?= htmlReady($screenshot['source']) ?>" alt="">
                    <?= htmlReady($screenshot['title']) ?>
                </a>
            </li>
            <? endforeach ?>
        </ul>
        <? endif ?>
    </div>
<? endif ?>

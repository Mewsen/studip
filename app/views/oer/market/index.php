<?php
/**
 * @var Oer_MarketController $controller
 * @var array $material_data
 * @var array $tags
 * @var OERMaterial[] $new_ones
 */
?>
<?= Studip\VueApp::create('OERSearch')
        ->withProps([
            'url' => $controller->link_for('oer/market/search'),
            'search-results' => $material_data,
            'filtered-tag' => Request::get('tag'),
            'filtered-category' => Request::get('category'),
            'tags' => $tags,
            'material-select-url-template' => $controller->detailsURL('__material_id__'),
        ])  ?>

<? if (!empty($new_ones)) : ?>
    <div id="new_ones">
        <h2><?= _('Neuste Materialien') ?></h2>
        <ul class="oer_material_overview">
            <?= $this->render_partial('oer/market/_materials.php', ['materialien' => $new_ones]) ?>
        </ul>
    </div>
<? endif ?>

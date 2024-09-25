<?
if ($best_nine_tags && count($best_nine_tags) > 0) {
    $tags = [];
    foreach ($best_nine_tags as $tag) {
        $tags[] = [
            'tag_hash' => $tag['tag_hash'],
            'name' => $tag['name']
        ];
    }
}
?>
<?= Studip\VueApp::create('OERSearch')
    ->withProps([
        'url' => $controller->url_for('oer/market/search'),
        'search-results' => $material_data ?? false,
        'filtered-tag' => Request::get('tag'),
        'filtered-category' => Request::get('category'),
        'tags' => $tags,
        'material-select-url-template' => $controller->url_for('oer/addfile/choose_file', ['material_id' => '__material_id__']),
        'to-plugin' => Request::get('to_plugin'),
        'to-folder-id' => Request::get('to_folder_id'),
    ])  ?>

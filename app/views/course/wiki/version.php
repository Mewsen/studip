<?php
/**
 * @var \Studip\VueApp $contentBarVueApp
 */
?>

<?= $contentBarVueApp->render() ?>

<div class="wiki_page_content">
    <?= wikiReady($version['content']) ?>
</div>

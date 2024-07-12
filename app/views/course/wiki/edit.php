<?php
/**
 * @var WikiPage $page
 * @var Course_WikiController $controller
 * @var WikiOnlineEditingUser $me_online
 * @var ContentBar $contentbar
 */
?>

<?= $contentbar ?>

<?= Studip\VueApp::create('WikiEditor')
    ->withProps([
        'cancel-url'   => $controller->leave_editingURL($page),
        'chdate'       => date('c', $page->chdate),
        'page-content' => wikiReady($page->content, true, $page->range_id, $page->id),
        'editing'      => (bool) $me_online->editing,
        'page-id'      => (int) $page->id,
        'save-url'     => $controller->saveURL($page),
        'users'        => $page->getOnlineUsers(),
    ])
?>

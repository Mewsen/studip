<?php
/**
 * @var WikiPage $page
 * @var Course_WikiController $controller
 * @var WikiOnlineEditingUser $me_online
 */
?>

<?= Studip\VueApp::create('WikiEditor')
    ->withProps([
        'cancel-url'   => $controller->leave_editingURL($page),
        'chdate'       => date('c', $page->chdate),
        'page-content' => $page->content,
        'editing'      => (bool) $me_online->editing,
        'page-id'      => (int) $page->id,
        'save-url'     => $controller->saveURL($page),
        'users'        => $page->getOnlineUsers(),
        'toc'          => CoreWiki::getTOC($page),
    ])
?>

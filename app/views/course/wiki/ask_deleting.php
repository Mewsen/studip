<?php
/**
 * @var Course_WikiController $controller
 * @var WikiPage $page
 */
?>
<form action="" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <div class="file_select_possibilities">
        <div>
        <? if (count($page->versions) > 0 && $page->versions->first()->isDeletable()): ?>
            <div class="clickable">
                <button
                    class="as-link"
                    data-confirm="<?= _('Wirklich die letzte Änderung löschen?') ?>"
                    formaction="<?= $controller->deleteversionURL($page, ['redirect_to' => 'page']) ?>"
                >
                    <?= Icon::create('archive2')->asImg(50) ?>
                    <?= _('Nur die letzte Änderung löschen') ?>
                </button>
            </div>
        <? endif; ?>
        <? if ($page->isDeletable()): ?>
            <div class="clickable">
                <button
                    class="as-link"
                    data-confirm="<?= _('Wollen Sie wirklich die komplette Seite löschen?') ?>"
                    formaction="<?= $controller->deleteURL($page) ?>"
                >
                    <?= Icon::create('wiki')->asImg(50) ?>
                    <?= _('Ganze Wikiseite löschen') ?>
                </button>
            </div>
        <? endif; ?>
        </div>
    </div>
</form>

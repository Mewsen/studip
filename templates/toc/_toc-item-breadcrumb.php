<? if (!$item->isRoot()) : ?>
    <?= $this->render_partial('toc/_toc-item-breadcrumb', ['item' => $item->getParent()]) ?>
<? endif ?>
<li class="contentbar-breadcrumb-item <? if ($item->isActive()) echo 'contentbar-breadcrumb-item-current'; ?>">
    <? if (!$item->isActive()) : ?>
        <a class="navigate" href="<?= htmlReady($item->getURL()) ?>">
    <? endif ?>
        <?= htmlReady($item->getTitle()) ?>
    <? if (!$item->isActive()) : ?>
        </a>
    <? endif ?>
</li>

<!-- Start sidebar -->
<aside id="sidebar" aria-label="<?= _('Seitenleiste') ?>" class="<?= empty($widgets) ? 'empty-sidebar' : '' ?>">
    <? foreach ($widgets as $index => $widget): ?>
        <?= $widget->render(['base_class' => 'sidebar']) ?>
    <? endforeach; ?>
</aside>
<!-- End sidebar -->

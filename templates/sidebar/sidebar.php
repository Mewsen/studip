<!-- Start sidebar -->
<aside id="sidebar" aria-label="<?= _('Seitenleiste') ?>">
    <div class="sidebar-title" style="display:none">
        <?= htmlReady($title) ?>
    </div>
    <? foreach ($widgets as $index => $widget): ?>
        <?= $widget->render(['base_class' => 'sidebar']) ?>
    <? endforeach; ?>
</aside>
<!-- End sidebar -->

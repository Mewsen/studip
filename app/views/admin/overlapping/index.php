<?php
/**
 * @var SimpleORMapCollection $conflicts
 * @var array $semtypes
 * @var array $fachsems
 * @var array $stgteil_versions
 * @var string $fullcalendar
 */
?>
<?= $this->render_partial('admin/overlapping/selection', ['fachsems' => $fachsems, 'semtypes' => $semtypes]) ?>
<? if (count($conflicts)) : ?>
    <?= $this->render_partial('admin/overlapping/overlapping') ?>
<? endif; ?>

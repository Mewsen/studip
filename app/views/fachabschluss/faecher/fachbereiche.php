<?php
/**
 * @var MVVController $controller
 * @var Fachbereich[] $fachbereiche
 * @var string $fachbereich_id
 */
?>
<table class="default collapsable">
    <colgroup>
        <col>
        <col style="width: 5%;">
    <thead>
        <tr class="sortable">
            <?= $controller->renderSortLink('fachabschluss/faecher/fachbereiche/', _('Fachbereich'), 'name') ?>
            <?= $controller->renderSortLink('fachabschluss/faecher/fachbereiche/', _('Fächer'), 'faecher', ['style' => 'text-align: center;']) ?>
        </tr>
    </thead>
    <? foreach ($fachbereiche as $fachbereich_data): ?>
        <? $fachbereich = Fachbereich::buildExisting($fachbereich_data); ?>
        <? if ($fachbereich->faecher) : ?>
            <tbody class="<?= isset($fachbereich_id) && $fachbereich_id === $fachbereich->id ? 'not-collapsed' : 'collapsed' ?>">
                <tr class="header-row">
                    <td class="toggle-indicator">
                        <a class="mvv-load-in-new-row"
                           href="<?= $controller->action_link('details_fachbereich/' . $fachbereich->id) ?>"><?= htmlReady($fachbereich->getDisplayName()) ?></a>
                    </td>
                    <td style="text-align: center;" class="dont-hide"><?= $fachbereich->faecher->count() ?> </td>
                </tr>
                <? if (isset($fachbereich_id) && $fachbereich_id === $fachbereich->id): ?>
                    <tr class="loaded-details nohover">
                        <?= $this->render_partial('fachabschluss/faecher/details_fachbereich', compact('fachbereich')) ?>
                    </tr>
                <? endif; ?>
            </tbody>
        <? endif; ?>
    <? endforeach; ?>
</table>

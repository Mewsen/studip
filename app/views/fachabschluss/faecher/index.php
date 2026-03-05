<?php
/**
 * @var int $count
 * @var MVVController $controller
 * @var Fach[] $faecher
 * @var string $fach_id
 * @var int $page
 */
?>
<form method="post">
    <?= CSRFProtection::tokenTag(); ?>
    <table class="default collapsable">
        <caption>
            <?= _('Fächer mit verwendeten Abschlüssen') ?>
            <span class="actions"><? printf(ngettext('%s Fach', '%s Fächer', $count), $count) ?></span>
        </caption>
        <thead>
            <tr class="sortable">
                <?= $controller->renderSortLink('/index', _('Fach'), 'name') ?>
                <?= $controller->renderSortLink('/index', _('Abschlüsse'), 'count_abschluesse', ['style' => 'width: 10%; text-align: center;']) ?>
                <th style="width: 5%; text-align: right;"><?= _('Aktionen') ?></th>
            </tr>
        </thead>
        <? foreach ($faecher as $fach_data) : ?>
            <? $fach = Fach::buildExisting($fach_data); ?>
            <tbody class="<?= $fach->abschluesse->count() ? '' : 'empty' ?>  <?= ($fach_id === $fach->id ? 'not-collapsed' : 'collapsed') ?>">
                <tr class="header-row">
                    <td class="toggle-indicator">
                        <? if ($fach->abschluesse->count()) : ?>
                            <a class="mvv-load-in-new-row"
                               href="<?= $controller->action_link('details/' . $fach->id) ?>"><?= htmlReady($fach->name) ?></a>
                        <? else: ?>
                            <?= htmlReady($fach->name) ?>
                        <? endif; ?>
                    </td>
                    <td class="dont-hide" style="text-align: center;"><?= $fach->abschluesse->count() ?> </td>
                    <td class="dont-hide actions" style="white-space: nowrap;">
                        <? if (MvvPerm::havePermWrite($fach)) : ?>
                            <a href="<?= $controller->action_link('fach/' . $fach->id) ?>">
                                <?= Icon::create('edit', Icon::ROLE_CLICKABLE, ['title' => _('Fach bearbeiten')])->asSvg(); ?>
                            </a>
                        <? endif; ?>
                        <? if (MvvPerm::havePermCreate($fach)) : ?>
                            <? if ($fach->abschluesse->count() == 0): ?>
                                <?= Icon::create('trash', Icon::ROLE_CLICKABLE, tooltip2(_('Fach löschen')))->asInput(
                                    [
                                        'formaction'   => $controller->action_url('delete/' . $fach->id),
                                        'data-confirm' => sprintf(_('Wollen Sie wirklich das Fach "%s" löschen?'), $fach->name),
                                        'name'         => 'delete'
                                    ]); ?>
                            <? else : ?>
                                <?= Icon::create('trash', Icon::ROLE_INACTIVE, tooltip2(_('Fach kann nicht gelöscht werden')))->asSvg(); ?>
                            <? endif; ?>
                        <? endif; ?>
                    </td>
                </tr>
                <? if ($fach_id === $fach->id) : ?>
                    <tr class="loaded-details nohover">
                        <?= $this->render_partial('fachabschluss/faecher/details', compact('fach')) ?>
                    </tr>
                <? endif; ?>
            </tbody>
        <? endforeach ?>
        <? if ($count > MVVController::$items_per_page) : ?>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;">
                        <?
                        $pagination = $GLOBALS['template_factory']->open('shared/pagechooser');
                        $pagination->clear_attributes();
                        $pagination->set_attribute('perPage', MVVController::$items_per_page);
                        $pagination->set_attribute('num_postings', $count);
                        $pagination->set_attribute('page', $page);
                        $page_link = explode('?', $controller->action_url('index'))[0] . '?page_faecher=%s';
                        $pagination->set_attribute('pagelink', $page_link);
                        echo $pagination->render();
                        ?>
                    </td>
                </tr>
            </tfoot>
        <? endif; ?>
    </table>
</form>

<?php
/**
 * @var Vips_SolutionsController $controller
 * @var string $sort
 * @var bool $desc
 * @var VipsBlock[] $blocks
 * @var bool $use_weighting
 * @var float $sum_max_points
 */
?>
<form class="default" action="<?= $controller->link_for('vips/admin/store_weight') ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <button hidden name="store_weight"></button>

    <table class="default collapsable">
        <caption>
            <?= _('Aufgabenblätter') ?>
        </caption>

        <thead>
            <tr class="sortable">
                <th style="width: 20px;">
                    <input type="checkbox" data-proxyfor=".batch_select" data-activates=".batch_action" aria-label="<?= _('Alle Aufgabenblätter auswählen') ?>">
                </th>
                <th style="width: 40%;" class="<?= $controller->sort_class($sort === 'title', $desc) ?>">
                    <a href="<?= $controller->assignments(['sort' => 'title', 'desc' => $sort === 'title' && !$desc]) ?>">
                        <?= _('Titel') ?>
                    </a>
                </th>
                <th style="width: 15%;" class="<?= $controller->sort_class($sort === 'start', $desc) ?>">
                    <a href="<?= $controller->assignments(['sort' => 'start', 'desc' => $sort === 'start' && !$desc]) ?>">
                        <?= _('Start') ?>
                    </a>
                </th>
                <th style="width: 15%;" class="<?= $controller->sort_class($sort === 'end', $desc) ?>">
                    <a href="<?= $controller->assignments(['sort' => 'end', 'desc' => $sort === 'end' && !$desc]) ?>">
                        <?= _('Ende') ?>
                    </a>
                </th>
                <th style="width: 5%; text-align: center;">
                    <?= _('Korrigiert') ?>
                </th>
                <th style="width: 5%; text-align: center;">
                    <?= _('Freigabe') ?>
                </th>
                <th style="width: 5%; text-align: right;">
                    <?= _('Punkte') ?>
                </th>
                <th style="width: 10%; text-align: right;">
                    <?= _('Gewichtung') ?>
                </th>
                <th class="actions">
                    <?= _('Aktionen') ?>
                </th>
            </tr>
        </thead>

        <? foreach ($blocks as $block) :?>
            <? if (isset($block_assignments[$block->id]) || $block->weight !== null): ?>
                <tbody>
                    <? if (count($blocks) > 1): ?>
                        <tr class="header-row">
                            <th class="toggle-indicator" colspan="7">
                                <a class="toggler" href="#">
                                    <?= htmlReady($block->name) ?>
                                    <? if (!$block->visible): ?>
                                        <?= _('(für Teilnehmende unsichtbar)') ?>
                                    <? elseif ($block->group_id): ?>
                                        <?= sprintf(_('(sichtbar für Gruppe „%s“)'), htmlReady($block->group->name)) ?>
                                    <? elseif ($block->id): ?>
                                        <?= _('(für alle sichtbar)') ?>
                                    <? endif ?>
                                </a>
                            </th>
                            <th class="dont-hide" style="text-align: right;">
                                <? if ($block->weight !== null): ?>
                                    <input type="text" class="percent_input" name="block_weight[<?= $block->id ?>]"
                                        value="<?= $use_weighting ? sprintf('%g', $block->weight) : '' ?>"> %
                                <? endif ?>
                            </th>
                            <th class="actions">
                            </th>
                        </tr>
                    <? endif ?>

                    <? if (isset($block_assignments[$block->id])): ?>
                        <? foreach ($block_assignments[$block->id] as $ass): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="batch_select" name="assignment_ids[]" value="<?= $ass['assignment']->id ?>"
                                        aria-label="<?= _('Zeile auswählen') ?>">
                                </td>
                                <td>
                                    <a href="<?= $controller->assignment_solutions(['assignment_id' => $ass['assignment']->id]) ?>">
                                        <?= $ass['assignment']->getTypeIcon() ?>
                                        <?= htmlReady($ass['assignment']->test->title) ?>
                                    </a>
                                </td>
                                <td>
                                    <?= date('d.m.Y, H:i', $ass['assignment']->start) ?>
                                </td>
                                <td>
                                    <? if (!$ass['assignment']->isUnlimited()): ?>
                                        <?= date('d.m.Y, H:i', $ass['assignment']->end) ?>
                                    <? endif ?>
                                </td>

                                <td style="text-align: center;">
                                    <? if (!isset($ass['uncorrected_solutions'])): ?>
                                        &ndash;
                                    <? elseif ($ass['uncorrected_solutions'] == 0): ?>
                                        <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asSvg(['title' => _('ja')]) ?>
                                    <? else : ?>
                                        <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asSvg(['title' => _('nein')]) ?>
                                    <? endif ?>
                                </td>

                                <td style="text-align: center;">
                                    <? if ($ass['released'] == VipsAssignment::RELEASE_STATUS_POINTS): ?>
                                        <?= _('Punkte') ?>
                                    <? elseif ($ass['released'] == VipsAssignment::RELEASE_STATUS_COMMENTS): ?>
                                        <?= _('Kommentare') ?>
                                    <? elseif ($ass['released'] == VipsAssignment::RELEASE_STATUS_CORRECTIONS): ?>
                                        <?= _('Korrektur') ?>
                                    <? elseif ($ass['released'] == VipsAssignment::RELEASE_STATUS_SAMPLE_SOLUTIONS): ?>
                                        <?= _('Lösungen') ?>
                                    <? else : ?>
                                        &ndash;
                                    <? endif ?>
                                </td>
                                <td style="text-align: right;">
                                    <?= sprintf('%g', $ass['max_points']) ?>
                                </td>
                                <td style="text-align: right;">
                                    <? if ($ass['assignment']->type !== 'selftest' && $block->weight === null): ?>
                                        <input type="text" class="percent_input" name="assignment_weight[<?= $ass['assignment']->id ?>]"
                                            value="<?= $use_weighting ? sprintf('%g', $ass['assignment']->weight) : '' ?>"> %
                                    <? endif ?>
                                </td>
                                <td class="actions">
                                    <? $menu = ActionMenu::get() ?>
                                    <? $menu->addLink(
                                        $controller->url_for('vips/solutions/update_released_dialog', ['assignment_ids[]' => $ass['assignment']->id]),
                                        _('Freigabe ändern'),
                                        Icon::create('lock-locked'),
                                        ['data-dialog' => 'size=auto']
                                    ) ?>
                                    <? $menu->addLink(
                                        $controller->url_for('vips/sheets/edit_assignment', ['assignment_id' => $ass['assignment']->id]),
                                        _('Aufgabenblatt bearbeiten'),
                                        Icon::create('edit')
                                    ) ?>
                                    <? $menu->addLink(
                                        $controller->url_for('vips/sheets/print_assignments', ['assignment_id' => $ass['assignment']->id]),
                                        _('Aufgabenblatt drucken'),
                                        Icon::create('print'),
                                        ['target' => '_blank']
                                    ) ?>
                                    <?= $menu->render() ?>
                                </td>
                            </tr>
                        <? endforeach ?>
                    <? endif ?>
                </tbody>
            <? endif ?>
        <? endforeach ?>

        <tfoot>
            <tr>
                <td colspan="6">
                    <?= Studip\Button::create(_('Freigabe ändern'), 'change_released', [
                        'class' => 'batch_action',
                        'formaction' => $controller->update_released_dialogURL(),
                        'data-dialog' => 'size=auto'
                    ]) ?>
                </td>
                <td style="padding-right: 5px; text-align: right;">
                    <?= sprintf('%g', $sum_max_points) ?>
                </td>
                <td colspan="2" style="text-align: center;">
                    <?= Studip\Button::create(_('Speichern'), 'store_weight') ?>
                </td>
            </tr>
        </tfoot>
    </table>
</form>

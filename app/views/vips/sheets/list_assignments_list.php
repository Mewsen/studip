<?php
/**
 * @var Vips_SheetsController $controller
 * @var VipsBlock $block
 * @var string $title
 * @var string $sort
 * @var bool $desc
 * @var int $i
 * @var VipsGroup $group
 * @var VipsAssignment[] $assignments
 * @var VipsBlock[] $blocks
 */
?>
<form action="" method="POST">
    <?= CSRFProtection::tokenTag() ?>

    <table class="default">
        <caption>
            <?= htmlReady($title) ?>

            <? if (isset($block->id)): ?>
                <? if (!$block->visible): ?>
                    <?= _('(für Teilnehmende unsichtbar)') ?>
                <? elseif ($block->group_id): ?>
                    <?= sprintf(_('(sichtbar für Gruppe „%s“)'), htmlReady($block->group->name)) ?>
                <? else: ?>
                    <?= _('(für alle sichtbar)') ?>
                <? endif ?>

                <div class="actions">
                    <? $menu = ActionMenu::get() ?>
                    <? $menu->addLink(
                        $controller->url_for('vips/admin/edit_block', ['block_id' => $block->id]),
                        _('Block bearbeiten'),
                        Icon::create('edit'),
                        ['data-dialog' => 'size=auto']
                    ) ?>
                    <? $menu->addButton(
                        'delete',
                        _('Block löschen'),
                        Icon::create('trash'),
                        [
                           'formaction' => $controller->url_for('vips/admin/delete_block', ['block_id' => $block->id]),
                           'data-confirm' => sprintf(_('Wollen Sie wirklich den Block „%s“ löschen?'), $title)
                       ]
                    ) ?>
                    <?= $menu->render() ?>
                </div>
            <? endif ?>
        </caption>

        <thead>
            <tr class="sortable">
                <th style="width: 20px;">
                    <input type="checkbox" data-proxyfor=".batch_select_<?= $i ?>" data-activates=".batch_action_<?= $i ?>" aria-label="<?= _('Alle Aufgabenblätter auswählen') ?>">
                </th>
                <th style="width: 40%;" class="<?= $controller->sort_class($sort === 'title', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/sheets', ['sort' => 'title', 'desc' => $sort === 'title' && !$desc]) ?>">
                        <?= _('Titel') ?>
                    </a>
                </th>
                <th style="width: 15%;" class="<?= $controller->sort_class($sort === 'start', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/sheets', ['sort' => 'start', 'desc' => $sort === 'start' && !$desc]) ?>">
                        <?= _('Start') ?>
                    </a>
                </th>
                <th style="width: 15%;" class="<?= $controller->sort_class($sort === 'end', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/sheets', ['sort' => 'end', 'desc' => $sort === 'end' && !$desc]) ?>">
                        <?= _('Ende') ?>
                    </a>
                </th>
                <th style="width: 10%;" class="<?= $controller->sort_class($sort === 'type', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/sheets', ['sort' => 'type', 'desc' => $sort === 'type' && !$desc]) ?>">
                        <?= _('Modus') ?>
                    </a>
                </th>
                <th style="width: 10%;">
                    <? if ($group == 1): ?>
                        <?= _('Status') ?>
                    <? else: ?>
                        <?= _('Block') ?>
                    <? endif ?>
                </th>
                <th class="actions">
                    <?= _('Aktionen') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($assignments as $assignment) : ?>
                <tr>
                    <? $halted = $assignment->isRunning() && !$assignment->active ?>
                    <? $style = $halted ? 'color: red;' : '' ?>
                    <td>
                        <input class="batch_select_<?= $i ?>" type="checkbox" name="assignment_ids[]" value="<?= $assignment->id ?>" aria-label="<?= _('Zeile auswählen') ?>">
                    </td>
                    <td style="<?= $style ?>">
                        <a href="<?= $controller->link_for('vips/sheets/edit_assignment', ['assignment_id' => $assignment->id]) ?>">
                            <?= $assignment->getTypeIcon() ?>
                            <?= htmlReady($assignment->test->title) ?>
                        </a>
                        <? if ($halted): ?>
                            (<?= _('unterbrochen') ?>)
                        <? endif ?>
                    </td>
                    <td>
                        <?= date('d.m.Y, H:i', $assignment->start) ?>
                    </td>
                    <td>
                        <? if (!$assignment->isUnlimited()) : ?>
                            <?= date('d.m.Y, H:i', $assignment->end) ?>
                        <? endif ?>
                    </td>
                    <td>
                        <?= htmlReady($assignment->getTypeName()) ?>
                    </td>
                    <td>
                        <? if ($group == 1): ?>
                            <? if ($assignment->isFinished()): ?>
                                <?= _('beendet') ?>
                            <? elseif ($assignment->isRunning()): ?>
                                <?= _('aktiv') ?>
                            <? endif ?>
                        <? elseif ($assignment->block_id): ?>
                            <?= htmlReady($assignment->block->name) ?>
                        <? endif ?>
                    </td>
                    <td class="actions">
                        <? $menu = ActionMenu::get() ?>
                        <? if ($assignment->isRunning()): ?>
                            <? if (!$assignment->active): ?>
                                <? $menu->addButton('go', _('Bearbeitung fortsetzen'), Icon::create('play'), [
                                       'formaction' => $controller->url_for('vips/sheets/stopgo_assignment', ['assignment_id' => $assignment->id])
                                   ]) ?>
                            <? else : ?>
                                <? $menu->addButton('stop', _('Bearbeitung anhalten'), Icon::create('pause'), [
                                       'formaction' => $controller->url_for('vips/sheets/stopgo_assignment', ['assignment_id' => $assignment->id])
                                   ]) ?>
                            <? endif ?>
                        <? elseif (!$assignment->isFinished()) : ?>
                            <? $menu->addLink($controller->url_for('vips/sheets/start_assignment_dialog', ['assignment_id' => $assignment->id]),
                                   _('Aufgabenblatt starten'), Icon::create('play'), ['data-dialog' => 'size=auto']
                            ) ?>
                        <? endif ?>

                        <? $menu->addLink($controller->url_for('vips/sheets/show_assignment', ['assignment_id' => $assignment->id]),
                               _('Studierendensicht anzeigen'), Icon::create('community')
                           ) ?>
                        <? $menu->addLink($controller->url_for('vips/solutions/assignment_solutions', ['assignment_id' => $assignment->id]),
                               _('Aufgaben korrigieren'), Icon::create('accept')
                           ) ?>
                        <? $menu->addLink($controller->url_for('vips/sheets/print_assignments', ['assignment_id' => $assignment->id]),
                               _('Aufgabenblatt drucken'), Icon::create('print'), ['target' => '_blank']
                           ) ?>
                        <? $menu->addButton('copy', _('Aufgabenblatt duplizieren'), Icon::create('copy'), [
                               'formaction' => $controller->url_for('vips/sheets/copy_assignment', ['assignment_id' => $assignment->id])
                           ]) ?>
                        <? if ($assignment->isLocked()): ?>
                            <? $menu->addButton('reset', _('Alle Lösungen zurücksetzen'), Icon::create('refresh'), [
                                   'formaction'    => $controller->url_for('vips/sheets/reset_assignment', ['assignment_id' => $assignment->id]),
                                   'data-confirm'  => _('Achtung: Wenn Sie die Lösungen zurücksetzen, werden die Lösungen aller Teilnehmenden archiviert!')
                               ]) ?>
                        <? else: ?>
                            <? $menu->addButton('delete', _('Aufgabenblatt löschen'), Icon::create('trash'), [
                                   'formaction'    => $controller->url_for('vips/sheets/delete_assignment', ['assignment_id' => $assignment->id]),
                                   'data-confirm'  => sprintf(_('Wollen Sie wirklich das Aufgabenblatt „%s“ löschen?'), $assignment->test->title)
                               ]) ?>
                        <? endif ?>
                        <?= $menu->render() ?>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>

        <? if (count($assignments)): ?>
            <tfoot>
                <tr>
                    <td colspan="7">
                        <? if (count($blocks) > 1): ?>
                            <?= Studip\Button::create(_('Block zuweisen'), 'assign_block', [
                                    'class' => 'batch_action_' . $i,
                                    'formaction' => $controller->url_for('vips/sheets/assign_block_dialog'),
                                    'data-dialog' => 'size=auto'
                                ]) ?>
                        <? endif ?>
                        <?= Studip\Button::create(_('Kopieren'), 'copy_assignments', [
                                'class' => 'batch_action_' . $i,
                                'formaction' => $controller->url_for('vips/sheets/copy_assignments_dialog'),
                                'data-dialog' => 'size=auto'
                            ]) ?>
                        <?= Studip\Button::create(_('Verschieben'), 'move_assignments', [
                                'class' => 'batch_action_' . $i,
                                'formaction' => $controller->url_for('vips/sheets/move_assignments_dialog'),
                                'data-dialog' => 'size=auto'
                            ]) ?>
                        <?= Studip\Button::create(_('Löschen'), 'delete_assignments', [
                                'class' => 'batch_action_' . $i,
                                'formaction' => $controller->url_for('vips/sheets/delete_assignments'),
                                'data-confirm' => _('Wollen Sie wirklich die ausgewählten Aufgabenblätter löschen?')
                            ]) ?>
                    </td>
                </tr>
            </tfoot>
        <? endif ?>
    </table>
</form>

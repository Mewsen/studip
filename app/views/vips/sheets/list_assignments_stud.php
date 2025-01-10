<?php
/**
 * @var VipsBlock[] $blocks
 * @var Vips_SheetsController $controller
 * @var string $sort
 * @var bool $desc
 * @var string $user_id
 */
?>

<? if (count($blocks) == 0): ?>
    <?= MessageBox::info(_('Es gibt aktuell keine laufenden Aufgabenblätter.')) ?>
<? endif ?>

<? foreach ($blocks as $block_id => $block): ?>
    <table class="default">
        <caption>
            <? if (count($blocks) > 1 || $block_id): ?>
                <?= htmlReady($block['title']) ?>
            <? else: ?>
                <?= _('Laufende Aufgabenblätter') ?>
            <? endif ?>
        </caption>

        <thead>
            <tr class="sortable">
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
                <th style="width: 15%;">
                    <?= _('Status') ?>
                </th>
                <th class="actions">
                    <?= _('Aktion') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($block['assignments'] as $assignment) : ?>
                <tr>
                    <td>
                        <a href="<?= $controller->link_for('vips/sheets/show_assignment', ['assignment_id' => $assignment->id]) ?>">
                            <?= $assignment->getTypeIcon() ?>
                            <?= htmlReady($assignment->test->title) ?>
                        </a>
                        <? if (!$assignment->active): ?>
                            <span style="color: red;">
                                (<?= _('unterbrochen') ?>)
                            </span>
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
                        <? if ($assignment->type === 'exam'): ?>
                            <? $assignment_attempt = $assignment->getAssignmentAttempt($user_id) ?>
                            <? if ($assignment_attempt === null): ?>
                                &ndash;
                            <? elseif ($assignment_attempt->end < time()): ?>
                                <?= _('beendet') ?>
                            <? else: ?>
                                <?= _('angefangen') ?>
                            <? endif ?>
                        <? elseif ($assignment->isFinished($user_id)): ?>
                            <?= _('beendet') ?>
                        <? else: ?>
                            <? $num_solutions = $assignment->countSolutions($user_id) ?>
                            <? if ($num_solutions == 0): ?>
                                &ndash;
                            <? elseif ($num_solutions == count($assignment->test->exercise_refs)): ?>
                                <?= _('bearbeitet') ?>
                            <? else: ?>
                                <?= _('angefangen') ?>
                            <? endif ?>
                        <? endif ?>
                    </td>
                    <td class="actions">
                        <? if ($assignment->active && $assignment->type !== 'exam'): ?>
                            <? $menu = ActionMenu::get() ?>
                            <? $menu->addLink($controller->url_for('vips/sheets/print_assignments', ['assignment_id' => $assignment->id]),
                                   _('Aufgabenblatt drucken'), Icon::create('print'), ['target' => '_blank']
                               ) ?>
                            <?= $menu->render() ?>
                        <? endif ?>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>
    </table>
<? endforeach ?>

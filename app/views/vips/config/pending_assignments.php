<?php
/**
 * @var Vips_ConfigController $controller
 * @var VipsAssignment[] $assignments
 */
?>
<? if (count($assignments) === 0): ?>
    <?= MessageBox::info(_('Es gibt zur Zeit keine anstehenden Klausuren.')) ?>
<? else: ?>
    <table class="default sortable-table" data-sortlist="[[1,0]]">
        <caption>
            <?= _('Klausuren') ?>
            <div class="actions">
                <?= sprintf(ngettext('%d Klausur', '%d Klausuren', count($assignments)), count($assignments)) ?>
            </div>
        </caption>

        <thead>
            <tr class="sortable">
                <th data-sort="text" style="width: 35%;">
                    <?= _('Titel') ?>
                </th>

                <th data-sort="text" style="width: 10%;">
                    <?= _('Start') ?>
                </th>

                <th data-sort="text" style="width: 10%;">
                    <?= _('Ende') ?>
                </th>

                <th data-sort="text" style="width: 15%;">
                    <?= _('Autor/-in') ?>
                </th>

                <th data-sort="text" style="width: 30%;">
                    <?= _('Veranstaltung') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($assignments as $assignment): ?>
                <tr>
                    <td>
                        <a href="<?= $controller->link_for('vips/sheets/edit_assignment', ['cid' => $assignment->range_id, 'assignment_id' => $assignment->id]) ?>">
                            <?= $assignment->getTypeIcon() ?>
                            <?= htmlReady($assignment->test->title) ?>
                        </a>
                        <? if ($assignment->isRunning() && !$assignment->active): ?>
                            (<?= _('unterbrochen') ?>)
                        <? endif ?>
                    </td>

                    <td data-text="<?= htmlReady($assignment->start) ?>">
                        <?= date('d.m.Y, H:i', $assignment->start) ?>
                    </td>

                    <td data-text="<?= htmlReady($assignment->end) ?>">
                        <?= date('d.m.Y, H:i', $assignment->end) ?>
                    </td>

                    <td>
                        <?= htmlReady($assignment->test->user->getFullName('no_title_rev')) ?>
                    </td>

                    <td>
                        <a href="<?= URLHelper::getLink('seminar_main.php', ['cid' => $assignment->range_id]) ?>">
                            <?= htmlReady($assignment->course->name) ?>
                        </a>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>
    </table>
<? endif ?>

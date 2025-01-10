<?php
/**
 * @var Vips_PoolController $controller
 * @var string $sort
 * @var bool $desc
 * @var int $page
 * @var array $search_filter
 * @var int $count
 * @var VipsAssignment[] $assignments
 */
?>
<form action="" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="sort" value="<?= htmlReady($sort) ?>">
    <input type="hidden" name="desc" value="<?= $desc ?>">
    <input type="hidden" name="page" value="<?= $page ?>">
    <input type="hidden" name="search_filter[search_string]" value="<?= htmlReady($search_filter['search_string']) ?>">
    <input type="hidden" name="search_filter[assignment_type]" value="<?= htmlReady($search_filter['assignment_type']) ?>">

    <table class="default">
        <caption>
            <?= _('Aufgabenblätter') ?>
            <div class="actions">
                <?= sprintf(ngettext('%d Aufgabenblatt', '%d Aufgabenblätter', $count), $count) ?>
            </div>
        </caption>

        <thead>
            <tr class="sortable">
                <th style="width: 20px;">
                    <input type="checkbox" data-proxyfor=".batch_select" data-activates=".batch_action" aria-label="<?= _('Alle Aufgabenblätter auswählen') ?>">
                </th>

                <th style="width: 35%;" class="<?= $controller->sort_class($sort === 'title', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/pool/assignments', ['sort' => 'title', 'desc' => $sort === 'title' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _('Titel') ?>
                    </a>
                </th>

                <th style="width: 15%;" class="<?= $controller->sort_class($sort === 'Nachname', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/pool/assignments', ['sort' => 'Nachname', 'desc' => $sort === 'Nachname' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _('Autor/-in') ?>
                    </a>
                </th>

                <th style="width: 10%;" class="<?= $controller->sort_class($sort === 'mkdate', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/pool/assignments', ['sort' => 'mkdate', 'desc' => $sort === 'mkdate' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _('Datum') ?>
                    </a>
                </th>

                <th style="width: 20%;" class="<?= $controller->sort_class($sort === 'Name', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/pool/assignments', ['sort' => 'Name', 'desc' => $sort === 'Name' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _('Veranstaltung') ?>
                    </a>
                </th>

                <th style="width: 10%;" class="<?= $controller->sort_class($sort === 'start_time', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/pool/assignments', ['sort' => 'start_time', 'desc' => $sort === 'start_time' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _('Semester') ?>
                    </a>
                </th>

                <th class="actions">
                    <?= _('Aktionen') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($assignments as $assignment): ?>
                <? $assignment_obj = VipsAssignment::buildExisting($assignment) ?>
                <? $course_id = $assignment['range_type'] === 'course' ? $assignment['range_id'] : null ?>
                <tr>
                    <td>
                        <input class="batch_select" type="checkbox" name="assignment_ids[]" value="<?= htmlReady($assignment['id']) ?>" aria-label="<?= _('Zeile auswählen') ?>">
                    </td>

                    <td>
                        <a href="<?= $controller->link_for('vips/sheets/edit_assignment', ['cid' => $course_id, 'assignment_id' => $assignment['id']]) ?>">
                            <?= $assignment_obj->getTypeIcon() ?>
                            <?= htmlReady($assignment['test_title']) ?>
                        </a>
                    </td>

                    <td>
                        <? if (isset($assignment['Nachname']) || isset($assignment['Vorname'])): ?>
                            <?= htmlReady($assignment['Nachname'] . ', ' . $assignment['Vorname']) ?>
                        <? endif ?>
                    </td>

                    <td>
                        <?= date('d.m.Y, H:i', $assignment['mkdate']) ?>
                    </td>

                    <td>
                        <? if ($course_id): ?>
                            <a href="<?= URLHelper::getLink('seminar_main.php', ['cid' => $course_id]) ?>">
                                <?= htmlReady($assignment['Name']) ?>
                            </a>
                        <? endif ?>
                    </td>

                    <td>
                        <? if ($course_id && $assignment['start_time']): ?>
                            <?= htmlReady(Semester::findByTimestamp($assignment['start_time'])->name) ?>
                        <? endif ?>
                    </td>

                    <td class="actions">
                        <? $menu = ActionMenu::get(); ?>
                        <? $menu->addLink(
                               $controller->url_for('vips/sheets/show_assignment', ['cid' => $course_id, 'assignment_id' => $assignment['id']]),
                               _('Studierendensicht anzeigen'),
                               Icon::create('community')
                           ) ?>

                        <? $menu->addLink(
                               $controller->url_for('vips/sheets/print_assignments', ['assignment_id' => $assignment['id']]),
                               _('Aufgabenblatt drucken'),
                               Icon::create('print'),
                               ['target' => '_blank']
                           ) ?>

                        <? $menu->addLink(
                               $controller->url_for('vips/sheets/copy_assignments_dialog', ['assignment_ids[]' => $assignment['id']]),
                               _('Aufgabenblatt kopieren'),
                               Icon::create('copy'),
                               ['data-dialog' => 'size=auto']
                           ) ?>

                        <? if ($assignment_obj->isLocked()): ?>
                            <? $menu->addButton('reset', _('Alle Lösungen zurücksetzen'), Icon::create('refresh'), [
                                   'formaction' => $controller->url_for('vips/sheets/reset_assignment', ['assignment_id' => $assignment['id']]),
                                   'data-confirm'  => _('Achtung: Wenn Sie die Lösungen zurücksetzen, werden die Lösungen aller Teilnehmenden archiviert!')
                               ]) ?>
                        <? else: ?>
                            <? $menu->addButton('delete', _('Aufgabenblatt löschen'), Icon::create('trash'), [
                                   'formaction' => $controller->url_for('vips/sheets/delete_assignments', ['assignment_ids[]' => $assignment['id']]),
                                   'data-confirm' => sprintf(_('Wollen Sie wirklich das Aufgabenblatt „%s“ löschen?'), $assignment['test_title'])
                               ]) ?>
                        <? endif ?>
                        <?= $menu->render() ?>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4">
                    <?= Studip\Button::create(_('Kopieren'), 'copy_selected', [
                            'class' => 'batch_action',
                            'data-dialog' => 'size=auto',
                            'formaction' => $controller->url_for('vips/sheets/copy_assignments_dialog')
                        ]) ?>
                    <?= Studip\Button::create(_('Verschieben'), 'move_selected', [
                            'class' => 'batch_action',
                            'data-dialog' => 'size=auto',
                            'formaction' => $controller->url_for('vips/sheets/move_assignments_dialog')
                        ]) ?>
                    <?= Studip\Button::create(_('Löschen'), 'delete_selected', [
                            'class' => 'batch_action',
                            'formaction' => $controller->url_for('vips/sheets/delete_assignments'),
                            'data-confirm' => _('Wollen Sie wirklich die ausgewählten Aufgabenblätter löschen?')
                        ]) ?>
                </td>
                <td colspan="3" class="actions">
                    <?= $controller->page_chooser($controller->url_for('vips/pool/assignments', ['page' => '%d', 'sort' => $sort, 'desc' => $desc, 'search_filter' => $search_filter]), $count, $page) ?>
                </td>
            </tr>
        </tfoot>
    </table>
</form>

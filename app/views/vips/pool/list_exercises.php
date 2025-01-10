<?php
/**
 * @var Vips_PoolController $controller
 * @var string $sort
 * @var bool $desc
 * @var int $page
 * @var array $search_filter
 * @var int $count
 * @var Exercise[] $exercises
 */
?>
<form action="" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="sort" value="<?= $sort ?>">
    <input type="hidden" name="desc" value="<?= $desc ?>">
    <input type="hidden" name="page" value="<?= $page ?>">
    <input type="hidden" name="search_filter[search_string]" value="<?= htmlReady($search_filter['search_string']) ?>">
    <input type="hidden" name="search_filter[exercise_type]" value="<?= htmlReady($search_filter['exercise_type']) ?>">

    <table class="default">
        <caption>
            <?= _('Aufgaben') ?>
            <div class="actions">
                <?= sprintf(ngettext('%d Aufgabe', '%d Aufgaben', $count), $count) ?>
            </div>
        </caption>

        <thead>
            <tr class="sortable">
                <th style="width: 20px;">
                    <input type="checkbox" data-proxyfor=".batch_select" data-activates=".batch_action" aria-label="<?= _('Alle Aufgaben auswählen') ?>">
                </th>

                <th style="width: 35%;" class="<?= $controller->sort_class($sort === 'title', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/pool/exercises', ['sort' => 'title', 'desc' => $sort === 'title' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _('Titel') ?>
                    </a>
                </th>

                <th style="width: 10%;" class="<?= $controller->sort_class($sort === 'type', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/pool/exercises', ['sort' => 'type', 'desc' => $sort === 'type' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _('Aufgabentyp') ?>
                    </a>
                </th>

                <th style="width: 15%;" class="<?= $controller->sort_class($sort === 'Nachname', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/pool/exercises', ['sort' => 'Nachname', 'desc' => $sort === 'Nachname' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _('Autor/-in') ?>
                    </a>
                </th>

                <th style="width: 10%;" class="<?= $controller->sort_class($sort === 'mkdate', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/pool/exercises', ['sort' => 'mkdate', 'desc' => $sort === 'mkdate' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _('Datum') ?>
                    </a>
                </th>

                <th style="width: 20%;" class="<?= $controller->sort_class($sort === 'test_title', $desc) ?>">
                    <a href="<?= $controller->link_for('vips/pool/exercises', ['sort' => 'test_title', 'desc' => $sort === 'test_title' && !$desc, 'search_filter' => $search_filter]) ?>">
                        <?= _('Aufgabenblatt') ?>
                    </a>
                </th>

                <th class="actions">
                    <?= _('Aktionen') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($exercises as $exercise): ?>
                <? $course_id = $exercise['range_type'] === 'course' ? $exercise['range_id'] : null ?>
                <tr>
                    <td>
                        <input class="batch_select" type="checkbox" name="exercise_ids[<?= $exercise['id'] ?>]" value="<?= $exercise['assignment_id'] ?>" aria-label="<?= _('Zeile auswählen') ?>">
                    </td>

                    <td>
                        <a href="<?= $controller->link_for('vips/sheets/edit_exercise', ['cid' => $course_id, 'assignment_id' => $exercise['assignment_id'], 'exercise_id' => $exercise['id']]) ?>">
                            <?= htmlReady($exercise['title']) ?>
                        </a>
                    </td>

                    <td>
                        <? if (isset($exercise_types[$exercise['type']])): ?>
                            <?= htmlReady($exercise_types[$exercise['type']]['name']) ?>
                        <? endif ?>
                    </td>

                    <td>
                        <? if (isset($exercise['Nachname']) || isset($exercise['Vorname'])): ?>
                            <?= htmlReady($exercise['Nachname'] . ', ' . $exercise['Vorname']) ?>
                        <? endif ?>
                    </td>

                    <td>
                        <?= date('d.m.Y, H:i', $exercise['mkdate']) ?>
                    </td>

                    <td>
                        <a href="<?= $controller->link_for('vips/sheets/edit_assignment', ['cid' => $course_id, 'assignment_id' => $exercise['assignment_id']]) ?>">
                            <?= htmlReady($exercise['test_title']) ?>
                        </a>
                    </td>

                    <td class="actions">
                        <? $menu = ActionMenu::get() ?>
                        <? $menu->addLink($controller->url_for('vips/sheets/show_exercise', ['cid' => $course_id, 'assignment_id' => $exercise['assignment_id'], 'exercise_id' => $exercise['id']]),
                               _('Studierendensicht anzeigen'), Icon::create('community')
                           ) ?>

                        <? $menu->addLink($controller->url_for('vips/pool/copy_exercises_dialog', ["exercise_ids[{$exercise['id']}]" => $exercise['assignment_id']]),
                               _('Aufgabe kopieren'), Icon::create('copy'), ['data-dialog' => 'size=auto']
                           ) ?>

                        <? $menu->addButton('delete', _('Aufgabe löschen'), Icon::create('trash'), [
                               'formaction' => $controller->url_for('vips/pool/delete_exercises', ["exercise_ids[{$exercise['id']}]" => $exercise['assignment_id']]),
                               'data-confirm' => sprintf(_('Wollen Sie wirklich die Aufgabe „%s“ löschen?'), $exercise['title'])
                           ]) ?>
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
                            'formaction' => $controller->url_for('vips/pool/copy_exercises_dialog')
                        ]) ?>
                    <?= Studip\Button::create(_('Verschieben'), 'move_selected', [
                            'class' => 'batch_action',
                            'data-dialog' => 'size=auto',
                            'formaction' => $controller->url_for('vips/pool/move_exercises_dialog')
                        ]) ?>
                    <?= Studip\Button::create(_('Löschen'), 'delete_selected', [
                            'class' => 'batch_action',
                            'formaction' => $controller->url_for('vips/pool/delete_exercises'),
                            'data-confirm' => _('Wollen Sie wirklich die ausgewählten Aufgaben löschen?')
                        ]) ?>
                </td>
                <td colspan="3" class="actions">
                    <?= $controller->page_chooser($controller->url_for('vips/pool/exercises', ['page' => '%d', 'sort' => $sort, 'desc' => $desc, 'search_filter' => $search_filter]), $count, $page) ?>
                </td>
            </tr>
        </tfoot>
    </table>
</form>

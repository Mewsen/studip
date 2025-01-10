<?php
/**
 * @var Vips_SheetsController $controller
 * @var VipsTest $test
 * @var Exercise[] $exercises
 * @var int $assignment_id
 * @var bool $locked
 */
?>
<? foreach ($test->exercise_refs as $i => $exercise_ref): ?>
    <? $exercise = $exercises[$i] ?>

    <tr id="item_<?= $exercise->id ?>" role="listitem" tabindex="0">
        <td class="drag-handle">
            <input type="checkbox" class="batch_select" name="exercise_ids[]" value="<?= $exercise->id ?>" aria-label="<?= _('Zeile auswählen') ?>">
        </td>
        <td class="dynamic_counter" style="text-align: right;">
            <!-- position -->
        </td>
        <td>
            <!-- exercise title -->
            <a href="<?= $controller->link_for('vips/sheets/edit_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise->id]) ?>">
                <?= htmlReady($exercise->title) ?>
            </a>
        </td>
        <td>
            <!-- exercise type -->
            <?= htmlReady($exercise->getTypeName()) ?>
        </td>
        <td>
            <!-- max points -->
            <input name="exercise_points[<?= $exercise->id ?>]" type="text" class="points" value="<?= sprintf('%g', $exercise_ref->points) ?>" data-secure required>
        </td>

        <td class="actions">
            <? $menu = ActionMenu::get() ?>
            <!-- display button -->
            <? $menu->addLink(
                $controller->url_for('vips/sheets/show_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise->id]),
                _('Studierendensicht anzeigen'),
                Icon::create('community')
            ) ?>

            <? if (!$locked): ?>
                <!-- copy button -->
                <? $menu->addButton(
                    'copy',
                    _('Aufgabe duplizieren'),
                    Icon::create('copy'),
                    ['formaction' => $controller->url_for('vips/sheets/copy_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise->id])]
                ) ?>

                <!-- delete button -->
                <? $menu->addButton(
                    'delete',
                    _('Aufgabe löschen'),
                    Icon::create('trash'),
                    [
                       'formaction' => $controller->url_for('vips/sheets/delete_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $exercise->id]),
                       'data-confirm' => sprintf(_('Wollen Sie wirklich die Aufgabe „%s“ löschen?'), $exercise->title)
                   ]
                ) ?>
            <? endif ?>
            <?= $menu->render() ?>
        </td>
    </tr>
<? endforeach ?>

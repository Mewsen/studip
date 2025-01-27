<?php
/**
 * @var ClozeTask $exercise
 */
?>
<div class="label-text">
    <?= _('Anzuordnende Antworten') ?>
</div>

<div class="dynamic_list sortable_list">
    <? foreach ($exercise->task['answers'] as $answer): ?>
        <? $size = $exercise->flexibleInputSize($answer['text']); ?>

        <div class="dynamic_row mc_row sortable_item drag-handle" tabindex="0">
            <label class="dynamic_counter size_toggle size_<?= $size ?> undecorated">
                <?= $this->render_partial('exercises/flexible_input', ['name' => 'answer[]', 'value' => $answer['text'], 'size' => $size]) ?>
                <input type="hidden" name="id[]" value="<?= $answer['id'] ?>">
            </label>

            <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
        </div>
    <? endforeach ?>

    <div class="dynamic_row mc_row sortable_item drag-handle template" tabindex="0">
        <label class="dynamic_counter size_toggle size_small undecorated">
            <?= $this->render_partial('exercises/flexible_input', ['data_name' => '', 'name' => 'answer[]', 'size' => 'small']) ?>
            <input type="hidden" name="id[]">
        </label>

        <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
    </div>

    <?= Studip\Button::create(_('Antwort hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row']) ?>
</div>

<label>
    <?= _('Verfahren zur Punktevergabe') ?>

    <select name="compare">
        <option value="">
            <?= _('Punkte nur bei vollständig korrekter Lösung') ?>
        </option>
        <option value="position" <?= isset($exercise->task['compare']) && $exercise->task['compare'] === 'position' ? 'selected' : '' ?>>
            <?= _('Punkte für Antworten an den korrekten Positionen') ?>
        </option>
        <option value="sequence" <?= isset($exercise->task['compare']) && $exercise->task['compare'] === 'sequence' ? 'selected' : '' ?>>
            <?= _('Punkte für Paare von Antworten in korrekter Reihenfolge') ?>
        </option>
    </select>
</label>

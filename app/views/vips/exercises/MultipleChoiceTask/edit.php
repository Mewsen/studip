<?php
/**
 * @var ClozeTask $exercise
 */
?>
<div class="label-text">
    <?= _('Antwortalternativen') ?>
</div>

<div class="dynamic_list">
    <? foreach ($exercise->task['answers'] as $i => $answer): ?>
        <? $size = $exercise->flexibleInputSize($answer['text']); ?>

        <div class="dynamic_row mc_row">
            <label class="dynamic_counter size_toggle size_<?= $size ?> undecorated">
                <?= $this->render_partial('exercises/flexible_input', ['name' => "answer[$i]", 'value' => $answer['text'], 'size' => $size]) ?>
            </label>

            <label class="undecorated" style="padding: 1ex;">
                <input type="checkbox" name="correct[<?= $i ?>]" value="1"<? if ($answer['score']): ?> checked<? endif ?>>
                <?= _('richtig') ?>
            </label>

            <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
        </div>
    <? endforeach ?>

    <div class="dynamic_row mc_row template">
        <label class="dynamic_counter size_toggle size_small undecorated">
            <?= $this->render_partial('exercises/flexible_input', ['data_name' => 'answer', 'size' => 'small']) ?>
        </label>

        <label class="undecorated" style="padding: 1ex;">
            <input type="checkbox" data-name="correct" value="1">
            <?= _('richtig') ?>
        </label>

        <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
    </div>

    <?= Studip\Button::create(_('Antwort hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row']) ?>
</div>

<div class="smaller">
    <?= _('Leere Antwortalternativen werden automatisch gelöscht.') ?>
</div>

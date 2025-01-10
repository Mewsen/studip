<?php
/**
 * @var ClozeTask $exercise
 */
?>
<label>
    <input type="checkbox" name="optional" value="1" <?= $exercise->options['optional'] ? 'checked' : '' ?>>
    <?= _('Antwortalternative „keine Antwort“ hinzufügen (ohne Bewertung)') ?>
</label>

<div class="label-text">
    <?= _('Antwortalternativen') ?>
</div>

<div class="dynamic_list">
    <? foreach ($exercise->task as $j => $task): ?>
        <div class="dynamic_list dynamic_row" style="border-bottom: 1px dotted grey;">
            <label class="hide_first">
                <?= _('Zwischentext') ?>
                <textarea name="description[<?= $j ?>]" class="character_input size-l wysiwyg"><?= isset($task['description']) ? wysiwygReady($task['description']) : '' ?></textarea>
            </label>

            <? foreach ($task['answers'] as $i => $answer): ?>
                <? $size = $exercise->flexibleInputSize($answer['text']); ?>

                <div class="dynamic_row mc_row">
                    <label class="dynamic_counter size_toggle size_<?= $size ?> undecorated">
                        <?= $this->render_partial('exercises/flexible_input', ['name' => "answer[$j][$i]", 'value' => $answer['text'], 'size' => $size]) ?>
                    </label>

                    <label class="undecorated" style="padding: 1ex;">
                        <input type="radio" name="correct[<?= $j ?>]" value="<?= $i ?>"<? if ($answer['score'] == 1): ?> checked<? endif ?>>
                        <?= _('richtig') ?>
                    </label>

                    <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
                </div>
            <? endforeach ?>

            <div class="dynamic_row mc_row template">
                <label class="dynamic_counter size_toggle size_small undecorated">
                    <?= $this->render_partial('exercises/flexible_input', ['data_name' => "answer[$j]", 'size' => 'small']) ?>
                </label>

                <label class="undecorated" style="padding: 1ex;">
                    <input type="radio" name="correct[<?= $j ?>]" data-value>
                    <?= _('richtig') ?>
                </label>

                <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
            </div>

            <?= Studip\Button::create(_('Antwort hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row']) ?>
            <?= Studip\Button::create(_('Antwortblock löschen'), 'del_group', ['class' => 'delete_dynamic_row']) ?>
        </div>
    <? endforeach ?>

    <div class="dynamic_list dynamic_row template" style="border-bottom: 1px dotted grey;">
        <label class="hide_first">
            <?= _('Zwischentext') ?>
            <textarea data-name="description" class="character_input size-l wysiwyg-hidden"></textarea>
        </label>

        <div class="dynamic_row mc_row template">
            <label class="dynamic_counter size_toggle size_small undecorated">
                <?= $this->render_partial('exercises/flexible_input', ['data_name' => ':answer', 'size' => 'small']) ?>
            </label>

            <label class="undecorated" style="padding: 1ex;">
                <input type="radio" data-name="correct" data-value=":value">
                <?= _('richtig') ?>
            </label>

            <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
        </div>

        <?= Studip\Button::create(_('Antwort hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row']) ?>
        <?= Studip\Button::create(_('Antwortblock löschen'), 'del_group', ['class' => 'delete_dynamic_row']) ?>
    </div>

    <?= Studip\Button::create(_('Antwortblock hinzufügen'), 'add_group', ['class' => 'add_dynamic_row']) ?>
</div>

<div class="smaller">
    <?= _('Leere Antwortalternativen werden automatisch gelöscht.') ?>
</div>

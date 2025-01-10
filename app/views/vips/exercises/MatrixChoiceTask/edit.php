<?php
/**
 * @var ClozeTask $exercise
 */
?>
<div class="label-text">
    <?= _('Auswahlmöglichkeiten') ?>
</div>

<div class="choice_list dynamic_list mc_row">
    <? foreach ($exercise->task['choices'] as $i => $choice): ?>
        <span class="dynamic_row">
            <input type="text" class="character_input size-s" name="choice[<?= $i ?>]" value="<?= htmlReady($choice) ?>">
            <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Auswahlmöglichkeit löschen')]) ?>
            /
        </span>
    <? endforeach ?>

    <span class="dynamic_row template">
        <input type="text" class="character_input size-s" data-name="choice">
        <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Auswahlmöglichkeit löschen')]) ?>
        /
    </span>

    <?= Icon::create('add')->asInput(['class' => 'add_dynamic_row', 'title' => _('Auswahlmöglichkeit hinzufügen')]) ?>
</div>

<label>
    <input type="checkbox" name="optional" value="1" <?= $exercise->options['optional'] ? 'checked' : '' ?>>
    <?= _('Auswahlmöglichkeit „keine Antwort“ hinzufügen (ohne Bewertung)') ?>
</label>

<div class="label-text">
    <?= _('Fragen/Aussagen') ?>
</div>

<div class="dynamic_list">
    <? foreach ($exercise->task['answers'] as $i => $answer): ?>
        <? $size = $exercise->flexibleInputSize($answer['text']); ?>

        <div class="dynamic_row mc_row">
            <label class="dynamic_counter size_toggle size_<?= $size ?> undecorated">
                <?= $this->render_partial('exercises/flexible_input', ['name' => "answer[$i]", 'value' => $answer['text'], 'size' => $size]) ?>
            </label>

            <span class="choice_select dynamic_list">
                <? foreach ($exercise->task['choices'] as $val => $choice): ?>
                    <label class="dynamic_row undecorated">
                        <input type="radio" name="correct[<?= $i ?>]" value="<?= $val ?>" <? if ($answer['choice'] === $val): ?>checked<? endif ?>>
                        <span><?= htmlReady($choice) ?></span>
                    </label>
                <? endforeach ?>

                <label class="dynamic_row undecorated template">
                    <input type="radio" name="correct[<?= $i ?>]" data-value>
                    <span></span>
                </label>
            </span>

            <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Frage löschen')]) ?>
        </div>
    <? endforeach ?>

    <div class="dynamic_row mc_row template">
        <label class="dynamic_counter size_toggle size_small undecorated">
            <?= $this->render_partial('exercises/flexible_input', ['data_name' => 'answer', 'size' => 'small']) ?>
        </label>

        <span class="choice_select dynamic_list">
            <? foreach ($exercise->task['choices'] as $val => $choice): ?>
                <label class="dynamic_row undecorated">
                    <input type="radio" data-name="correct" value="<?= $val ?>">
                    <span><?= htmlReady($choice) ?></span>
                </label>
            <? endforeach ?>

            <label class="dynamic_row undecorated template">
                <input type="radio" data-name="correct" data-value=":value">
                <span></span>
            </label>
        </span>

        <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Frage löschen')]) ?>
    </div>

    <?= Studip\Button::create(_('Frage hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row']) ?>
</div>

<div class="smaller">
    <?= _('Leere Antwortalternativen werden automatisch gelöscht.') ?>
</div>

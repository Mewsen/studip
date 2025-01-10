<?php
/**
 * @var ClozeTask $exercise
 */
?>
<div class="label-text">
    <?= _('Automatisch bewertete Antworten') ?>
</div>

<div class="dynamic_list">
    <? foreach ($exercise->task['answers'] as $i => $answer): ?>
        <div class="dynamic_row mc_row">
            <label class="dynamic_counter undecorated">
                <input class="character_input" name="answer[<?= $i ?>]" type="text" value="<?= htmlReady($answer['text']) ?>">
            </label>
            <label class="undecorated" style="padding: 1ex;">
                <input type="radio" name="correct[<?= $i ?>]" value="1"<? if ($answer['score'] == 1): ?> checked<? endif ?>>
                <?= _('richtig') ?>
            </label>
            <label class="undecorated" style="padding: 1ex;">
                <input type="radio" name="correct[<?= $i ?>]" value="0.5"<? if ($answer['score'] == 0.5): ?> checked<? endif ?>>
                <?= _('teils richtig') ?>
            </label>
            <label class="undecorated" style="padding: 1ex;">
                <input type="radio" name="correct[<?= $i ?>]" value="0"<? if ($answer['score'] == 0): ?> checked<? endif ?>>
                <?= _('falsch') ?>
            </label>

            <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
        </div>
    <? endforeach ?>

    <div class="dynamic_row mc_row template">
        <label class="dynamic_counter undecorated">
            <input class="character_input" data-name="answer" type="text">
        </label>
        <label class="undecorated" style="padding: 1ex;">
            <input type="radio" data-name="correct" value="1">
            <?= _('richtig') ?>
        </label>
        <label class="undecorated" style="padding: 1ex;">
            <input type="radio" data-name="correct" value="0.5">
            <?= _('teils richtig') ?>
        </label>
        <label class="undecorated" style="padding: 1ex;">
            <input type="radio" data-name="correct" value="0" checked>
            <?= _('falsch') ?>
        </label>

        <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
    </div>

    <?= Studip\Button::create(_('Antwort hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row']) ?>
</div>

<label>
    <?= _('Art des Textvergleichs') ?>

    <select name="compare" onchange="$(this).parent().next('label').toggle($(this).val() === 'numeric')">
        <option value="">
            <?= _('Groß-/Kleinschreibung ignorieren') ?>
        </option>
        <option value="levenshtein" <?= isset($exercise->task['compare']) && $exercise->task['compare'] === 'levenshtein' ? 'selected' : '' ?>>
            <?= _('Textähnlichkeit (Levenshtein-Distanz)') ?>
        </option>
        <option value="soundex" <?= isset($exercise->task['compare']) && $exercise->task['compare'] === 'soundex' ? 'selected' : '' ?>>
            <?= _('Ähnlichkeit der Aussprache (Soundex)') ?>
        </option>
        <option value="numeric" <?= isset($exercise->task['compare']) && $exercise->task['compare'] === 'numeric' ? 'selected' : '' ?>>
            <?= _('Numerischer Wertevergleich (ggf. mit Einheit)') ?>
        </option>
    </select>
</label>

<label style="<?= isset($exercise->task['compare']) && $exercise->task['compare'] === 'numeric' ? '' : 'display: none;' ?>">
    <?= _('Erlaubte relative Abweichung vom korrekten Wert') ?>
    <br>
    <input type="text" class="size-s" style="display: inline; text-align: right;"
           name="epsilon" value="<?= isset($exercise->task['epsilon']) ? sprintf('%g', $exercise->task['epsilon'] * 100) : '0' ?>"> %
</label>

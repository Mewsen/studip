<?php
/**
 * @var ClozeTask $exercise
 */
?>
<? $tooltip = sprintf('<p>%s:<br>[[ ... ]]</p><p>%s:<br>[[ ... | ... | ... ]]</p><p>%s:<br>[[ ... | ... | *... ]]</p><p>%s:<br>[[: ... | ... | *... ]]</p>',
      _('Lücke hinzufügen'), _('Mehrere Lösungen mit | trennen'), _('Falsche Antworten mit * markieren'), _('Auswahl aus Liste statt Eingabe')) ?>

<label>
    <?= _('Lückentext') ?> <?= tooltipIcon($tooltip, false, true) ?>
    <? $cloze_text = $exercise->getClozeText() ?>
    <textarea name="cloze_text" class="character_input size-l wysiwyg" rows="<?= $exercise->textareaSize($cloze_text) ?>"><?= wysiwygReady($cloze_text) ?></textarea>
</label>

<label>
    <?= _('Antwortmodus') ?>

    <select name="layout" onchange="$(this).parent().next().toggle(this.value === '')">
        <option value="">
            <?= _('Texteingabe') ?>
        </option>
        <option value="select" <?= $exercise->interactionType() === 'select' ? 'selected' : '' ?>>
            <?= _('Antwort aus Liste auswählen') ?>
        </option>
        <option value="drag" <?= $exercise->interactionType() === 'drag' ? 'selected' : '' ?>>
            <?= _('Antwort in das Feld ziehen') ?>
        </option>
    </select>
</label>

<div style="<?= $exercise->interactionType() !== 'input' ? 'display: none;' : '' ?>">
    <label>
        <?= _('Art des Textvergleichs') ?>

        <select name="compare" onchange="$(this).parent().next('label').toggle($(this).val() === 'numeric')">
            <option value="">
                <?= _('Groß-/Kleinschreibung unterscheiden') ?>
            </option>
            <option value="ignorecase" <?= isset($exercise->task['compare']) && $exercise->task['compare'] === 'ignorecase' ? 'selected' : '' ?>>
                <?= _('Groß-/Kleinschreibung ignorieren') ?>
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

    <label>
        <input type="checkbox" <?= isset($exercise->task['input_width']) ? 'checked' : '' ?> onchange="$(this).next('select').attr('disabled', !this.checked)">
        <?= _('Feste Breite der Eingabefelder:') ?>

        <select name="input_width" style="display: inline; width: auto;" <?= isset($exercise->task['input_width']) ? '' : 'disabled' ?>>
            <? foreach ([_('kurz'), _('mittel'), _('lang'), _('maximal')] as $key => $label): ?>
                <option value="<?= $key ?>" <?= isset($exercise->task['input_width']) && $exercise->task['input_width'] == $key ? 'selected' : '' ?>>
                    <?= htmlReady($label) ?>
                </option>
            <? endforeach ?>
        </select>
    </label>
</div>

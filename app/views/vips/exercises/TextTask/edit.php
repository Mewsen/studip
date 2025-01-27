<?php
/**
 * @var ClozeTask $exercise
 */
?>
<label>
    <?= _('Art der Abgabe') ?>

    <select class="tb_layout" name="layout" onchange="$(this).closest('fieldset').find('.none-hidden').toggle($(this).val() !== 'none')">
        <option value="">
            <?= _('Texteingabe - einfacher Text ohne Formatierungen') ?>
        </option>
        <option value="markup" <? if ($exercise->getLayout() === 'markup'): ?>selected<? endif ?>>
            <?= _('Texteingabe - Textformatierungen bei Eingabe der Lösung anbieten') ?>
        </option>
        <option value="code" <? if ($exercise->getLayout() === 'code'): ?>selected<? endif ?>>
            <?= _('Texteingabe - Programmcode (nichtproportionale Schriftart)') ?>
        </option>
        <option value="none" <? if ($exercise->getLayout() === 'none'): ?>selected<? endif ?>>
            <?= _('keine Texteingabe - nur Hochladen von Dateien erlauben') ?>
        </option>
    </select>
</label>

<label class="none-hidden" style="<?= $exercise->getLayout() === 'none' ? 'display: none;' : '' ?>">
    <?= _('Vorgegebener Text im Antwortfeld') ?>
    <?= $this->render_partial('exercises/flexible_textarea',
        ['name' => 'answer_default', 'value' => $exercise->task['template'], 'monospace' => $exercise->getLayout() === 'code', 'wysiwyg' => $exercise->getLayout() === 'markup']) ?>
</label>

<label>
    <?= _('Musterlösung') ?>
    <textarea class="character_input size-l wysiwyg" name="answer_0" rows="<?= $exercise->textareaSize($exercise->task['answers'][0]['text']) ?>"><?= wysiwygReady($exercise->task['answers'][0]['text']) ?></textarea>
</label>

<div class="none-hidden" style="<?= $exercise->getLayout() === 'none' ? 'display: none;' : '' ?>">
    <label>
        <input type="checkbox" name="file_upload" value="1" <?= $exercise->options['file_upload'] ? ' checked' : '' ?>>
        <?= _('Hochladen von Dateien als Lösung erlauben') ?>
        <?= tooltipIcon(_('Hochgeladene Dateien können nicht automatisch bewertet werden.')) ?>
    </label>

    <label>
        <input type="checkbox" name="compare" value="levenshtein" <?= $exercise->task['compare'] === 'levenshtein' ? 'checked' : '' ?>>
        <?= _('Punktevorschlag basierend auf Textähnlichkeit (Levenshtein-Distanz)') ?>
    </label>
</div>

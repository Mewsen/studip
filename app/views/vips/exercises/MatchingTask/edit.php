<?php
/**
 * @var ClozeTask $exercise
 */
?>
<label>
    <input class="rh_select_type" type="checkbox" name="multiple" value="1" <?= $exercise->isMultiSelect() ? 'checked' : '' ?>>
    <?= _('Mehrfachzuordnungen zu einem vorgegebenen Text erlauben') ?>
</label>

<table class="default description <?= $exercise->isMultiSelect() ? '' : 'rh_single' ?>">
    <thead>
        <tr>
            <th style="width: 50%;">
                <?= _('Vorgegebener Text') ?>
            </th>
            <th style="width: 50%;">
                <?= _('Zuzuordnende Antworten') ?>
            </th>
        </tr>
    </thead>

    <tbody class="dynamic_list" style="vertical-align: top;">
        <? foreach ($exercise->task['groups'] as $i => $group): ?>
            <? $size = $exercise->flexibleInputSize($group) ?>

            <tr class="dynamic_row">
                <td class="size_toggle size_<?= $size ?>">
                    <?= $this->render_partial('exercises/flexible_input', ['name' => "default[$i]", 'value' => $group, 'size' => $size]) ?>

                    <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Zuordnung löschen')]) ?>
                </td>
                <td class="dynamic_list">
                    <? $j = 0 ?>
                    <? foreach ($exercise->task['answers'] as $answer): ?>
                        <? if ($answer['group'] == $i): ?>
                            <? $size = $exercise->flexibleInputSize($answer['text']) ?>

                            <div class="dynamic_row size_toggle size_<?= $size ?>">
                                <?= $this->render_partial('exercises/flexible_input', ['name' => "answer[$i][$j]", 'value' => $answer['text'], 'size' => $size]) ?>
                                <input type="hidden" name="id[<?= $i ?>][<?= $j++ ?>]" value="<?= $answer['id'] ?>">

                                <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
                            </div>
                        <? endif ?>
                    <? endforeach ?>

                    <div class="dynamic_row size_toggle size_small template">
                        <?= $this->render_partial('exercises/flexible_input', ['data_name' => "answer[$i]", 'size' => 'small']) ?>
                        <input type="hidden" data-name="id[<?= $i ?>]">

                        <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
                    </div>

                    <?= Studip\Button::create(_('Antwort hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row rh_add_answer']) ?>
                </td>
            </tr>
        <? endforeach ?>

        <tr class="dynamic_row template">
            <td class="size_toggle size_small">
                <?= $this->render_partial('exercises/flexible_input', ['data_name' => 'default', 'size' => 'small']) ?>

                <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Zuordnung löschen')]) ?>
            </td>
            <td class="dynamic_list">
                <div class="dynamic_row size_toggle size_small template">
                    <?= $this->render_partial('exercises/flexible_input', ['data_name' => ':answer', 'size' => 'small']) ?>
                    <input type="hidden" data-name=":id">

                    <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Antwort löschen')]) ?>
                </div>

                <?= Studip\Button::create(_('Antwort hinzufügen'), 'add_answer', ['class' => 'add_dynamic_row rh_add_answer']) ?>
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <?= Studip\Button::create(_('Zuordnung hinzufügen'), 'add_pairs', ['class' => 'add_dynamic_row']) ?>
            </th>
        </tr>
    </tbody>
</table>

<div class="label-text">
    <?= _('Distraktoren (optional)') ?>
    <?= tooltipIcon(_('Weitere Antworten, die keinem Text zugeordnet werden dürfen.')) ?>
</div>

<div class="dynamic_list">
    <? foreach ($exercise->task['answers'] as $answer): ?>
        <? if ($answer['group'] == -1): ?>
            <? $size = $exercise->flexibleInputSize($answer['text']) ?>

            <div class="dynamic_row mc_row">
                <label class="dynamic_counter size_toggle size_<?= $size ?> undecorated">
                    <?= $this->render_partial('exercises/flexible_input', ['name' => '_answer[]', 'value' => $answer['text'], 'size' => $size]) ?>
                    <input type="hidden" name="_id[]" value="<?= $answer['id'] ?>">
                </label>

                <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Distraktor löschen')]) ?>
            </div>
        <? endif ?>
    <? endforeach ?>

    <div class="dynamic_row mc_row template">
        <label class="dynamic_counter size_toggle size_small undecorated">
            <?= $this->render_partial('exercises/flexible_input', ['data_name' => '', 'name' => '_answer[]', 'size' => 'small']) ?>
            <input type="hidden" name="_id[]">
        </label>

        <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Distraktor löschen')]) ?>
    </div>

    <?= Studip\Button::create(_('Distraktor hinzufügen'), 'add_false_answer', ['class' => 'add_dynamic_row']) ?>
</div>

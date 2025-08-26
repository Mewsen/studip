<?php
/**
 * @var Datafield $model
 * @var DatafieldEntry $entry
 * @var string $value
 * @var string $name
 * @var bool $multiple
 * @var bool $is_assoc
 * @var array $type_param
 */
?>
<label>
    <span class="datafield_title <?= $model->is_required ? 'required' : '' ?>">
        <?= htmlReady($model->name) ?>
    </span>

    <? if ($model->description): ?>
        <?= tooltipIcon($model->description) ?>
    <? endif ?>

    <?php
    $selected = function ($needle) use ($value) {
        if (is_array($value) && !in_array($needle, $value)) {
            return '';
        }
        if (!is_array($value) && $needle != $value) {
            return '';
        }
        return ' selected';
    };
    ?>
    <select name="<?= htmlReady($name) ?>[<?= htmlReady($model->id) ?>]<? if ($multiple) echo '[]'; ?>"
            id="<?= htmlReady($name) ?>_<?= htmlReady($model->id) ?>"
            <?  if (!$entry->isEditable()) echo 'disabled'; ?>
            <? if ($multiple) echo 'multiple'; ?>
            <? if ($model->is_required) echo 'required'; ?>>
    <? if (!$model->is_required): ?>
        <option value="">
            (<?= _('keine Auswahl') ?>)
        </option>
    <? endif; ?>
    <? foreach ($type_param as $pkey => $pval): ?>
        <option value="<?= htmlReady($is_assoc ? (string) $pkey : $pval) ?>"
                <?= $selected($is_assoc ? (string)$pkey : $pval) ?>>
            <?= htmlReady($pval) ?>
        </option>
    <? endforeach; ?>
    </select>
</label>

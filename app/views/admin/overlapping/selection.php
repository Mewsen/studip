<?php
/**
 * @var Admin_OverlappingController $controller
 * @var array $fachsems
 * @var array $semtypes
 * @var StgteilVersion $base_version
 * @var StgteilVersion[] $comp_versions
 * @var StgteilVersion[] $stgteil_versions
 * @var string $base_version_id
 * @var array $comp_versions_ids
 */
?>
<form method="post" class="default collapsable mvv-ovl-selection" action="<?= $controller->checkURL() ?>">
    <fieldset>
        <legend>
            <?= _('Auswahl') ?>
        </legend>
        <label>
            <span class="required"><?= _('Studiengangteil') ?></span>
            <select class="nested-select" name="base_version" required>
                <? foreach($stgteil_versions as $stgteil_version) : ?>
                    <option
                        value="<?= $stgteil_version->id ?>"
                        <?= $stgteil_version->id === $base_version_id ? 'selected' : '' ?>
                    ><?= htmlReady($stgteil_version->getDisplayName()) ?></option>
                <? endforeach ?>
            </select>
        </label>
        <label>
            <?= _('Vergleichs-Studiengangteile') ?>
            <select class="nested-select" name="comp_versions[]" multiple>
                <? foreach ($stgteil_versions as $stgteil_version) : ?>
                    <option
                        value="<?= htmlReady($stgteil_version->id) ?>"
                        <?= in_array($stgteil_version->id, $comp_versions_ids) ? 'selected' : '' ?>
                    ><?= htmlReady($stgteil_version->getDisplayName()) ?></option>
                <? endforeach ?>
            </select>
        </label>
        <label>
            <span><?= _('Fachsemester') ?></span>
            <select class="nested-select" name="fachsems[]" multiple>
                <? foreach (range(1, 6) as $fsem) : ?>
                    <option value="<?= $fsem ?>"<?= in_array($fsem, (array) $fachsems) ? ' selected' : '' ?>>
                    <?= $fsem . ModuleManagementModel::getLocaleOrdinalNumberSuffix($fsem) . ' ' . _('Fachsemester') ?>
                    </option>
                <? endforeach; ?>
            </select>
        </label>
        <label>
            <?= _('Veranstaltungstyp-Filter') ?>
            <select id="semtype-select_" class="nested-select" name="semtypes[]" multiple>
                <? foreach ($GLOBALS['SEM_CLASS'] as $class_id => $class) : ?>
                    <? if ($class['studygroup_mode']) : continue;
                    endif; ?>
                    <optgroup class="nested-item-header" label="<?= htmlReady($class['name']) ?>">
                        <? foreach ($class->getSemTypes() as $id => $type) : ?>
                            <option class="nested-item nested-item-level-2"
                                    value="<?= $id ?>"<?= in_array($id, (array) $semtypes) ? ' selected' : '' ?>>
                                <?= htmlReady($type['name']) ?>
                            </option>
                        <? endforeach; ?>
                    </optgroup>
                <? endforeach; ?>
            </select>
        </label>
        <label>
            <input type="checkbox"
                   name="show_hidden"
                   value="1" <?= !empty($_SESSION['MVV_OVL_HIDDEN']) ? ' checked' : '' ?>>
            <?= _('ausgeblendete Veranstaltungen anzeigen') ?>
        </label>
    </fieldset>
    <footer>
        <?= \Studip\Button::createAccept(_('Vergleichen'), 'compare') ?>
        <?= \Studip\Button::createCancel(_('Zurücksetzen'), 'index', ['formaction' => $controller->resetURL()]) ?>
    </footer>
</form>

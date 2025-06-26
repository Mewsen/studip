<fieldset>
<legend>
    <?= _('Grunddaten') ?>
</legend>

<label class="">
    <span class="required"><?= _('Name') ?></span>
    <input type="text" name="name" id="wizard-name" maxlength="254" value="<?= htmlReady($values['name'] ?? '') ?>" required>
</label>

<? if(count($types) > 1) : ?>
    <label class="">
        <span class="required"><?= _('Typ') ?></span>
        <select name="coursetype" id="wizard-coursetype">
            <?php foreach ($types as $class => $subtypes) : ?>
                <optgroup label="<?= htmlReady($class) ?>">
                    <?php foreach ($subtypes as $type) : ?>
                        <option value="<?= $type['id'] ?>"<?= $type['id'] == $values['coursetype'] ? ' selected="selected"' : '' ?>>
                            <?= htmlReady($type['name']) ?>
                        </option>
                    <?php endforeach ?>
                </optgroup>
            <?php endforeach ?>
        </select>
    </label>
<? else : ?>
    <? $type = array_values($types)[0]; ?>
    <input type="hidden" name="coursetype" value="<?= htmlReady($type[0]['id']) ?>">
<? endif ?>


<label class="">
    <?= _('Beschreibung') ?>
    <textarea name="description" id="wizard-description"
              rows="4"><?= htmlReady($values['description'] ?? '') ?></textarea>
</label>


<label class="">
    <span class="required"><?= _('Zugang') ?></span>

    <select name="access" id="wizard-access">
        <option value="all"
            <? if (isset($values['access']) && $values['access'] === 'all') echo 'selected'; ?>>
            <?= _('Offen für alle') ?>
        </option>
        <option value="invite"
            <? if (isset($values['access']) && $values['access'] === 'invite') echo 'selected'; ?>>
            <?= _('Auf Anfrage') ?>
        </option>
        <?php if (Config::get()->STUDYGROUPS_INVISIBLE_ALLOWED) : ?>
            <option value="invisible"
                <? if (isset($values['access']) && $values['access'] === 'invisible') echo 'selected'; ?>>
                <?= _('Unsichtbar') ?>
            </option>
        <?php endif ?>
    </select>
</label>


<label><span class="required"><?= _('Nutzungsbedingungen')?></span></label>

<? if ($GLOBALS['perm']->have_perm('admin')) : ?>
    <p style="font-weight: bold;">
        <?= _('Ich habe die eingetragenen Personen darüber informiert, dass in Ihrem Namen eine Studiengruppe angelegt wird und versichere, dass Sie mit folgenden Nutzungsbedingungen einverstandenen sind:') ?>
    </p>
<? endif ?>
<?= formatReady(Config::Get()->STUDYGROUP_TERMS) ?>

<label>
    <input type="checkbox" name="accept" id="wizard-accept" required>
    <?= _('Einverstanden') ?>
</label>
</fieldset>

<fieldset>
    <legend>
        <?= _('Erweiterte Einstellungen') ?>
    </legend>

    <label>
        <?= _('Ablaufdatum / Löschdatum') ?>
        <input type="text" aria-label="<?= _('Ablaufdatum / Löschdatum') ?>" title="<?= _('Ablaufdatum / Löschdatum') ?>"
               data-date-picker
               name="exp_date"
               id="wizard-datepicker"
               value="<?= date('d.m.Y H:i', time() + 86400 * 365 * 2) ?>"
               class="hasDatePicker">
    </label>

    <label>
        <span id="studygroup-wizard-tags"><?= _('Schlagwörter') ?></span>
        <?= Studip\VueApp::create('Multiquicksearch')
            ->withProps([
                'name'         => 'tags[]',
                'searchtype'   => (string) SQLSearch::get('SELECT `name`, `name` FROM `tags` WHERE `active` = 1 AND `name` LIKE :input', _('Schlagwort suchen')),
                'autocomplete' => true,
                'addlabel' => _('Schlagwort hinzufügen')
            ])
        ?>
    </label>

</fieldset>


<input type="hidden" name="institute" value="<?= $values['institute'] ?>">
<input type="hidden" name="studygroup" value="1">
<input type="hidden" name="stgteil_id" value="<?= htmlReady($values['stgteil_id']) ?>">
<?php foreach ($values['lecturers'] as $id => $assigned) : ?>
    <input type="hidden" name="lecturers[<?= $id ?>]" value="1">
<?php endforeach ?>

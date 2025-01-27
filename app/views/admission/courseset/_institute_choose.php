<?php
/**
 * @var array $myInstitutes
 * @var string $current_institut_id
 * @var string $set_name_prefix
 * @var array $ruleTypes
 * @var string $current_semester_id
 */
?>
<form action="?" method="post" name="institute_choose" class="default">
    <?= CSRFProtection::tokenTag() ?>

    <fieldset>
        <legend><?= _('Anmeldesets auflisten') ?></legend>

        <?= $this->render_partial('admission/institute-select.php', [
            'institutes' => $myInstitutes,
            'current_institut_id' => $current_institut_id,
        ]) ?>

        <label>
            <?=_("Präfix des Namens:")?>
            <input type="text" name="set_name_prefix" value="<?=htmlReady($set_name_prefix)?>" size="40">
        </label>

        <section>
            <?=_("Enthaltene Regeln:")?>
            <div class="hidden-no-js check_actions">
                (<?= _('markieren') ?>:
                <button class="as-link" onclick="return STUDIP.Admission.checkUncheckAll('choose_rule_type', 'check')"
                        title="<?= _('Alle Regeltypen auswählen') ?>">
                    <?= _('alle') ?>
                </button>
                |
                <button class="as-link" onclick="return STUDIP.Admission.checkUncheckAll('choose_rule_type', 'uncheck')"
                        title="<?= _('Keinen Regeltyp auswählen') ?>">
                    <?= _('keine') ?>
                </button>
                |
                <button class="as-link" onclick="return STUDIP.Admission.checkUncheckAll('choose_rule_type', 'invert')"
                        title="<?= _('Aktuelle Auswahl der Regeltypen umkehren') ?>">
                    <?= _('Auswahl umkehren') ?>
                </button>)
            </div>
        </section>

        <div>
            <? foreach ($ruleTypes as $type => $detail) : ?>
            <label class="col-2">
                <input type="checkbox" name="choose_rule_type[<?= $type?>]" <?=(isset($current_rule_types[$type]) ? 'checked' : '')?> value="1">
                <?= htmlReady($detail['name']);?>
            </label>
            <? endforeach; ?>
        </div>

        <label>
            <?=_("Zugewiesene Veranstaltungen aus diesem Semester:")?>
            <?= Semester::getSemesterSelector(['name'=>'select_semester_id'], $current_semester_id, 'semester_id', true)?>
        </label>
    </fieldset>

    <footer>
        <?= Studip\Button::create(_('Auswählen'), 'choose_institut', ['title' => _("Einrichtung auswählen")]) ?>
    </footer>
</form>
<br>

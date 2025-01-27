<?php
/**
 * @var Vips_SheetsController $controller
 * @var VipsAssignment $assignment
 * @var string[] $user_ids
 * @var bool $print_files
 * @var bool $print_correction
 * @var bool $print_sample_solution
 * @var array $assignment_data
 */
?>
<? if ($assignment->checkEditPermission()): ?>
    <form class="print_settings" action="<?= $controller->link_for('vips/sheets/print_assignments') ?>" method="POST">
        <input type="hidden" name="assignment_id" value="<?= $assignment->id ?>">

        <? foreach ($user_ids as $user_id): ?>
            <input type="hidden" name="user_ids[]" value="<?= htmlReady($user_id) ?>">
        <? endforeach ?>

        <?= _('Einstellungen:') ?>

        <? if ($user_ids): ?>
            <label>
                <input type="checkbox" name="print_files" value="1" <?= $print_files ? 'checked' : '' ?> onchange="this.form.submit();">
                <?= _('Dateiabgaben drucken') ?>
            </label>

            <label>
                <input type="checkbox" name="print_correction" value="1" <?= $print_correction ? 'checked' : '' ?> onchange="this.form.submit();">
                <?= _('Korrekturen drucken') ?>
            </label>
        <? endif ?>

        <label>
            <input type="checkbox" name="print_sample_solution" value="1" <?= $print_sample_solution ? 'checked' : '' ?> onchange="this.form.submit();">
            <?= _('Musterlösung drucken') ?>
        </label>
    </form>
<? endif ?>

<? foreach ($assignment_data as $data): ?>
    <?= $this->render_partial('vips/sheets/print_assignment', $data) ?>
<? endforeach ?>

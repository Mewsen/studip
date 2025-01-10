<?php
/**
 * @var Vips_ConfigController $controller
 * @var Config $config
 */
?>
<form class="default width-1200" action="<?= $controller->link_for('vips/config/save') ?>" data-secure method="post">
    <?= CSRFProtection::tokenTag() ?>
    <button hidden name="save"></button>

    <fieldset>
        <legend>
            <?= _('Einstellungen für Klausuren') ?>
        </legend>

        <div class="label-text">
            <?= _('Klausurmodus aktivieren') ?>
        </div>

        <label class="undecorated">
            <input type="checkbox" name="exam_mode" value="1" <?= $config->VIPS_EXAM_RESTRICTIONS ? 'checked' : '' ?>>
            <?= _('Während einer Klausur den Zugriff auf andere Bereiche von Stud.IP sperren') ?>
            <?= tooltipIcon(_('Gilt nur für Klausuren mit beschränktem IP-Zugriffsbereich.')) ?>
        </label>

        <div class="label-text">
            <?= _('Vordefinierte IP-Bereiche für PC-Räume') ?>
        </div>

        <table class="default">
            <thead>
                <tr>
                    <th style="width: 20%;">
                        <?= _('Raum') ?>
                    </th>
                    <th style="width: 75%;">
                        <?= _('IP-Bereiche') ?>
                        <?= tooltipIcon($this->render_partial('vips/sheets/ip_range_tooltip'), false, true) ?>
                    </th>
                    <th class="actions">
                        <?= _('Löschen') ?>
                    </th>
                </tr>
            </thead>

            <tbody class="dynamic_list">
                <? foreach ($config->VIPS_EXAM_ROOMS ?: [] as $room => $ip_range): ?>
                    <tr class="dynamic_row">
                        <td>
                            <input type="text" name="room[]" value="<?= htmlReady($room) ?>">
                        </td>
                        <td>
                            <input type="text" class="size-l validate_ip_range" name="ip_range[]" value="<?= htmlReady($ip_range) ?>">
                        </td>
                        <td class="actions">
                            <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Eintrag löschen')]) ?>
                        </td>
                    </tr>
                <? endforeach ?>

                <tr class="dynamic_row template">
                    <td>
                        <input type="text" name="room[]">
                    </td>
                    <td>
                        <input type="text" class="size-l validate_ip_range" name="ip_range[]">
                    </td>
                    <td class="actions">
                        <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Eintrag löschen')]) ?>
                    </td>
                </tr>

                <tr>
                    <th colspan="3">
                        <?= Studip\Button::create(_('Eintrag hinzufügen'), 'add_room', ['class' => 'add_dynamic_row']) ?>
                    </th>
                </tr>
            </tbody>
        </table>

        <label>
            <?= _('Teilnahmebedingungen vor Beginn einer Klausur') ?>
            <textarea name="exam_terms" class="size-l wysiwyg"><?= wysiwygReady($config->VIPS_EXAM_TERMS) ?></textarea>
        </label>
    </fieldset>

    <footer>
        <?= Studip\Button::createAccept(_('Speichern'), 'save') ?>
    </footer>
</form>

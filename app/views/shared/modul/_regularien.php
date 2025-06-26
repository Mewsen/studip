<?php
/**
 * @var Modul $modul
 */
?>
<table class="mvv-modul-details default nohover">
    <thead>
        <tr>
            <th><?= _('Regularien') ?></th>
            <th><?= _('Teilnahme&shy;voraussetzungen') ?></th>
            <th><?= _('Angebots&shy;rhythmus') ?></th>
            <th><?= _('Anwesenheits&shy;pflicht') ?></th>
            <th><?= _('Gewicht an Modulnote in %') ?></th>
        </tr>
    </thead>
    <tbody>
        <? foreach ($modul->modulteile as $modulteil): ?>
            <?
            $modulteil_deskriptor = $modulteil->getDeskriptor();
            // Für die Kenntlichmachung der Modulteile in Listen die Nummer des
            // Modulteils und den ausgewählten Namen verwenden.
            // Ist keine Nummer vorhanden, dann Durchnummerieren und Standard-
            // Bezeichnung verwenden.
            if (trim($modulteil->nummer)) {
                $num_bezeichnung = $GLOBALS['MVV_MODULTEIL']['NUM_BEZEICHNUNG']['values'][$modulteil->num_bezeichnung]['name'];
                $name_kurz = sprintf('%s %d', $num_bezeichnung, $modulteil->nummer);
            } else {
                $num_bezeichnung_default = $GLOBALS['MVV_MODULTEIL']['NUM_BEZEICHNUNG']['default'];
                $name_kurz = $GLOBALS['MVV_MODULTEIL']['NUM_BEZEICHNUNG']['values'][$num_bezeichnung_default]['name']
                        . ' ' . $nummer_modulteil;
                $nummer_modulteil++;
            }
            ?>
            <tr data-mvv-id="<?= $modulteil->id; ?>" data-mvv-type="modulteil">
                <td style="vertical-align: top; font-weight: bold;" data-mvv-field="mvv_modulteil.nummer mvv_modulteil.num_bezeichnung"><?= $name_kurz ?></td>
                <td data-mvv-field="mvv_modulteil_deskriptor.voraussetzung">
                    <?= formatReady($modulteil_deskriptor->getReplacedValue('voraussetzung')) ?>
                </td>
                <td data-mvv-field="mvv_modulteil.semester">
                    <?= $GLOBALS['MVV_NAME_SEMESTER']['values'][$modulteil->semester]['name'] ?>
                </td>
                <td data-mvv-field="mvv_modulteil.pflicht">
                    <?= ($modulteil->pflicht ? _('Ja') : _('Nein')) ?>
                    <?= formatReady($modulteil_deskriptor->getReplacedValue('kommentar_pflicht')) ?>
                </td>
                <td data-mvv-field="mvv_modulteil.anteil_note"><?= $modulteil->anteil_note ?>%</td>
            </tr>
        <? endforeach; ?>
    </tbody>
</table>

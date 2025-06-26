<?php
/**
 * @var Modul $modul
 * @var StgteilabschnittModul|null $abschnitt_modul
 */
?>
<table class="mvv-modul-details default nohover">
    <thead>
        <tr>
            <th><?= _('Modulveranstaltung') ?></th>
            <th><?= _('Lehrveranstaltungsform') ?></th>
            <th><?= _('Veranstaltungstitel') ?></th>
            <th><?= _('SWS') ?></th>
            <th><?= _('Workload Präsenz') ?></th>
            <th><?= _('Workload Vor- / Nachbereitung') ?></th>
            <th><?= _('Workload selbstgestaltete Arbeit') ?></th>
            <th><?= _('Workload Prüfung incl. Vorbereitung') ?></th>
            <th><?= _('Workload Summe') ?></th>
        </tr>
    </thead>
    <tbody>
        <? $wlSelbst = 0; ?>
        <? $wlPruef = 0; ?>
        <? $modulSumme = 0; ?>
        <? $nummer_modulteil = 1; ?>
        <? foreach ($modul->modulteile as $modulteil): ?>
            <? $modulteil_deskriptor = $modulteil->getDeskriptor();
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
            } ?>
            <? $wlSelbst += $modulteil->wl_selbst; ?>
            <? $wlPruef += $modulteil->wl_pruef; ?>
            <? $modulteil_summe = $modulteil->wl_praesenz + $modulteil->wl_bereitung + $modulteil->wl_selbst + $modulteil->wl_pruef; ?>
            <? $modulSumme += $modulteil_summe; ?>
            <? if (!empty($show_synopse)) : ?>
            <tr data-mvv-id="<?= $modulteil->id; ?>" data-mvv-type="modulteil">
                <td data-mvv-field="mvv_modulteil.nummer mvv_modulteil.num_bezeichnung"><strong><?= htmlReady($name_kurz) ?></strong></td>
                <td data-mvv-field="mvv_modulteil.lernlehrform"><?= $GLOBALS['MVV_MODULTEIL']['LERNLEHRFORM']['values'][$modulteil->lernlehrform]['name'] ?? '' ?></td>
                <td data-mvv-field="mvv_modulteil_deskriptor.bezeichnung"><?= htmlReady($modulteil_deskriptor->bezeichnung) ?></td>
                <td style="text-align: right;" data-mvv-field="mvv_modulteil.sws"><?= htmlReady($modulteil->sws) ?: '' ?></td>
                <td style="text-align: right;" data-mvv-field="mvv_modulteil.wl_praesenz mvv_modulteil_deskriptor.kommentar_wl_praesenz">
                    <?= $modulteil->wl_praesenz ?>
                    <?= MVVController::trim($modulteil_deskriptor->kommentar_wl_praesenz)
                        ? sprintf(' (%s)', formatReady($modulteil_deskriptor->kommentar_wl_praesenz))
                        : '' ?>
                </td>
                <td style="text-align: right;" data-mvv-field="mvv_modulteil.wl_bereitung mvv_modulteil_deskriptor.kommentar_wl_bereitung">
                    <?= $modulteil->wl_bereitung ?>
                    <?= MVVController::trim($modulteil_deskriptor->kommentar_wl_bereitung)
                        ? sprintf(' (%s)', formatReady($modulteil_deskriptor->kommentar_wl_bereitung))
                        : '' ?>
                </td>
                <td style="text-align: right;" data-mvv-field="mvv_modulteil.wl_selbst mvv_modulteil_deskriptor.kommentar_wl_selbst">
                    <?= $modulteil->wl_selbst ?>
                    <?= MVVController::trim($modulteil_deskriptor->kommentar_wl_selbst)
                        ? sprintf(' (%s)', formatReady($modulteil_deskriptor->kommentar_wl_selbst))
                        : '' ?>
                </td>
                <td style="text-align: right;" data-mvv-field="mvv_modulteil.wl_pruef mvv_modulteil_deskriptor.kommentar_wl_pruef">
                    <?= $modulteil->wl_pruef ?>
                    <?= MVVController::trim($modulteil_deskriptor->kommentar_wl_pruef)
                        ? sprintf(' (%s)',formatReady($modulteil_deskriptor->kommentar_wl_pruef))
                        : '' ?>
                </td>
                <td style="text-align: right;"><?= $modulteil_summe ?></td>
            </tr>
            <? else : ?>
            <tr data-mvv-id="<?= $modulteil->id; ?>" data-mvv-type="modulteil">
                <td data-mvv-field="mvv_modulteil.nummer mvv_modulteil.num_bezeichnung"><strong><?= htmlReady($name_kurz) ?></strong></td>
                <td data-mvv-field="mvv_modulteil.lernlehrform"><?= $GLOBALS['MVV_MODULTEIL']['LERNLEHRFORM']['values'][$modulteil->lernlehrform]['name'] ?? '' ?></td>
                <td data-mvv-field="mvv_modulteil_deskriptor.bezeichnung"><?= htmlReady($modulteil_deskriptor->getReplacedValue('bezeichnung')) ?></td>
                <td style="text-align: right;" data-mvv-field="mvv_modulteil.sws"><?= htmlReady($modulteil->sws) ?: '' ?></td>
                <td style="text-align: right;" data-mvv-field="mvv_modulteil.wl_praesenz mvv_modulteil_deskriptor.kommentar_wl_praesenz">
                    <?= $modulteil->wl_praesenz ?>
                    <?= MVVController::trim($modulteil_deskriptor->getReplacedValue('kommentar_wl_praesenz'))
                        ? tooltipIcon(formatReady($modulteil_deskriptor->getReplacedValue('kommentar_wl_praesenz')))
                        : '' ?>
                </td>
                <td style="text-align: right;" data-mvv-field="mvv_modulteil.wl_bereitung mvv_modulteil_deskriptor.kommentar_wl_bereitung">
                    <?= $modulteil->wl_bereitung ?>
                    <?= MVVController::trim($modulteil_deskriptor->getReplacedValue('kommentar_wl_bereitung'))
                        ? tooltipIcon(formatReady($modulteil_deskriptor->getReplacedValue('kommentar_wl_bereitung')))
                        : '' ?>
                </td>
                <td style="text-align: right;" data-mvv-field="mvv_modulteil.wl_selbst mvv_modulteil_deskriptor.kommentar_wl_selbst">
                    <?= $modulteil->wl_selbst ?>
                    <?= MVVController::trim($modulteil_deskriptor->getReplacedValue('kommentar_wl_selbst'))
                        ? tooltipIcon(formatReady($modulteil_deskriptor->getReplacedValue('kommentar_wl_selbst')))
                        : '' ?>
                </td>
                <td style="text-align: right;" data-mvv-field="mvv_modulteil.wl_pruef mvv_modulteil_deskriptor.kommentar_wl_pruef">
                    <?= $modulteil->wl_pruef ?>
                    <?= MVVController::trim($modulteil_deskriptor->getReplacedValue('kommentar_wl_pruef'))
                        ? tooltipIcon(formatReady($modulteil_deskriptor->kommentar_wl_pruef))
                        : '' ?>
                </td>
                <td style="text-align: right;"><?= $modulteil_summe ?></td>
            </tr>
            <? endif; ?>
        <? endforeach; ?>
        <?
        $modulWLSumme = $modul->wl_selbst + $modul->wl_pruef;
        $modulSumme += $modulWLSumme;
        ?>
        <? if ($modulWLSumme > 0) : ?>
        <tr>
            <td colspan="6"><strong><?= _('Workload modulbezogen') ?></strong></td>
            <td style="text-align: right;"><?= htmlReady($modul->wl_selbst) ?></td>
            <td style="text-align: right;"><?= htmlReady($modul->wl_pruef) ?></td>
            <td style="text-align: right;"><?= $modulWLSumme ?></td>
        </tr>
        <? endif; ?>
        <tr>
            <td colspan="8"><strong><?= _('Workload Modul insgesamt') ?></strong></td>
            <td style="text-align: right;"><?= $modulSumme ?></td>
        </tr>
    </tbody>
</table>

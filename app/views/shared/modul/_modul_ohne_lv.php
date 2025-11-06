<?php
/**
 * @var Modul $modul
 * @var StgteilabschnittModul|null $abschnitt_modul
 * @var string $code
 * @var string $title
 */
?>
<? $deskriptor = $modul->getDeskriptor(); ?>
<table class="mvv-modul-details default nohover" data-mvv-id="<?= $modul->id; ?>" data-mvv-type="modul">
    <tbody>
        <? $modulSumme =  $modul->wl_selbst + $modul->wl_pruef ?>
        <tr>
            <td style="width: 30%;"><strong><?= _('Workload selbstgestaltete Arbeit') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modul.wl_selbst mvv_modul_deskriptor.kommentar_wl_selbst">
                <?= htmlReady($modul->getReplacedValue('wl_selbst')) ?>
                <?= MVVController::trim($deskriptor->getReplacedValue('kommentar_wl_selbst'))
                    ? sprintf(" (%s)", formatReady($deskriptor->getReplacedValue('kommentar_wl_selbst')))
                    : '' ?>
            </td>

        </tr>
        <tr>
            <td style="width: 30%;"><strong><?= _('Workload Prüfung incl. Vorbereitung') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modul.wl_pruef mvv_modul_deskriptor.kommentar_wl_pruef">
                <?= htmlReady($modul->getReplacedValue('wl_pruef')) ?>
                <?= MVVController::trim($deskriptor->getReplacedValue('kommentar_wl_pruef'))
                    ? sprintf(" (%s)", formatReady($deskriptor->getReplacedValue('kommentar_wl_pruef')))
                    : '' ?>
            </td>

        </tr>
        <tr>
            <td style="width: 30%;"><strong><?= _('Workload insgesamt') ?></strong></td>
            <td style="width: 70%;"><?= $modulSumme ?></td>
        </tr>
    </tbody>
</table>
<table class="mvv-modul-details default nohover" data-mvv-id="<?= $deskriptor->id ?>" data-mvv-type="moduldeskriptor">
    <tbody>
        <? if (trim($deskriptor->getReplacedValue('pruef_vorleistung'))) : ?>
        <tr>
            <td style="width: 30%;"><strong><?= _('Prüfungsvorleistung') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modul_deskriptor.pruef_vorleistung" >
                <?= formatReady($deskriptor->getReplacedValue('pruef_vorleistung')) ?>
            </td>
        </tr>
        <? endif; ?>
        <tr>
            <td style="width: 30%;"><strong><?= _('Prüfungsform') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modul_deskriptor.pruef_leistung">
                <?= formatReady($deskriptor->getReplacedValue('pruef_leistung')) ?>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;"><strong><?= _('Wiederholungsprüfung') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modul_deskriptor.pruef_wiederholung">
                <?= formatReady($deskriptor->getReplacedValue('pruef_wiederholung')) ?>
            </td>
        </tr>
    </tbody>
</table>

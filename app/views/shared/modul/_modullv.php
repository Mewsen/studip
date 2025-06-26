<?php
/**
 * @var Modul $modul
 * @var StgteilabschnittModul|null $abschnitt_modul
 */
?>
<? $modulteil = $modul->modulteile->first(); ?>
<? $modulteil_deskriptor = $modulteil->getDeskriptor(); ?>
<table class="mvv-modul-details default nohover" data-mvv-id="<?= $modulteil->id; ?>" data-mvv-type="modulteil">
    <tbody>
        <? $modulteil_summe = $modulteil->wl_praesenz + $modulteil->wl_bereitung + $modulteil->wl_selbst + $modulteil->wl_pruef ?>
        <tr>
            <td style="width: 30%;"><strong><?= _('Lehrveranstaltungsform') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil.lernlehrform"><?= $GLOBALS['MVV_MODULTEIL']['LERNLEHRFORM']['values'][$modulteil->lernlehrform]['name'] ?? '' ?></td>
        </tr>
        <tr>
            <td style="width: 30%;"><strong><?= _('Veranstaltungstitel') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil_deskriptor.bezeichnung">
                <?= htmlReady($modulteil_deskriptor->getReplacedValue('bezeichnung')) ?>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;"><strong><?= _('SWS') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil.sws mvv_modulteil_deskriptor.sws_alternative">
                <?= $modulteil->sws ?: '' ?>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;"><strong><?= _('Workload Präsenz') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil.wl_praesenz mvv_modulteil_deskriptor.kommentar_wl_praesenz">
                <?= $modulteil->getReplacedValue('wl_praesenz') ?>
                <?= MVVController::trim($modulteil_deskriptor->getReplacedValue('kommentar_wl_praesenz'))
                    ? sprintf(" (%s)", formatReady($modulteil_deskriptor->getReplacedValue('kommentar_wl_praesenz')))
                    : '' ?>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;"><strong><?= _('Workload Vor- / Nachbereitung') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil.wl_bereitung mvv_modulteil_deskriptor.kommentar_wl_bereitung">
                <?= $modulteil->getReplacedValue('wl_bereitung') ?>
                <?= MVVController::trim($modulteil_deskriptor->getReplacedValue('kommentar_wl_bereitung'))
                    ? sprintf(" (%s)", formatReady($modulteil_deskriptor->getReplacedValue('kommentar_wl_bereitung')))
                    : '' ?>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;"><strong><?= _('Workload selbstgestaltete Arbeit') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil.wl_selbst mvv_modulteil_deskriptor.kommentar_wl_selbst">
                <?= $modulteil->getReplacedValue('wl_selbst') ?>
                <?= MVVController::trim($modulteil_deskriptor->getReplacedValue('kommentar_wl_selbst'))
                    ? sprintf(" (%s)", formatReady($modulteil_deskriptor->getReplacedValue('kommentar_wl_selbst')))
                    : '' ?>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;"><strong><?= _('Workload Prüfung incl. Vorbereitung') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil.wl_pruef mvv_modulteil_deskriptor.kommentar_wl_pruef">
                <?= $modulteil->getReplacedValue('wl_pruef') ?>
                <?= MVVController::trim($modulteil_deskriptor->getReplacedValue('kommentar_wl_pruef'))
                    ? sprintf(" (%s)", formatReady($modulteil_deskriptor->getReplacedValue('kommentar_wl_pruef')))
                    : '' ?>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;"><strong><?= _('Workload insgesamt') ?></strong></td>
            <td style="width: 70%;"><?= $modulteil_summe ?></td>
        </tr>
        <? if ((int) $modul->getReplacedValue('wl_selbst')) : ?>
            <tr>
                <td style="width: 30%;"><strong><?= _('Workload selbstgestaltete Arbeit (modulbezogen') ?></strong></td>
                <td style="width: 70%;" data-mvv-field="mvv_modul.wl_selbst mvv_modul_deskriptor.kommentar_wl_selbst">
                    <?= $modul->getReplacedValue('wl_selbst') ?>
                    <?= MVVController::trim($modul->deskriptoren->getReplacedValue('kommentar_wl_selbst'))
                        ? sprintf(" (%s)", formatReady($modul->deskriptoren->getReplacedValue('kommentar_wl_selbst')))
                        : '' ?>
                </td>
            </tr>
        <? endif; ?>
        <? if ((int) $modul->getReplacedValue('wl_pruef')) : ?>
            <tr>
                <td style="width: 30%;"><strong><?= _('Workload Prüfung incl. Vorbereitung (modulbezogen)') ?></strong></td>
                <td style="width: 70%;" data-mvv-field="mvv_modul.wl_pruef mvv_modul_deskriptor.kommentar_wl_pruef">
                    <?= $modul->getReplacedValue('wl_pruef') ?>
                    <?= MVVController::trim($modul->deskriptoren->getReplacedValue('kommentar_wl_pruef'))
                        ? sprintf(" (%s)", formatReady($modul->deskriptoren->getReplacedValue('kommentar_wl_pruef')))
                        : '' ?>
                </td>
            </tr>
        <? endif; ?>
        <? if (intval($modul->getReplacedValue('wl_selbst')) + intval($modul->getReplacedValue('wl_pruef'))) : ?>
            <tr>
                <td style="width: 30%;"><strong><?= _('Workload Modul insgesamt') ?></strong></td>
                <td style="width: 70%;"><?=
                    $modulteil_summe
                    + intval($modul->getReplacedValue('wl_selbst'))
                    + intval($modul->getReplacedValue('wl_pruef')) ?></td>
            </tr>
        <? endif; ?>
    </tbody>
</table>
<table class="mvv-modul-details default nohover" data-mvv-id="<?= $modulteil_deskriptor->id; ?>" data-mvv-type="modulteil_deskriptor">
    <tbody>
        <? if (trim($modulteil_deskriptor->pruef_vorleistung)) : ?>
        <tr>
            <td style="width: 30%;"><strong><?= _('Prüfungsvorleistung') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil_deskriptor.pruef_vorleistung">
                <?= formatReady($modulteil_deskriptor->getReplacedValue('pruef_vorleistung')) ?>
            </td>
        </tr>
        <? endif; ?>
        <tr>
            <td style="width: 30%;"><strong><?= _('Prüfungsform') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil_deskriptor.pruef_leistung">
                <?= formatReady($modulteil_deskriptor->getReplacedValue('pruef_leistung')) ?>
            </td>
        </tr>
    </tbody>
</table>
<table class="mvv-modul-details default nohover" data-mvv-id="<?= $modulteil->id; ?>" data-mvv-type="modulteil">
    <tbody>
        <tr>
            <td style="width: 30%;"><strong><?= _('Angebotsrhythmus') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil.semester"><?= $GLOBALS['MVV_NAME_SEMESTER']['values'][$modulteil->semester]['name'] ?? '' ?></td>
        </tr>
        <tr>
            <td style="width: 30%;"><strong><?= _('Aufnahmekapazität') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil.kapazitaet mvv_modulteil_deskriptor.kommentar_kapazitaet">
                <?= trim($modulteil->kapazitaet) ?: _('unbegrenzt') ?>
                <?= MVVController::trim($modulteil_deskriptor->getReplacedValue('kommentar_kapazitaet'))
                    ? sprintf("(%s)", formatReady($modulteil_deskriptor->getReplacedValue('kommentar_kapazitaet')))
                    : '' ?>
            </td>
        </tr>
        <? if ($modulteil->pflicht) : ?>
        <tr>
            <td style="width: 30%;"><strong><?= _('Anwesenheitspflicht') ?></strong></td>
            <td style="width: 70%;" data-mvv-field="mvv_modulteil.pflicht mvv_modulteil_deskriptor.kommentar_pflicht">
                <?= $modulteil->pflicht ? _('Ja') : _('Nein') ?>
                <?= MVVController::trim($modulteil_deskriptor->getReplacedValue('kommentar_pflicht'))
                    ? sprintf("(%s)", formatReady($modulteil_deskriptor->getReplacedValue('kommentar_pflicht')))
                    : '' ?>
            </td>
        </tr>
        <? endif; ?>
    </tbody>
</table>
<? $data_fields = []; ?>
<? if ($abschnitt_modul) : ?>
    <? $data_fields = $abschnitt_modul->datafields->filter(
        fn(DatafieldEntryModel $d): bool => ($d->datafield->object_class ?? '') === '') ?>
<? endif; ?>
<? if (count($modulteil_deskriptor->datafields)) : ?>
    <table class="mvv-modul-details default nohover" data-mvv-id="<?= $modulteil_deskriptor->id; ?>" data-mvv-type="modulteil_deskriptor">
        <tbody>
            <? foreach ($modulteil_deskriptor->datafields as $entry) : ?>
                <? if (trim($entry->content)) : ?>
                    <? $df = $entry->getTypedDatafield(); ?>
                    <tr>
                        <td style="width: 30%;"><strong><?= htmlReady($df->getName()) ?></strong></td>
                        <td style="width: 70%;"><?= $df->getDisplayValue(); ?></td>
                    </tr>
                <? endif; ?>
            <? endforeach; ?>
            <? foreach ($data_fields as $entry) : ?>
                <? if (trim($entry->content)) : ?>
                    <? $df = $entry->getTypedDatafield(); ?>
                    <tr>
                        <td style="width: 30%;"><strong><?= htmlReady($df->getName()) ?></strong></td>
                        <td style="width: 70%;"><?= $df->getDisplayValue(); ?></td>
                    </tr>
                <? endif; ?>
            <? endforeach; ?>
        </tbody>
    </table>
<? endif; ?>

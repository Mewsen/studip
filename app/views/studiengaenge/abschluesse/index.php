<?php
/**
 * @var Studiengaenge_AbschluesseController $controller
 * @var Abschluss[] $abschluesse
 * @var string $abschluss_id
 */
?>
<table class="default collapsable"">
<thead>
    <tr class="sortable">
        <?= $controller->renderSortLink('/index', _('Abschluss'), 'name') ?>
        <?= $controller->renderSortLink('/index', _('Studiengänge'), 'count_studiengaenge', ['style' => 'text-align: center; width: 10%;']) ?>
        <th style="width: 5%; text-align: right;"><?= _('Aktionen') ?></th>
    </tr>
</thead>
<? foreach ($abschluesse as $abschluss) : ?>
    <?php
    // skip unknown Abschluesse
    if (is_null($abschluss->name)) {
        continue;
    }
    ?>
    <tbody class="<?= (count($abschluss->studiengaenge) ? '' : 'empty') ?> <?= ($abschluss_id === $abschluss->id ? 'not-collapsed' : 'collapsed') ?>">
        <tr class="header-row" id="abschluss_<?= $abschluss->id ?>">
            <td class="toggle-indicator">
                <? if (is_null($abschluss->name) && count($abschluss->studiengaenge)) : ?>
                    <a class="mvv-load-in-new-row" href="<?= $controller->detailsLink($abschluss->id) ?>">
                        <?= _('Keinem Abschluss zugeordnet') ?>
                    </a>
                <? else : ?>
                    <? if (count($abschluss->studiengaenge)) : ?>
                        <a class="mvv-load-in-new-row"
                           href="<?= $controller->detailsLink($abschluss->id) ?>">
                            <?= htmlReady($abschluss->getDisplayName()) ?>
                        </a>
                    <? else : ?>
                        <?= htmlReady($abschluss->getDisplayName()) ?>
                    <? endif; ?>
                <? endif; ?>
            </td>
            <td style="text-align: center;" class="dont-hide"><?= count($abschluss->studiengaenge) ?></td>
            <td></td>
        </tr>
        <? if (isset($abschluss_id) && $abschluss_id === $abschluss->id) : ?>
            <tr class="loaded-details nohover">
                <?= $this->render_partial('studiengaenge/studiengaenge/details') ?>
            </tr>
        <? endif; ?>
    </tbody>
<? endforeach; ?>
</table>

<?php
/***
 * @var array $waiting_list
 * @var MyCoursesController $controller
 */

?>

<? if (!empty($waiting_list)) : ?>
    <table class="default collapsable" id="my_waitlists">
        <caption>
            <?= _('Anmelde- und Wartelisteneinträge') ?>
        </caption>
        <colgroup class="hidden-small-down">
            <col style="width: 1px">
            <col style="width: 65%">
            <col style="width: 7%">
            <col style="width: 10%">
            <col style="width: 10%">
            <col style="width: 15%">
            <col style="width: 3%">
        </colgroup>
        <colgroup class="hidden-medium-up">
            <col style="width: 1px">
        </colgroup>

        <thead>
        <tr>
            <th></th>
            <th style="text-align: left"><?= _('Name') ?></th>
            <th class="hidden-small-down"><?= _('Inhalt') ?></th>
            <th style="text-align: center"><?= _('Datum') ?></th>
            <th class="hidden-small-down"
                style="text-wrap: nowrap; white-space: nowrap"><?= _('Position') ?></th>
            <th class="hidden-small-down"><?= _('Art') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($waiting_list as $wait) : ?>

            <?php
            // wir sind in einer Anmeldeliste, also keine Wahrscheinlichkeit zuverlässig berechenbar
            if ($wait['status'] === 'claiming') {
                $chance_color = 'ff';
            } // wir sind in einer Warteliste
            else {
                $chance_color = $wait['position'] < 30 ? dechex(255 - $wait['position'] * 6) : 44;
            }

            $seminar_name = $wait["Name"];
            if (!empty($wait['sem_status']) && SeminarCategories::GetByTypeId($wait['sem_status'])->studygroup_mode) {
                $seminar_name .= ' (' . _('Studiengruppe') . ', ' . _('geschlossen') . ')';
            }
            ?>
            <tr>
                <td title="<?= _('Position') ?>" style="background:#44<?= $chance_color ?>44">
                </td>

                <td>
                    <a href="<?= URLHelper::getLink('dispatch.php/course/details/', [
                        'sem_id'                => $wait['seminar_id'],
                        'send_from_search_page' => 'dispatch.php/my_courses/index',
                        'send_from_search'      => 'TRUE'
                    ]) ?>">
                        <?= htmlReady($seminar_name) ?>
                    </a>
                    <?php if ($wait['status'] === 'claiming') : ?>
                        <br>
                        <?= sprintf(_('Priorität %1$u im Anmeldeset "%2$s"'), $wait['priority'], $wait['cname']) ?>
                    <?php endif ?>
                </td>
                <td class="hidden-small-down">
                    <a data-dialog="size=auto"
                       href="<?= $controller->link_for('course/details/index', $wait['seminar_id']) ?>"
                    >
                        <? $params = tooltip2(_('Veranstaltungsdetails anzeigen')) ?>
                        <?= Icon::create('info-circle', Icon::ROLE_INACTIVE)->asImg(['style' => 'cursor: pointer']) ?>
                    </a>
                </td>
                <td style="text-align: center">
                    <?= $wait['status'] === 'claiming' ? date('d.m.', $wait['admission_endtime']) : "-" ?>
                </td>

                <td class="hidden-small-down" style="text-align: center">
                    <?= $wait['status'] === 'claiming' ? '-' : $wait['position'] ?>
                </td>

                <td class="hidden-small-down" style="text-align: center">
                    <? if ($wait['status'] === 'claiming') : ?>
                        <?= _('Autom.') ?>
                    <? elseif ($wait['status'] === 'accepted') : ?>
                        <?= _('Vorl.') ?>
                    <? else: ?>
                        <?= _('Wartel.') ?>
                    <? endif ?>
                </td>

                <td style="text-align: right">
                    <? if ($wait['status'] === 'accepted' && $wait['admission_binding']) : ?>
                        <a href="<?= $controller->url_for('my_courses/decline_binding') ?>">
                            <?= Icon::create('door-leave', Icon::ROLE_INACTIVE)->asImg(['title' => _('Die Teilnahme ist bindend. Bitte wenden Sie sich an die Lehrenden.')]) ?>
                        </a>
                    <? else : ?>
                        <a href="<?= $controller->link_for('my_courses/decline', $wait['seminar_id'], ['cmd' => 'suppose_to_kill_admission']) ?>">
                            <?= Icon::create('door-leave', Icon::ROLE_INACTIVE)->asImg(['title' => _('aus der Veranstaltung abmelden')]) ?>
                        </a>
                    <? endif ?>
                </td>
            </tr>
        <? endforeach ?>
        </tbody>
    </table>
    <br>
    <br>
<? endif ?>

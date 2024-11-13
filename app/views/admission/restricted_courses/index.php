<?php
/**
 * @var Course[] $courses
 * @var array[] $additional_data
 */
?>
<?= $this->render_partial('admission/restricted_courses/_institute_choose.php')?>
<br>
<? if (count($courses)) : ?>
    <table class="default nohover sortable-table">
        <thead>
            <tr>
                <th data-sort="text"><?= _('Anmeldeset') ?></th>
                <th data-sort="text"><?= _('Name') ?></th>
                <th data-sort="digit"><?= _('max. Teilnehmende') ?></th>
                <th data-sort="digit"><?= _('Teilnehmer aktuell') ?></th>
                <th data-sort="htmldata"><?= _('Anmeldungen') ?></th>
                <th data-sort="htmldata"><?= _('Warteliste') ?></th>
                <th data-sort="htmldata"><?= _('Platzverteilung') ?></th>
                <th data-sort="htmldata"><?= _('Startzeitpunkt') ?></th>
                <th data-sort="htmldata"><?= _('Endzeitpunkt') ?></th>
            </tr>
        </thead>
        <tbody>
        <? foreach ($courses as $course) : ?>
            <?php
            $additional = $additional_data[$course->id];
            ?>
            <tr>
                <td><a href="<?= URLHelper::getLink('dispatch.php/admission/courseset/configure/' . $additional['courseset_id']) ?>"><?= htmlReady($additional['courseset_name']) ?></td>
                <td>
                    <a href="<?= URLHelper::getLink('dispatch.php/course/members/index', ['cid' => $course->id])?>">
                        <?= htmlReady($course->getFullName()) ?>
                    </a>
                </td>
                <td><?= htmlReady($course->admission_turnout ?: '') ?></td>
                <td>
                    <?= htmlReady($additional['participant_count'] + $additional['accepted_count'])?>
                <? if ($course->admission_prelim && $additional['accepted_count']) : ?>
                    <?= tooltipIcon(_('vorläufige Teilnahme: ') . $additional['accepted_count']) ?>
                <? endif ?>
                </td>
                <td data-value="<?= $additional['claiming_count'] ?? 0 ?>">
                    <?= htmlReady($additional['claiming_count'] ?? '-') ?>
                </td>
                <td data-sort-value="<?= $additional['awaiting_count'] ?? 0 ?>">
                    <?= htmlReady($additional['awaiting_count'] ?? '-') ?>
                </td>
                <td style="white-space:nowrap" data-sort-value="<?= (int) $additional['distribution_time']?>">
                    <?= htmlReady($additional['distribution_time'] ? date('d.m.Y H:i', $additional['distribution_time']) : '-') ?>
                </td>
                <td style="white-space:nowrap" data-sort-value="<?= (int) ($course->start_semester->beginn ?? null) ?>">
                    <?= date('d.m.Y H:i', $course->start_semester->beginn) ?>
                </td>
                <td style="white-space:nowrap" data-sort-value="<?= (int) ($course->end_semester->ende ?? null) ?>">
                    <?= $course->end_semester ? date('d.m.Y H:i', $course->end_semester->ende) : '-' ?>
                </td>
            </tr>
        <? endforeach ?>
        </tbody>
    </table>
<? endif ?>

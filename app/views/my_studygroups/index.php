<? if (!empty($studygroups)) : ?>
    <table class="default sortable-table mycourses">
        <caption>
            <?= _('Meine Studiengruppen') ?>
        </caption>
        <colgroup>
            <col style="width: 7px">
            <col style="width: 5px">
            <col>
            <col>
            <col style="width: "<?= $nav_elements * 27 ?>px">
            <? if (!$is_widget) : ?>
                <col style="width: 45px">
            <? endif ?>
        </colgroup>
        <thead>
            <tr class="sortable">
                <th colspan="2"></th>
                <th data-sort="text"><?= _('Name') ?></th>
                <th data-sort="digit"><?= _('gegründet') ?></th>
                <th><?= _('Inhalt') ?></th>
                <? if (!$is_widget) : ?>
                    <th><?= _('Aktionen') ?></th>
                <? endif ?>
            </tr>
        </thead>
        <?= $this->render_partial('my_studygroups/_course', compact('studygroups')) ?>
    </table>
<? else : ?>
    <?= MessageBox::info(_('Sie haben bisher noch keine Studiengruppe gegründet oder sich in eine eingetragen.')) ?>
<? endif ?>

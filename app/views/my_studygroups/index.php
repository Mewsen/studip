<? if (!empty($studygroups)) : ?>
    <table class="default sortable-table" id="my_seminars">
        <caption>
            <?= _('Meine Studiengruppen') ?>
        </caption>
        <colgroup>
            <col width="10px">
            <col width="25px">
            <col>
            <col>
            <col width="<?= $nav_elements * 27 ?>px">
            <? if (!$is_widget) : ?>
                <col width="45px">
            <? endif ?>
        </colgroup>
        <thead>
            <tr class="sortable" title="<?= _('Klicken, um die Sortierung zu ändern') ?>">

                <th colspan="2" nowrap align="center">
                    <a href="<?= URLHelper::getLink('dispatch.php/my_courses/groups/all/true') ?>"
                       data-dialog="size=normal">
                        <?= Icon::create('group')->asImg(['title' => _('Gruppe ändern'), 'class' => 'middle']) ?>
                    </a>
                </th>
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

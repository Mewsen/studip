<form method="post">
<? foreach ($ilias_list as $ilias_index => $ilias) : ?>
    <table class="default">
        <caption>
            <?= sprintf(_('Meine Kurse in %s'), htmlReady($ilias->getName())) ?>
        </caption>
        <colgroup>
            <col style="width: 2%">
            <col>
            <col style="width: 15%">
            <col style="width: 15%">
            <? if (Config::get()->ILIAS_INTERFACE_BASIC_SETTINGS['show_course_paths'] ?? false) : ?>
                <col style="width: 30%">
            <? endif ?>
        </colgroup>
        <thead>
            <th></th>
            <th><?= _('Name') ?></th>
            <th><?= _('Stud.IP-Veranstaltung') ?></th>
            <th><?= _('Status') ?></th>
            <? if (Config::get()->ILIAS_INTERFACE_BASIC_SETTINGS['show_course_paths'] ?? false) : ?>
                <th><?= _('Pfad') ?></th>
            <? endif ?>
        </thead>
    <? if (array_key_exists($ilias_index, $courses_list) && count($courses_list[$ilias_index]) > 0) : ?>
        <? foreach ($courses_list[$ilias_index] as $crs_id => $course) : ?>
            <tr>
                <td><?= Icon::create('learnmodule', Icon::ROLE_INFO)->asSvg(['title' => _('ILIAS-Kurs')]) ?></td>
                <td>
                    <? if ($course['online'] || in_array($course['status'], [2, 4])) : ?>
                        <a href="<?= URLHelper::getLink("dispatch.php/my_ilias_accounts/view_course/{$ilias_index}/{$crs_id}")?>" data-dialog=""><?= htmlReady($course['title']) ?></a>
                    <? else : ?>
                        <?= htmlReady($course['title']) ?>
                    <? endif ?>
                    <?= !$course['online'] ? '(' . _('offline') . ')' : '' ?>
                </td>
                <td>
                    <? if ($course['studip_object']) : ?>
                        <a href="<?= URLHelper::getLink('dispatch.php/course/go', ['to' => $course['studip_object']]) ?>">
                            <?= Icon::create('seminar')->asSvg(['title' => Course::find($course['studip_object'])->Name]) ?>
                        </a>
                    <? endif ?>
                </td>
                <td><?= htmlReady($course['status_text'] ?: _('unbekannt')) ?></td>
                <? if (!empty(Config::get()->ILIAS_INTERFACE_BASIC_SETTINGS['show_course_paths'])) : ?>
                    <td><?= htmlReady($course['path']) ?></td>
                <? endif ?>
            </tr>
        <? endforeach ?>
    <? else : ?>
        <tr>
            <td colspan="5">
                 <?= sprintf(
                     $selected_semester ? _('Keine Kurse im System %s zum gewählten Filter gefunden.') : _('Sie nehmen im System %s an keinem Kurs teil.'),
                     htmlReady($ilias->getName())
                 )?>
            </td>
        </tr>
   <? endif ?>
   </table>
<? endforeach ?>
<? foreach ($ilias_list as $ilias_index => $ilias) : ?>
    <? if (!empty($workgroups_list[$ilias_index])) : ?>
        <br>
        <br>
        <table class="default">
            <caption>
                <?= sprintf(_('Meine Arbeitsbereiche in %s'), htmlReady($ilias->getName())) ?>
            </caption>
            <colgroup>
                <col style="width: 2%">
                <col>
                <col style="width: 2%">
            </colgroup>
            <thead>
                <tr>
                    <th></th>
                    <th><?= _('Name') ?></th>
                    <th><?= _('Aktionen') ?></th>
                </tr>
            </thead>
        <? if (!empty($workgroups_list[$ilias_index])) : ?>
            <? foreach ($workgroups_list[$ilias_index] as $cat_id => $workgroup) : ?>
                <tr>
                    <td><?= Icon::create('community', Icon::ROLE_INFO)->asSvg(['title' => _('ILIAS-Arbeitsbereich')]) ?></td>
                    <td>
                        <a href="<?= URLHelper::getLink("dispatch.php/my_ilias_accounts/view_workgroup/{$ilias_index}/{$cat_id}")?>" data-dialog=""><?= htmlReady($workgroup['title']) ?></a>
                    </td>
                    <td class="actions">
                    <?= ActionMenu::get()->setContext($workgroup['title'])
                        ->addMultiPersonSearch(
                            MultiPersonSearch::get('add_ilias_workgroup_member' . $cat_id)
                                ->setTitle(sprintf(_('Personen zu Arbeitsbereich "%s" hinzufügen'), $workgroup['title']))
                                ->setLinkText(_('Personen hinzufügen'))
                                ->setSearchObject($add_member_search)
                                ->setDataDialogStatus(Request::isXhr())
                                ->setJSFunctionOnSubmit(Request::isXhr() ? 'STUDIP.Dialog.close();' : false)
                                ->setExecuteURL($controller->url_for('my_ilias_accounts/request_workgroup_member/' . $ilias_index . '/' . $cat_id))
                        ) ?>
                    </td>
                </tr>
            <? endforeach ?>
       <? endif ?>
       </table>
    <? endif ?>
<? endforeach ?>
</form>

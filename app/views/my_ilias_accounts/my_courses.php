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
                <td><?= Icon::create('learnmodule', Icon::ROLE_INFO)->asImg(['title' => _('ILIAS-Kurs')]) ?></td>
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
                        <a href="<?= URLHelper::getLink('dispatch.php/course/basicdata/view', ['cid' => $course['studip_object']]) ?>">
                            <?= Icon::create('seminar')->asImg(['title' => Course::find($course['studip_object'])->Name]) ?>
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
</form>

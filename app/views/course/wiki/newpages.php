<?php
/**
 * @var Course_WikiController $controller
 * @var string $sort
 * @var bool $sort_asc
 * @var WikiPage[]|null $pages
 * @var int $last_visit
 *
 * @var int $num_entries
 * @var int $limit
 * @var int $pagenumber
 */
?>
<table class="default">
    <caption>
        <?= _('Letzte Änderungen') ?>
    </caption>
    <colgroup>
        <col style="min-width: 120px;">
        <col>
        <col style="min-width: 150px;">
        <col>
    </colgroup>
    <thead>
        <tr class="sortable">
            <th <? if ($sort === 'name') echo 'class="' . ($sort_asc ? 'sortasc' : 'sortdesc') . '"'; ?>>
                <a href="<?= $controller->newpages(['sort' => 'name', 'sort_asc' => $sort !== 'name' || !$sort_asc ? 1 : 0]) ?>">
                    <?= _('Seitenname') ?>
                </a>
            </th>
            <th><?= _('Text') ?></th>
            <th><?= _('Autor/-in') ?></th>
            <th <? if ($sort === 'chdate') echo 'class="' . ($sort_asc ? 'sortasc' : 'sortdesc') . '"'; ?>>
                <a href="<?= $controller->newpages(['sort' => 'chdate', 'sort_asc' => $sort === 'chdate' && !$sort_asc ? 1 : 0]) ?>">
                    <?= _('Datum') ?>
                </a>
            </th>
        </tr>
    </thead>
    <tbody>
    <? if (count($pages) === 0): ?>
        <tr>
            <td colspan="4">
                <?= _('Keine Seiten wurden seit Ihrem letzten Besuch verändert.') ?>
            </td>
        </tr>
    <? endif ?>
    <? foreach ($pages as $page) : ?>
        <tr>
            <td>
                <a href="<?= $controller->page($page) ?>">
                    <?= htmlReady($page->name) ?>
                </a>
            </td>
            <td>
            <?
                $authors = [$page->user_id => $page->user];
                $versions = [$page];
                $oldcontent = "";
                $oldversion = $page;
                while ($oldversion = $oldversion->predecessor) {
                    if ($oldversion->mkdate >= $last_visit && $oldversion->user_id !== User::findCurrent()->id) {
                        $oldcontent = $oldversion->content;
                        if (!isset($authors[$oldversion->user_id])) {
                            $versions[] = $oldversion;
                            $authors[$oldversion->user_id] = $oldversion->user;
                        }
                    } else {
                        break;
                    }
                }
                $oldcontent = $oldversion->content;
                $oldcontent = strip_tags(wikiReady($oldcontent));
                $content = strip_tags(wikiReady($page->content));

                $commonFromStart = $controller->findLongestCommonSubstring($content, $oldcontent);
                $commonFromEnd = $controller->findLongestCommonSubstring($content, $oldcontent, true);

                $oldcontent = mb_substr($oldcontent, $commonFromStart, mb_strlen($oldcontent) - mb_strlen($content));
                $content = mb_substr($content, $commonFromStart, $commonFromEnd - $commonFromStart);
                if ($content) {
                    echo htmlReady(mila($content, 300), true, true);
                } elseif ($oldcontent) {
                    echo _('Gelöscht') . ': ' . htmlReady(mila($oldcontent, 300), true, true);
                }
            ?>
            </td>
            <td>
                <ul class="wiki_authors">
                <? foreach ($authors as $user_id => $user) : ?>
                    <li>
                    <? if ($user): ?>
                        <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $user->username]) ?>"
                           style="background-image: url(<?= Avatar::getAvatar($user->id)->getURL(Avatar::SMALL) ?>)"
                        >
                            <?= htmlReady($user->getFullName()) ?>
                        </a>
                    <? else: ?>
                        <?= _('unbekannt') ?>
                    <? endif; ?>
                    <? foreach ($versions as $version) : ?>
                        <? if ($version->user_id === $user_id) : ?>
                            <a href="<?= $controller->versiondiff($page, is_a($version, 'WikiVersion') ? $version->id : null) ?>"
                               title="<?= _('Einzelne Änderung anzeigen') ?>"
                               data-dialog>
                                <?= Icon::create('log')->asImg(['class' => 'text-bottom']) ?>
                            </a>
                        <? endif ?>
                   <? endforeach ?>
                    </li>
                <? endforeach ?>
                </ul>
            </td>
            <td><?= strftime('%x %X', $page->chdate) ?></td>
        </tr>
    <? endforeach ?>
    </tbody>
    <? if ($num_entries > $limit) : ?>
        <tfoot>
            <tr>
                <td colspan="4" class="actions">
                    <?= Pagination::create($num_entries, $pagenumber, $limit)->asLinks() ?>
                </td>
            </tr>
        </tfoot>
    <? endif ?>
</table>

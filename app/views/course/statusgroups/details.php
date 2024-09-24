<?php
/**
 * @var Statusgruppen $group
 */
?>
<table class="default">
    <caption class="hide-in-dialog">
        <?= sprintf(_('Gruppe %s'), htmlReady($group->name)) ?>
    </caption>
    <colgroup>
        <col style="width: 32px">
        <col>
    </colgroup>
    <tbody>
    <? foreach ($group->members as $member): ?>
        <tr>
            <td>
                <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $member->user->username], true) ?>">
                    <?= $member->avatar() ?>
                </a>
            </td>
            <td>
                <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $member->user->username], true) ?>">
                    <?= htmlReady($member->user->getFullname()) ?>
                </a>
            </td>
        </tr>
    <? endforeach; ?>
    </tbody>
</table>

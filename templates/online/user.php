<?
# Lifter010: TODO
?>
<tr class="<?= $background ? $background : 'table_row_even' ?>">

    <td width="1%" <?= $gruppe ? "class=\"$gruppe\"" : '' ?>>
        <? if (!$is_buddy) : ?>
            <a href="<?= URLHelper::getLink('online.php', ['cmd' => 'add_user',
                                                                'add_uname' => $tmp_online_uname]) ?>">
                <?= Icon::create('arr_2left', Icon::ROLE_SORT)->asImg([
                    'title' => _('zu den Buddies hinzufügen'),
                    'style' => 'padding-right: 0.33em;',
                    'class' => 'middle',
                ]) ?>
            </a>
        <? else : ?>
            &nbsp;
        <? endif ?>
    </td>

    <td width="4%">
        <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $tmp_online_uname]) ?>">
            <?= Avatar::getAvatar($tmp_user_id)->getImageTag(Avatar::SMALL) ?>
        </a>
    </td>

    <td width="66%">
        <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $tmp_online_uname]) ?>">
            <?= htmlReady($fullname) ?>
        </a>
            <? foreach (StudipKing::is_king($tmp_user_id, TRUE) as $type => $text) : ?>
                <?= Icon::create('crown', Icon::ROLE_SORT)->asImg(['title' => $text, 'class' => 'text-bottom']) ?>
            <? endforeach ?>
    </td>

    <td width="20%">
            <?= date("i:s", $zeit) ?>
    </td>

    <td width="3%" align="center">
        <? if (class_exists("Blubber")) : ?>
        <a href="<?= URLHelper::getLink('dispatch.php/course/streams/global', ['mention' => $tmp_online_uname]) ?>">
            <?= Icon::create('blubber')->asImg(['title' => _("Blubber diesen Nutzer an"), 'class' => 'text-bottom']) ?>
        </a>
        <? endif ?>
    </td>

    <td width="3%" align="center">
        <a href="<?= URLHelper::getLink('dispatch.php/messages/write', ['rec_uname' => $tmp_online_uname]) ?>">
            <?= Icon::create('mail')->asImg(['title' => _('Nachricht an Benutzer verschicken'), 'class' => 'text-bottom']) ?>
        </a>
    </td>

    <td width="3%" align="center">
        <? if ($is_buddy) : ?>
            <a href="<?= URLHelper::getLink("online.php",
                                            ["cmd" => "delete_user",
                                                  "delete_uname" => $tmp_online_uname]) ?>">
                <?= Icon::create('trash')->asImg(['title' => _('aus der Buddy-Liste entfernen'), 'class' => 'text-bottom']) ?>
            </a>
        <? else : ?>
            &nbsp;
        <? endif ?>
    </td>
</tr>

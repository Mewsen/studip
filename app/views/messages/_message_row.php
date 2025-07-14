<?php
/**
 * @var Message $message
 */
?>
<tr id="message_<?= $message->getId() ?>" class="<?= $message->isRead() || $message['autor_id'] === $GLOBALS['user']->id ? "" : "unread" ?>">
    <td class="hidden-small-down"><input type="checkbox" name="bulk[]" value="<?= htmlReady($message->getId()) ?>"></td>
    <td class="title">
        <a href="<?= URLHelper::getLink('dispatch.php/messages/read/' . $message->getId() .'/' . ($received ? 'rec' : 'snd') ) ?>" data-dialog>
            <?= trim($message['subject']) ? htmlReady($message['subject']) : htmlReady(mila(kill_format($message['message']), 40)) ?>
            <div class="message-indicators">
                <span><?= $message->getNumAttachments() ? Icon::create('staple', Icon::ROLE_INFO)->asSvg(['title' => _('Mit Anhang')]) : '' ?></span>
                <span><?= $message->isAnswered($GLOBALS['user']->id) ? Icon::create('outbox', Icon::ROLE_INFO)->asSvg(['title' => _('Beantwortet')]) : '' ?></span>
            </div>
        </a>
        <p class="hidden-medium-up responsive_author">
            <? if ($message['autor_id'] == "____%system%____") : ?>
                <?= _("Systemnachricht") ?>
            <? elseif (!$received): ?>
                <? $num_recipients = $message->getNumRecipients() ?>
                <? if ($num_recipients > 1) : ?>
                    <?= sprintf(_("%s Personen"), $num_recipients) ?>
                <? elseif (isset($message->receivers->first()->user)): ?>
                     <?= htmlReady($message->receivers->first()->user->getFullName()) ?>
                <? else: ?>
                    <?= _('unbekannt') ?>
                <? endif ?>
            <? else: ?>
                    <?= htmlReady(get_fullname($message['autor_id'])) ?>
            <? endif; ?>
        </p>
    </td>
    <td class="hidden-small-down">
    <? if ($message['autor_id'] === "____%system%____") : ?>
        <?= _("Systemnachricht") ?>
    <? elseif (isset($received) && !$received): ?>
        <? $num_recipients = $message->getNumRecipients() ?>
        <? if ($num_recipients > 1) : ?>
            <?= sprintf(_("%s Personen"), $num_recipients) ?>
        <? elseif (isset($message->receivers->first()->user)): ?>
        <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $message->receivers->first()->user->username]) ?>">
            <?= htmlReady($message->receivers->first()->user->getFullName()) ?>
        </a>
        <? else: ?>
        <?= _('unbekannt') ?>
        <? endif ?>
    <? elseif ($message->author instanceof User): ?>
        <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $message->author->username]) ?>">
            <?= htmlReady($message->author->getFullName()) ?>
        </a>
    <? else: ?>
        <?= _('unbekannt') ?>
    <? endif; ?>
    </td>
    <td><?= strftime('%x %R', $message['mkdate']) ?></td>
    <td class="tag-container hidden-small-down">
    <? foreach ($message->getTags() as $tag) : ?>
        <a href="<?= URLHelper::getLink("?", ['tag' => $tag]) ?>" class="message-tag" title="<?= _("Alle Nachrichten zu diesem Schlagwort") ?>">
            <?= htmlReady(ucfirst($tag)) ?>
        </a>
    <? endforeach ?>
    </td>
</tr>

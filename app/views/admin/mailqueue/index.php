<article class="studip">
    <table class="default sortable-table" data-sortlist="[[0, 1]]">
        <caption><?= _('Aktuelle Mailqueue-Einträge') ?></caption>

        <thead>
            <tr class="sortable">
                <th data-sort="htmldata"><?= _('Letzte Änderung') ?></th>
                <th data-empty="zero" data-sort="text"><?= _('Betreff') ?></th>
                <th data-sort="htmldata"><?= _('Message ID') ?></th>
                <th data-empty="zero" data-sort="text"><?= _('Empfänger') ?></th>
                <th data-sort="htmldata"><?= _('Versuche') ?></th>
                <th data-sort="htmldata"><?= _('Letzter Sendeversuch') ?></th>
                <th><?= _('Eintrag löschen') ?></th>
            </tr>
        </thead>

        <tbody>
        <? foreach ($this->mailqueues as $queue_entry) : ?>
            <? $mail_content = json_decode($queue_entry->mail) ?>
            <tr>
                <td data-sort-value="<?= $queue_entry->chdate ?>"><?= htmlReady( date('d.m.Y H:i', $queue_entry->chdate)) ?></td>
                <td><?= htmlReady($mail_content->subject) ?></td>
                <td data-sort-value="<?= $queue_entry->message_id ?>"><?= htmlReady($queue_entry->message_id) ?></td>
                <td><? $user = User::find($queue_entry->user_id) ?><?= htmlReady($user->vorname . ' ' . $user->nachname)?></td>
                <td data-sort-value="<?= $queue_entry->tries ?>"><?= htmlReady($queue_entry->tries) ?></td>
                <td data-sort-value="<?= $queue_entry->last_try ?>"><? if ($queue_entry->last_try != 0) echo htmlReady(date('d.m.Y H:i', $queue_entry->last_try)) ?></td>
                <td>
                    <? $actionmenu = ActionMenu::get(); ?>
                    <? $actionmenu->addLink(PluginEngine::getURL('mailqueuevisualisation', array(), "mailqueue/delete_entry/" . $queue_entry->mail_queue_id . "/new"),
                   _('Eintrag löschen'),
                    Icon::create('trash'),
                    ['data-dialog' => 'size=auto']); ?>
                    <?= $actionmenu->render(); ?>
                </td>
            </tr>
        <? endforeach ?>

        </tbody>
    </table>
</article>



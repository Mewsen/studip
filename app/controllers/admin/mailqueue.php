<?php

/**
 *
 */
class Admin_MailqueueController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!$GLOBALS['perm']->have_perm('root')) {
            throw new AccessDeniedException();
        }

    }

    public function index_action()
    {
        Navigation::activateItem('/admin/mailqueue/index');

        $export = new \ExportWidget();
        $export->addLink(
            _('Aktuelle Mailqueue als CSV exportieren'),
            $this->url_for('admin/mailqueue/export/new'),
            Icon::create('export')
        );
        \Sidebar::Get()->addWidget($export);

        $this->mailqueues = [];
        $this->mailqueues = MailQueueEntry::findBySQL("chdate >= UNIX_TIMESTAMP() - 15768000 ORDER BY chdate DESC");

        foreach ($this->mailqueues as $queue)
        {
            if ($queue->tries == 0)
            {
                $queue->tries .= ' (noch nicht gesendet)';
            }
            elseif ($queue->tries >= 1 && $queue->tries <= 24)
            {
                $queue->tries .= ' Sendeversuch(e), noch nicht zugestellt';
            }
            elseif ($queue->tries >= 25)
            {
                $queue->tries .= ' (Zustellung fehlgeschlagen)';
            }
        }

    }

    public function showoldqueue_action()
    {
        Navigation::activateItem('/admin/mailqueue/oldqueue');
        $export = new \ExportWidget();
        $export->addLink(
            _('Alte Mailqueue als CSV exportieren'),
            $this->url_for('admin/mailqueue/export/old' ),
            Icon::create('export')
        );
        \Sidebar::Get()->addWidget($export);

        $actions = new ActionsWidget();
        $actions->addLink(
            _('Alte Mailqueue-Einträge löschen'),
            $this->url_for('admin/mailqueue/delete_old'),
            Icon::create('trash')

        )->asDialog('size=auto');
        Sidebar::Get()->addWidget($actions);

        $this->old_mailqueues = [];
        $this->old_mailqueues = MailQueueEntry::findBySQL("chdate < UNIX_TIMESTAMP() - 15768000 ORDER BY chdate DESC");
        foreach ($this->old_mailqueues as $queue)
        {
            if ($queue->tries == 0)
            {
                $queue->tries .= ' (noch nicht gesendet)';
            }
            elseif ($queue->tries >= 1 && $queue->tries <= 24)
            {
                $queue->tries .= ' Sendeversuch(e), noch nicht zugestellt';
            }
            elseif ($queue->tries >= 25)
            {
                $queue->tries .= ' (Zustellung fehlgeschlagen)';
            }
        }
    }

    public function delete_entry_action($queue_id, $oldornew)
    {
        PageLayout::setTitle('Eintrag löschen');
        $this->oldornew = $oldornew;
        $this->queue_id = $queue_id;
        $queue_entry = MailQueueEntry::find($queue_id);

        if (Request::isPost())
        {
            CSRFProtection::verifyUnsafeRequest();
            $queue_entry->delete();

            if ($oldornew == 'new') {
                $this->relocate('admin/mailqueue/index');
            } else if ($oldornew == 'old') {
                $this->relocate('admin/mailqueue/showoldqueue');
            }
            PageLayout::postSuccess('Eintrag wurde gelöscht.');
        }

    }

    public function delete_old_action()
    {
        $this->old_mailqueues = MailQueueEntry::findBySQL("chdate < UNIX_TIMESTAMP() - 15768000");

        if (Request::isPost())
        {
            CSRFProtection::verifyUnsafeRequest();

            foreach ($this->old_mailqueues as $mailqueue) {
                $mailqueue->delete();
            }
            $this->relocate('mailqueue/showqueue');
            PageLayout::postSuccess('Alle Mailqueue-Einträge, die mindestens ein Jahr alt sind, wurden gelöscht.');
        }

    }

    public function export_action($oldornew)
    {
        if ($oldornew == 'new') {
            $this->mailqueues = MailQueueEntry::findBySQL("chdate >= UNIX_TIMESTAMP() - 15768000");
            $filename = FileManager::cleanFileName(
                sprintf(
                    'mailqueue-export-aktuell.csv',
                    \Context::getHeaderLine()
                )
            );
        } else if ($oldornew == 'old') {
            $this->mailqueues = MailQueueEntry::findBySQL("chdate < UNIX_TIMESTAMP() - 15768000");
            $filename = FileManager::cleanFileName(
                sprintf(
                    'mailqueue-export-alt.csv',
                    \Context::getHeaderLine()
                )
            );
        }

        $captions = [
            _('Erstelldatum'),
            _('Betreff'),
            _('Message ID'),
            _('Empfänger'),
            _('Versuche'),
            _('Letzter Sendeversuch')
        ];

        $data = [];
        foreach ($this->mailqueues as $queue_entry)
        {
            $row['erstelldatum'] = date('d.m.Y H:i',$queue_entry->chdate);
            $message = Message::find($queue_entry->message_id);
            $row['betreff'] = $message->subject;
            $row['message_id'] =$queue_entry->message_id;

            $user = User::find($queue_entry->user_id);
            $row['empfaenger'] = $user->vorname . ' ' . $user->nachname;

            $row['versuche'] = $queue_entry->tries;
            $row['lastsend'] = date('d.m.Y H:i',$queue_entry->last_try);

            $data[] = array_values($row);
        }
        $this->render_csv(array_merge([$captions], $data), $filename);
    }

}

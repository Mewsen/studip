<?php

class Massmail_MessageController extends \AuthenticatedController
{

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!\MassMail\MassMailPermission::has(User::findCurrent()->id)) {
            throw new AccessDeniedException();
        }
    }

    public function index_action($id = null)
    {
        Navigation::activateItem('/messaging/massmail/message');
        PageLayout::setTitle(_('Nachricht an Zielgruppe schreiben'));

        $message = new \MassMail\MassMailMessage($id);

        $temp_id = $id ?: md5(uniqid(time()));
        $folder = $message->findFolder($temp_id);

        // SearchType needed for course selection
        $courseSearch = new StandardSearch('Seminar_id');

        // SearchType needed for user
        $userSearch = new StandardSearch('user_id');

        $form = \Studip\Forms\Form::fromSORM(
            $message,
            [
                'legend' => _('Grunddaten'),
                'collapsed' => false,
                'collapsable' => false,
                'fields' => [
                    'target' => [
                        'type' => 'select',
                        'required' => true,
                        'label' => _('Zielgruppe'),
                        'value' => $message->target ?? 'all',
                        'options' => \MassMail\MassMailMessage::getTargets()
                    ],
                    'student_filters' => [
                        'type' => 'userFilter',
                        'label' => _('Auswahlfilter'),
                        'if' => 'target === "students"',
                        'context' => 'MassMail',
                        'target' => 'students',
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'students') {
                                $filters = [];
                                foreach ($value as $one) {
                                    $filter = new UserFilter($one['id'] ?? '');
                                    $filter->fields = [];
                                    foreach ($one['attributes']['fields'] as $field) {
                                        $classname = $field['attributes']['type'];
                                        $f = new $classname();
                                        if (!empty($fiele['id'])) {
                                            $f->setId($field['id']);
                                        }
                                        $f->setCompareOperator($field['attributes']['compare-operator']);
                                        $f->setValue($field['attributes']['value']);
                                        $filter->addField($f);
                                    }
                                    $filter->store();
                                    $connection = new \MassMail\MassMailFilter();
                                    $connection->filter_id = $filter->getId();
                                    $filters[] = $connection;
                                }
                                $input->getContextObject()->filters = $filters;
                            }
                        }
                    ],
                    'employee_filters' => [
                        'type' => 'userFilter',
                        'label' => _('Auswahlfilter'),
                        'if' => 'target === "employees"',
                        'context' => 'MassMail',
                        'target' => 'employees',
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'employees') {
                                $filters = [];
                                foreach ($value as $one) {
                                    $filter = new UserFilter($one['id'] ?? '');
                                    $filter->fields = [];
                                    foreach ($one['attributes']['fields'] as $field) {
                                        $classname = $field['attributes']['type'];
                                        $f = new $classname();
                                        if (!empty($fiele['id'])) {
                                            $f->setId($field['id']);
                                        }
                                        $f->setCompareOperator($field['attributes']['compare-operator']);
                                        $f->setValue($field['attributes']['value']);
                                        $filter->addField($f);
                                    }
                                    $filter->store();
                                    $connection = new \MassMail\MassMailFilter();
                                    $connection->filter_id = $filter->getId();
                                    $filters[] = $connection;
                                }
                                $input->getContextObject()->filters = $filters;
                            }
                        }
                    ],
                    'semester' => [
                        'type' => 'select',
                        'label' => _('Semester wählen'),
                        'value' => $message->config['semester'] ?? \Semester::findDefault()->id,
                        'if' => 'target === "lecturers"',
                        'options' => \MassMail\MassMailMessage::getSemesters(),
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'lecturers') {
                                $input->getContextObject()->config = ['semester' => $value];
                            }
                        }
                    ],
                    'courses' => [
                        'type' => 'quicksearchList',
                        'label' => _('Veranstaltungen wählen'),
                        'value' => json_encode($message->config?->getArrayCopy()['courses'] ?? []),
                        'if' => 'target === "courses"',
                        'searchtype' => $courseSearch,
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'courses') {
                                $input->getContextObject()->config = [];
                                $input->getContextObject()->config['courses'] = \Course::findAndMapMany(
                                    function ($course) {
                                        return ['id' => $course->id, 'name' => $course->getFullname()];
                                    },
                                    json_decode($value, true)
                                );
                            }
                        }
                    ],
                    'course_perm' => [
                        'type' => 'multiselect',
                        'label' => _('Berechtigungsebene wählen'),
                        'value' => $message->config['perm'] ?? ['autor'],
                        'if' => 'target === "courses"',
                        'options' => [
                            'dozent' => get_title_for_status('dozent', 2, 1),
                            'tutor' => get_title_for_status('tutor', 2, 1),
                            'autor' => get_title_for_status('autor', 2, 1),
                            'user' => get_title_for_status('user', 2, 1),
                        ],
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'courses') {
                                $input->getContextObject()->config['perm'] = $value;
                            }
                        }
                    ],
                    'manual_usernames' => [
                        'type' => 'textarea',
                        'label' => _('Liste von Benutzernamen, durch Zeilenumbruch getrennt'),
                        'if' => 'target === "usernames"',
                        'value' => $message->config['usernames'] ?? '',
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'usernames') {
                                $input->getContextObject()->config = [];
                                $input->getContextObject()->config['usernames'] = $value;
                            }
                        }
                    ],
                    'subject' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => _('Betreff'),
                        'value' => $message->subject
                    ],
                    'message' => [
                        'type' => 'serialWysiwyg',
                        'required' => true,
                        'label' => _('Nachricht'),
                        'value' => $message->message,
                        'markers' => json_encode(
                            array_map(
                                fn ($m) => $m->toArray(),
                                \MassMail\MassMailMarker::findAll(
                                    \MassMail\MassMailPermission::has(User::findCurrent()->id, true)
                                )
                            )
                        )
                    ]
                ]
            ],
            $this->url_for('massmail/overview')
        )->addSORM($message, [
            'legend' => _('Weitere Einstellungen'),
            'collapsable' => true,
            'collapsed' => true,
            'fields' => [
                'author_id' => [
                    'type' => 'hidden',
                    'value' => User::findCurrent()->id
                ],
                'attachments' => [
                    'type' => 'file',
                    'label' => _('Dateianhänge auswählen'),
                    'value' => $message->folder_id ?? $message->folder_id = $folder->id,
                    'upload_url' => $this->url_for('massmail/message/attachments', $folder->id),
                    'multiple' => true,
                    'if' => $GLOBALS['ENABLE_EMAIL_ATTACHMENTS']
                        ? 'true' : 'false',
                    'store' => function($value, $input) {
                        $input->getContextObject()->folder_id = $value;
                    }
                ],
                'tokens' => [
                    'type' => 'file',
                    'label' => _('CSV mit Teilnahmecodes auswählen'),
                    'value' => $message->folder_id ?? $message->folder_id = $folder->id,
                    'upload_url' => $this->url_for('massmail/message/tokens', $message->folder_id),
                    'accept' => '.csv,.txt',
                    'if' => \MassMail\MassMailPermission::has(User::findCurrent()->id, true)
                        ? 'true' : 'false',
                    'store' => function($value, $input) {
                        $input->getContextObject()->folder_id = $value;
                    }
                ],
                'send_at_date' => [
                    'type' => 'datetimepicker',
                    'label' => _('Zu einem späteren Zeitpunkt senden'),
                    'value' => $message->send_at_date ?? time()
                ],
                'send_as' => [
                    'type' => 'select',
                    'label' => ('Nachricht senden als'),
                    'value' => $message->sender_id ?? User::findCurrent()->id,
                    'if' => \MassMail\MassMailPermission::has(User::findCurrent()->id, true)
                        ? 'true' : 'false',
                    'options' => [
                        User::findCurrent()->id => _('Von meiner Kennung verschicken'),
                        'user_id' => _('Eine andere Person eintragen'),
                        '____%system%____' => _('Anonym, mit "Stud.IP" als Absender')
                    ],
                    'store' => function($value, $input) {
                        if ($value === User::findCurrent()->id || $value === '____%system%____') {
                            $input->getContextObject()->sender_id = $value;
                        }
                    }
                ],
                'sender_id' => [
                    'type' => 'quicksearch',
                    'label' => _('Absender:in wählen'),
                    'value' => $message->sender_id ?? '',
                    'if' => 'send_as === "user_id"',
                    'searchtype' => $userSearch,
                    'store' => function($value, $input) {
                        $sender_id = $input->getContextObject()->sender_id;
                        if ($sender_id !== User::findCurrent()->id && $sender_id !== '____%system%____') {
                            $input->sender_id = $value;
                        }
                    }
                ],
                'exclude_users' => [
                    'type' => 'textarea',
                    'label' => _('Liste von Benutzernamen, die die Nachricht nicht erhalten sollen'),
                    'value' => $message->exclude_users ?? ''
                ],
                'cc' => [
                    'type' => 'textarea',
                    'label' => _('Liste von Benutzernamen, die die Nachricht als Kopie erhalten sollen'),
                    'value' => $message->cc ?? ''
                ],
                'flags' => [
                    'type' => 'radio',
                    'label' => _('Besondere Kennzeichnung'),
                    'value' => $message->is_template
                        ? 'is_template'
                        : ($message->protected ? 'protected' : ''),
                    'options' => [
                        '' => _('Keine besondere Kennzeichnung'),
                        'is_template' => _('Nicht verschicken, sondern als Vorlage speichern'),
                        'protected' => _('Auch nach dem Versand dauerhaft speichern')
                    ],
                    'store' => function($value, $input) {
                        switch ($value) {
                            case 'is_template':
                                $input->getContextObject()->is_template = 1;
                                $input->getContextObject()->protected = 0;
                                break;
                            case 'protected':
                                $input->getContextObject()->is_template = 0;
                                $input->getContextObject()->protected = 1;
                                break;
                            default:
                                $input->getContextObject()->is_template = 0;
                                $input->getContextObject()->protected = 0;
                                break;
                        }
                    }
                ]
            ]
        ])->addStoreCallback(function ($form) {
            $message = $form->getLastPart()->getContextObject();

            // Adjust folder range_id to the actual message id.
            $folder = Folder::find($message->folder_id);
            $folder->range_id = $message->id;
            $folder->store();

            // Create message tokens if necessary.
            if ($message->hasMarkers('token')) {
                foreach ($folder->getTypedFolder()->getFiles() as $ref) {
                    if (isset($ref->file->metadata['is_token_file'])) {
                        $file = fopen($ref->file->getPath(), 'r');
                        while (!feof($file)) {
                            $token = fgets($file);
                            $t = new \MassMail\MassMailToken();
                            $t->message_id = $message->id;
                            $t->token = $token;
                            $t->store();
                        }
                    }
                }
            }
        })->autoStore();

        if (Config::get()->MASSMAIL_EXPORT_RECIPIENTS_ENABLE) {
            $form->addButton(
                \Studip\Button::create(_('Zielgruppe exportieren'),
                    'export',
                    ['onclick' => 'STUDIP.MassMail.exportRecipients(event)']
                ));
        }

        $this->render_form($form);
    }

    public function delete_action(int $id)
    {
        $message = \MassMail\MassMailMessage::find($id);

        if (
            !$message
            || (
                $message->author_id !== User::findCurrent()->id
                && !\MassMail\MassMailPermission::has(User::findCurrent()->id, true)
            )
        ) {
            throw new AccessDeniedException();
        }

        if ($message->delete() !== false) {
            PageLayout::postSuccess(_('Die Nachricht wurde gelöscht.'));
        } else {
            PageLayout::postError(_('Die Nachricht konnte nicht gelöscht werden.'));
        }

        $this->relocate('massmail/overview');
    }

    public function attachments_action(string $folder_id)
    {
        if (!$GLOBALS['ENABLE_EMAIL_ATTACHMENTS']) {
            throw new AccessDeniedException();
        }

        $folder = Folder::find($folder_id)->getTypedFolder();
        $uploaded = FileManager::handleFileUpload($_FILES['attachments'], $folder);

        if (!empty($uploaded['error'])) {
            $this->set_status(400);
            $this->render_text(implode('<br>' . $uploaded['error']));
        } else {
            $this->render_nothing();
        }
    }

    public function tokens_action(string $folder_id)
    {
        if (!\MassMail\MassMailPermission::has(User::findCurrent()->id, true)) {
            throw new AccessDeniedException();
        }

        $data = [
            'name'     => [$_FILES['tokens']['name']],
            'tmp_name' => [$_FILES['tokens']['tmp_name']],
            'type'     => [$_FILES['tokens']['type']],
            'error'    => [$_FILES['tokens']['error']],
            'size'     => [$_FILES['tokens']['size']],
        ];

        $folder = Folder::find($folder_id)->getTypedFolder();
        $uploaded = FileManager::handleFileUpload($data, $folder);

        if (!empty($uploaded['error'])) {
            $this->set_status(400);
            $this->render_text(implode('<br>' . $uploaded['error']));
        } else {

            // Set metadata for created file, indicating that this is a file with message tokens.
            foreach ($uploaded['files'] as $ref) {
                $ref->file->metadata = ['is_token_file' => true];
                $ref->file->store();
            }

            $this->render_nothing();
        }
    }

    public function export_action()
    {
        $message = new MassMail\MassMailMessage();
        $message->target = Request::get('target');
        $message->author_id = User::findCurrent()->id;

        $data = [[_('Zielgruppe: alle')]];
        $currentRow = 2;

        switch($message->target) {
            case 'students':
            case 'employees':
                $data = [[_('Zielgruppe:') . ' ' .
                    ($message->target === 'students' ? _('Studierende') : _('Beschäftigte'))]];

                $value = json_decode(
                    Request::get($message->target === 'students' ? 'student_filters' : 'employee_filters', '[]'),
                    true
                );
                $filters = [];
                foreach ($value as $one) {
                    $filter = new UserFilter();
                    $filter->fields = [];
                    foreach ($one['attributes']['fields'] as $field) {
                        $classname = $field['attributes']['type'];
                        $f = new $classname();
                        if (!empty($fiele['id'])) {
                            $f->setId($field['id']);
                        }
                        $f->setCompareOperator($field['attributes']['compare-operator']);
                        $f->setValue($field['attributes']['value']);
                        $filter->addField($f);
                    }
                    $filter->store();
                    $connection = new \MassMail\MassMailFilter();
                    $connection->filter_id = $filter->getId();
                    $filters[] = $connection;

                    $data[] = [strip_tags($filter->toString())];
                    $currentRow++;
                }
                $message->filters = $filters;

                break;
            case 'lecturers':
                $data = [[_('Zielgruppe:') . ' ' . _('Aktive Lehrende')
                    . ' (' . Semester::find(Request::get('semester'))->name . ')']];

                $message->config = json_encode([
                    'semester' => Request::get('semester')
                ]);
                break;
            case 'courses':
                $data = [[_('Zielgruppe:') . ' ' . _('Veranstaltungen')]];

                $message->config = json_encode([
                    'courses' => Request::getArray('courses'),
                    'perm' => Request::get('course_perm')
                ]);
                break;
            case 'usernames':
                $data = [[_('Zielgruppe:') . ' ' . _('Manuell gewählte Nutzernamen')]];

                $message->config = json_encode(['usernames' => Request::getArray('manual_usernames')]);
                break;
        }
        $now = time();

        $data[] = [sprintf(_('Stand der Daten vom %s'), date('d.m.Y, H:i', $now))];
        $data[] = [''];
        $data[] = [_('Nutzername'), _('Nachname'), _('Vorname')];

        $data = array_merge(
            $data,
            User::findAndMapBySQL(
                fn ($user) => [$user->username, $user->nachname, $user->vorname],
                "`username` IN (:usernames) ORDER BY `Nachname`, `Vorname`, `username`",
                ['usernames' => $message->getRecipients() ?? ['']]
            )
        );

        $xls = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $xls->getProperties()
            ->setCreator(User::findCurrent()->getFullname())
            ->setLastModifiedBy(User::findCurrent()->getFullname())
            ->setTitle('Zielgruppenexport');
        $sheet = $xls->getActiveSheet();

        $style = $sheet->getStyle('A1:C1');
        $style->getFill()
            ->setFillType(PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('dddddd');
        $style->getFont()
            ->setSize(14)
            ->setBold(true);

        $style2 = $sheet->getStyle('A2:C' . $currentRow);
        $style2->getFill()
            ->setFillType(PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('dddddd');
        $style2->getFont()
            ->setSize(12)
            ->setBold(true);

        $style3 = $sheet->getStyle('A' . ($currentRow + 2) . ':C' . ($currentRow + 2));
        $style3->getFont()
            ->setBold(true);

        foreach (['A', 'B', 'C'] as $column) {
            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $sheet->fromArray($data);

        $tmpname = tempnam($GLOBALS['TMP_PATH'], '');
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($xls);
        $writer->save($tmpname);

        $this->render_text(
            FileManager::getDownloadURLForTemporaryFile(
                $tmpname,
                'zielgruppe-' . date('Y-m-d-h-i', $now) . '.xlsx'
            )
        );
    }

}

<?php

class Oer_MymaterialController extends AuthenticatedController
{
    protected $_autobind = true;

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        if (
            !Config::get()->OERCAMPUS_ENABLED
            || !$GLOBALS['perm']->have_perm(Config::get()->OER_PUBLIC_STATUS)
        ) {
            throw new AccessDeniedException();
        }
        PageLayout::setTitle(_('Lernmaterialien'));
    }

    public function index_action()
    {
        if (Navigation::hasItem("/oer/mymaterial")) {
            Navigation::activateItem("/oer/mymaterial");
        }
        $this->materialien = OERMaterial::findMine();
        $this->buildSidebar();
    }

    public function edit_action(OERMaterial $material = null)
    {
        $this->validateMaterial($material);

        PageLayout::setTitle($material->isNew() ? _('Neues Material hochladen') : _('Material bearbeiten'));

        $tagsearch = new SQLSearch(
            "SELECT oer_tags.name, oer_tags.name
             FROM oer_tags
             WHERE name LIKE :input
             ORDER BY oer_tags.name",
            _('Thema suchen')
        );

        $this->render_vue_app(
            Studip\VueApp::create('OERMaterialEditor')
                ->withProps([
                    'store-url' => $this->storeURL($material),
                    'material' => [
                        ...$material->toArray(),

                        'filesize' => $material->getFilePath() && file_exists($material->getFilePath()) ? filesize($material->getFilePath()) : null,
                        'logoUrl'  => $material->getLogoURL(),
                        'tags' => array_column($material->getTopics(), 'name'),
                        'users' => $material->users->map(function (OERMaterialUser $user) {
                            if ($user->external_contact) {
                                return [
                                    'id'       => $user->oeruser->id,
                                    'name'     => $user->oeruser->name,
                                    'avatar'   => $user->oeruser->avatar_url,
                                    'external' => true,
                                ];
                            }

                            $u = User::find($user->user_id);
                            return [
                                'id'       => $u->user_id,
                                'avatar'   => Avatar::getAvatar($u->id)->getURL(Avatar::SMALL),
                                'name'     => $u ? $u->getFullName() : _('unbekannt'),
                                'external' => false,
                            ];
                        }),
                    ],
                    'template'         => $_SESSION['NEW_OER'] ?? null,
                    'tag-search'       => (string) $tagsearch,
                    'licenses-enabled' => !Config::get()->getValue('OER_DISABLE_LICENSE'),
                    'licenses'         => License::findAndMapBySQL(
                        function (License $license) {
                            return [
                                'id'   => $license->id,
                                'name' => $license->name,
                            ];
                        },
                        '1 ORDER BY name'
                    ),
                    'enable-twillo' => $this->isTwilloEnabled(),
                ])
        );

    }

    public function store_action(OERMaterial $material = null)
    {
        $material = $this->validateMaterial($material);

        CSRFProtection::verifyUnsafeRequest();

        $content_types = ['application/x-zip-compressed', 'application/zip', 'application/x-zip'];
        $tmp_folder = $GLOBALS['TMP_PATH'] . '/temp_folder_' . md5(uniqid());

        $was_new = $material->isNew();
        $was_on_twillo = (bool) $material->published_id_on_twillo;
        $data = Request::getArray('data');
        $material->setData($data);
        if ($data['player_url'] && !$material->hasValidPreviewUrl()) {
            PageLayout::postWarning(_('Die angegebene URL muss mit http(s) beginnen.'));
            $material->player_url = '';
        }
        $material->host_id = null;
        if (!empty($_FILES['file']['tmp_name'])) {
            $material->content_type = get_mime_type($_FILES['file']['name']);
            if (in_array($material->content_type, $content_types)) {
                mkdir($tmp_folder);
                \Studip\ZipArchive::extractToPath($_FILES['file']['tmp_name'], $tmp_folder);
                $material->structure = $this->getFolderStructure($tmp_folder);
                rmdirr($tmp_folder);
            } else {
                $material->structure = null;
            }
            $material->filename = $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $material->getFilePath());
        } elseif (!empty($_SESSION['NEW_OER']['tmp_name'])) {
            $material->content_type = $_SESSION['NEW_OER']['content_type'] ?: get_mime_type($_SESSION['NEW_OER']['tmp_name']);
            if (in_array($material->content_type, $content_types)) {
                mkdir($tmp_folder);
                \Studip\ZipArchive::extractToPath($_SESSION['NEW_OER']['tmp_name'], $tmp_folder);
                $material->structure = $this->getFolderStructure($tmp_folder);
                rmdirr($tmp_folder);
            } else {
                $material->structure = null;
            }
            $material->filename = $_SESSION['NEW_OER']['filename'];
            copy($_SESSION['NEW_OER']['tmp_name'], $material->getFilePath());
        }


        if (
            !empty($_FILES['image']['tmp_name'])
            && getimagesize($_FILES['image']['tmp_name']) !== false
        ) {
            $material->front_image_content_type = get_mime_type($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $material->getFrontImageFilePath());
        } elseif (!empty($_SESSION['NEW_OER']['image_tmp_name'])) {
            $material->front_image_content_type = get_mime_type($_SESSION['NEW_OER']['image_tmp_name']);
            copy($_SESSION['NEW_OER']['image_tmp_name'], $material->getFrontImageFilePath());
        }
        if (Request::get('delete_front_image')) {
            $material->front_image_content_type = null;
        }
        if ($material->isNew() && $material->category === 'auto') {
            $material->category = $material->autoDetectCategory();
        }
        $material->store();

        if ($was_new) {
            OERMaterialUser::create([
                'material_id'      => $material->id,
                'user_id'          => User::findCurrent()->id,
                'external_contact' => false,
                'position'         => 1,
            ]);
            $material->notifyFollowersAboutNewMaterial();
        }
        $removed_users = Request::getArray('remove_users');
        foreach (Request::getArray('remove_users') as $index => $user) {
            if (!$index && count($removed_users) === count($material->users)) {
                continue;
            }
            [$external, $user_id] = array_map('trim', explode('_', $user));
            OERMaterialUser::deleteBySQL(
                'user_id = ? AND material_id = ? AND external_contact = ?',
                [$user_id, $material->getId(), $external]
            );
        }

        //Topics:
        $tags = Request::getArray('tags');
        $material->setTopics($tags);

        $material->pushDataToIndexServers();

        if ($this->isTwilloEnabled()) {
            if (
                Request::bool('publish_on_twillo')
                || !empty($_SESSION['NEW_OER']['publish_on_twillo'])
            ) {
                //upload it to twillo.de
                $succes_or_error = $material->uploadToTwillo();
                if (is_string($succes_or_error)) {
                    PageLayout::postWarning(
                        _('Konnte Material nicht zu twillo.de hochladen.'),
                        [htmlReady($succes_or_error)]
                    );
                }
            } elseif ($was_on_twillo) {
                //remove it from twillo.de if able
                $material->deleteFromTwillo();
            }
        }

        unset($_SESSION['NEW_OER']);
        PageLayout::postSuccess(_('Lernmaterial erfolgreich gespeichert.'));

        $redirect_url = Request::get('redirect_url');
        if (!$redirect_url) {
            $this->redirect('oer/market/details/' . $material->id);
        } elseif ($redirect_url === 'files') {
            $this->redirect(
                URLHelper::getURL(
                    'dispatch.php/course/files/index/' . Request::get('dir'),
                    ['cid' => Request::get('cid')]
                )
            );
        } else {
            $this->redirect(URLHelper::getURL($redirect_url, [
                'material_id' => $material->id,
                'url' => $this->url_for('oer/market/details/' . $material->id)
            ]));
        }
    }

    public function delete_action(OERMaterial $material)
    {
        $material = $this->validateMaterial($material);

        if (Request::isPost()) {
            $material->pushDataToIndexServers('delete');
            $material->delete();
            PageLayout::postSuccess(_('Das Material wurde gelöscht.'));
            $this->redirect('oer/market/index');
            return;
        } else {
            throw new Exception("Use this route with POST.");
        }
    }

    public function statistics_action(OERMaterial $material)
    {
        $material = $this->validateMaterial($material);

        PageLayout::setTitle(sprintf(
            _('Zugriffszahlen für %s'),
            $material->name
        ));
        if (Request::get("export")) {
            $this->counter = OERDownloadcounter::findBySQL(
                'material_id = ? ORDER BY mkdate DESC',
                [$material->id]
            );
            $output = [
                ['Datum', 'Longitude', 'Latitude']
            ];
            foreach ($this->counter as $counter) {
                $output[] = [
                    date('Y-m-d H:i:s', $counter['mkdate']),
                    $counter['longitude'],
                    $counter['latitude']
                ];
            }

            $this->render_csv($output, FileManager::cleanFileName('Zugriffszahlen ' . $material->name . '.csv'));
            return;
        }
        $this->counter = OERDownloadcounter::countBySQL("material_id = ?", [$material->id]);
        $this->counter_today = OERDownloadcounter::countBySQL("material_id = :material_id AND mkdate >= :start", [
            'material_id' => $material->id,
            'start' => mktime(0, 0, 0)
        ]);
    }

    public function show_tmp_image_action()
    {
        if ($_SESSION['NEW_OER']['image_tmp_name'] && file_exists($_SESSION['NEW_OER']['image_tmp_name'])) {
            $this->render_file(
                $_SESSION['NEW_OER']['image_tmp_name'],
                null,
                null,
                'inline'
            );
        } else {
            throw new Exception(_("Datei ist nicht vorhanden"));
        }
    }


    protected function getFolderStructure($folder)
    {
        $structure = [];
        foreach (scandir($folder) as $file) {
            if (!in_array($file, [".", ".."])) {
                $attributes = [
                    'is_folder' => is_dir($folder . "/" . $file) ? 1 : 0
                ];
                if (is_dir($folder . "/" . $file)) {
                    $attributes['structure'] = $this->getFolderStructure($folder . "/" . $file);
                } else {
                    $attributes['size'] = filesize($folder . "/" . $file);
                }
                $structure[$file] = $attributes;
            }
        }
        return $structure;
    }

    public function add_tag_action()
    {
        if (!Request::isPost()) {
            throw new AccessDeniedException();
        }
        $this->material = new OERMaterial(Request::option('material_id'));
        $this->render_nothing();
    }

    private function buildSidebar()
    {
        $actions = new ActionsWidget();
        $actions->addLink(
            _('Neues Lernmaterial hochladen'),
            $this->editURL(),
            Icon::create('add'),
            ['data-dialog' => 'size=auto']
        );

        Sidebar::Get()->addWidget($actions);
    }

    private function isTwilloEnabled(): bool
    {
        return Config::get()->getValue('OERCAMPUS_ENABLE_TWILLO')
            && TwilloConnector::getTwilloUserID();
    }

    private function validateMaterial(OERMaterial $material): OERMaterial
    {
        if (!$material->isNew() && !$material->isMine() && !$GLOBALS['perm']->have_perm('root')) {
            throw new AccessDeniedException();
        }

        return $material;
    }
}

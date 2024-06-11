<? if ($topFolder): ?>
    <?php
    if (empty($controllerpath)) {
        $controllerpath = 'files/index';
        if ($topFolder->range_type !== 'user') {
            $controllerpath = $topFolder->range_type . '/' . $controllerpath;
        }
    }

    $show_downloads = Config::get()->DISPLAY_DOWNLOAD_COUNTER === 'always';
    $vue_breadcrumbs = [];
    $folder = $topFolder;
    do {
        $vue_breadcrumbs[] = [
            'folder_id' => $folder->getId(),
            'name' => $folder->name,
            'url' => $controller->url_for($controllerpath . '/' . $folder->getId())
        ];
    } while ($folder = $folder->getParent());

    $vue_topFolder = [
        'description' => $topFolder->getDescriptionTemplate(),
        'additionalColumns' => $topFolder->getAdditionalColumns(),
        'buttons' => null
    ];
    if ($vue_topFolder['description'] instanceof Flexi\Template) {
        $vue_topFolder['description'] = $vue_topFolder['description']->render();
    }
    $vue_files = [];
    foreach ($topFolder->getFiles() as $file) {
        if ($file->isVisible($GLOBALS['user']->id)) {
            $vue_files[] = FilesystemVueDataManager::getFileVueData($file, $topFolder, $last_visitdate);
        }
    }
    $vue_folders = [];
    foreach ($topFolder->getSubfolders() as $folder) {
        if ($folder->isVisible($GLOBALS['user']->id)) {
            $vue_folders[] = FilesystemVueDataManager::getFolderVueData($folder, $topFolder, $last_visitdate);
        }
    }

    $vue_topFolder['buttons'] = '<span class="multibuttons">';
    $vue_topFolder['buttons'] .= Studip\Button::create(_('Herunterladen'), 'download', [
        'data-activates-condition' => 'table.documents tr[data-permissions*=d] :checkbox:checked'
    ]);
    if ($topFolder->isWritable($GLOBALS['user']->id)) {
        $vue_topFolder['buttons'] .= Studip\Button::create(_('Verschieben'), 'move', [
            'formaction'  => $controller->url_for('file/choose_destination/move/bulk'),
            'data-dialog' => 'size=auto',
            'data-activates-condition' => 'table.documents tr[data-permissions*=w] :checkbox:checked'
        ]);
    }
    if ($topFolder->isReadable($GLOBALS['user']->id)) {
        $vue_topFolder['buttons'] .= Studip\Button::create(_('Kopieren'), 'copy', [
            'formaction'  => $controller->url_for('file/choose_destination/copy/bulk'),
            'data-dialog' => 'size=auto',
            'data-activates-condition' => 'table.documents tr[data-permissions*=r] :checkbox:checked'
        ]);
    }
    if ($topFolder->isWritable($GLOBALS['user']->id)) {
        $vue_topFolder['buttons'] .= Studip\Button::create(_('Löschen'), 'delete', [
            'data-confirm'             => _('Soll die Auswahl wirklich gelöscht werden?'),
            'data-activates-condition' => 'table.documents tr[data-permissions*=w] :checkbox:checked'
        ]);
    }
    $vue_topFolder['buttons'] .= '</span>';
    if ($topFolder->isSubfolderAllowed($GLOBALS['user']->id)) {
        $vue_topFolder['buttons'] .= Studip\LinkButton::create(
            _('Neuer Ordner'),
            $controller->url_for('file/new_folder/' . $topFolder->getId()),
            ['data-dialog' => '']
        );
    }
    if ($topFolder->isWritable($GLOBALS['user']->id)) {
        $vue_topFolder['buttons'] .= Studip\LinkButton::create(_('Dokument hinzufügen'), '#', [
            'onclick' => 'STUDIP.Files.openAddFilesWindow(); return false;'
        ]);
    }
    foreach ($topFolder->getAdditionalActionButtons() as $button) {
        $vue_topFolder['buttons'] .= $button;
    }
    ?>

    <? if (!empty($show_file_search)) : ?>
        <form class="default" method="get" action="<?= $controller->link_for('files_dashboard/search') ?>">
            <?= $this->render_partial('files_dashboard/_input-group-search') ?>
        </form>
    <? endif ?>

    <form method="post" action="<?= $controller->link_for('file/bulk/' . $topFolder->getId()) ?>">
        <?= CSRFProtection::tokenTag() ?>
        <input type="hidden" name="parent_folder_id" value="<?= $topFolder->getId() ?>">

        <?= Studip\VueApp::create('FilesTable')
                ->withProps([
                    'breadcrumbs'   => $vue_breadcrumbs,
                    'files'         => $vue_files,
                    'folders'       => $vue_folders,
                    'showdownloads' => $show_downloads,
                    'topfolder'     => $vue_topFolder,
                ]) ?>
    </form>
    <? if (User::findCurrent()) : ?>

        <?= $this->render_partial('file/upload_window.php') ?>
        <?= $this->render_partial('file/add_files_window.php', [
            'folder_id' => $topFolder->getId(),
            'hidden'    => true,
            'range'   => $topFolder instanceof StandardFolder ? $topFolder->getRangeObject() : null,
            'upload_type' => FileManager::getUploadTypeConfig($topFolder->range_id, $GLOBALS['user']->id),
            'show_library_functions' => Config::get()->LITERATURE_ENABLE,
            'library_search_description' => Config::get()->LIBRARY_ADD_ITEM_ACTION_DESCRIPTION
        ]) ?>
    <? endif ?>
<? endif ?>
<?= Feedback::getHTML($topFolder->getId(), 'Folder') ?>

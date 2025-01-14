<?php
/**
 * StandardFolder.php
 *
 * the standard folder is the default implementation for folders from courses institutes
 * and users. Special folders should extend this class
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author    André Noack <noack@data-quest.de>
 * @copyright 2016 Stud.IP Core-Group
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category  Stud.IP
 */
class StandardFolder implements FolderType
{

    public static $sorter = 0;

    /**
     * @var Folder
     */
    protected $folderdata;

    /**
     * StandardFolder constructor.
     * @param Folder|StandardFolder|null $folderdata
     */
    public function __construct($folderdata = null)
    {
        if ($folderdata instanceof StandardFolder) {
            $this->folderdata = $folderdata->folderdata;
        } elseif ($folderdata instanceof Folder) {
            $this->folderdata = $folderdata;
        } else {
            $this->folderdata = Folder::build($folderdata);
        }
        $this->folderdata['folder_type'] = get_class($this);
    }

    /**
     * @return string
     */
    public static function getTypeName(): string
    {
        return _('Ordner');
    }

    public static function availableInRange(SimpleORMap|string $range_id_or_object, string $user_id): bool
    {
        return true;
    }

    /**
     * @param string $role
     * @return Icon
     */
    public function getIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        if ($this->parent_id || !$this->id) {
            $shape = $this->is_empty
                   ? 'folder-empty'
                   : 'folder-full';
        } else {
            $shape = $this->is_empty
                   ? 'folder-home-empty'
                   : 'folder-home-full';
        }

        return Icon::create($shape, $role);
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->folderdata->getId();
    }

    /**
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        return $this->folderdata[$attribute];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        return $this->folderdata[$name] = $value;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isVisible(string $user_id): bool
    {
        $visible = $this->isVisibleNonRecursive($user_id);

        if ($visible && $parent_folder = $this->getParent()) {
            return $parent_folder->isVisible($user_id);
        }

        return $visible;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    protected function isVisibleNonRecursive($user_id)
    {
        $visible = false;

        if ($this->range_type === 'user') {
            $visible = $this->range_id === $user_id;
        }

        if ($this->range_type === 'institute' ) {
            $visible = (Config::get()->ENABLE_FREE_ACCESS
                     && (Config::get()->ENABLE_FREE_ACCESS != 'courses_only')) ||
                       Seminar_Perm::get()->have_perm('user', $user_id);
        }

        if ($this->range_type === 'course') {
            $range = $this->getRangeObject();
            $visible = Config::get()->ENABLE_FREE_ACCESS && isset($range) && $range->lesezugriff == 0
                    || Seminar_Perm::get()->have_studip_perm('user', $this->range_id, $user_id);
        }
        return $visible;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isReadable(string $user_id): bool
    {
        $readable = $this->isVisibleNonRecursive($user_id);
        if ($readable && $parent_folder = $this->getParent()) {
            return $parent_folder->isReadable($user_id);
        }

        return $readable;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isWritable(string $user_id): bool
    {
        return ($this->range_type === 'user' && $this->range_id === $user_id)
            || Seminar_Perm::get()->have_studip_perm('autor', $this->range_id, $user_id);
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isEditable(string $user_id): bool
    {
        return ($this->range_type === 'user' && $this->range_id === $user_id)
            || Seminar_Perm::get()->have_studip_perm('tutor', $this->range_id, $user_id);
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isSubfolderAllowed(string $user_id): bool
    {
        return ($this->range_type === 'user' && $this->range_id === $user_id)
            || Seminar_Perm::get()->have_studip_perm('tutor', $this->range_id, $user_id);
    }

    /**
     * @return \Flexi\Template|string|null
     */
    public function getDescriptionTemplate(): \Flexi\Template|string|null
    {
        return formatReady($this->folderdata['description']);
    }

    /**
     * @return \Flexi\Template|null
     */
    public function getEditTemplate(): ?\Flexi\Template
    {
        return null;
    }

    /**
     * @param array|ArrayAccess $folderdata
     * @return FolderType|MessageBox
     */
    public function setDataFromEditTemplate(array|ArrayAccess $folderdata): FolderType|MessageBox
    {
        if (!$folderdata['name']) {
            return MessageBox::error(_('Die Bezeichnung des Ordners fehlt.'));
        }
        $this->folderdata['name']        = $folderdata['name'];
        $this->folderdata['description'] = $folderdata['description'] ?: '';
        return $this;
    }

    /**
     * @return bool
     */
    public function store(): bool
    {
        return $this->folderdata->store();
    }

    /**
     * @param string $user_id
     * @return string|null
     */
    public function validateUpload(FileType $file, string $user_id): ?string
    {
        $upload_type = FileManager::getUploadTypeConfig($this->range_id, $user_id);
        return $this->getValidationMessages($upload_type, $file);
    }

    protected function getValidationMessages($upload_type, $newfile)
    {
        if ($upload_type['file_size'] < $newfile->getSize()) {
            return sprintf(
                _('Die maximale Größe für einen Upload (%s) wurde überschritten.'),
                relsize($upload_type['file_size'])
            );
        }

        $ext   = strtolower(pathinfo($newfile->getFilename(), PATHINFO_EXTENSION));
        $types = array_map('strtolower', $upload_type['file_types']);

        if (!in_array($ext, $types) && $upload_type['type'] === 'deny') {
            return sprintf(
                _('Sie dürfen nur die Dateitypen %s hochladen!'),
                join(',', $upload_type['file_types'])
            );
        }

        if (in_array($ext, $types) && $upload_type['type'] === 'allow') {
            return sprintf(_('Sie dürfen den Dateityp %s nicht hochladen!'), $ext);
        }

        return null;
    }

    /**
     * @return FolderType[]
     */
    public function getSubfolders(): array
    {
        // We must load the subfolders from the database instead
        // of using $this->folderdata->subfolders, because subfolders
        // that have been added to this folder aren't included in
        // $this->folderdata->subfolders although they are in the database.
        $subfolders = [];
        $database_subfolders = Folder::findByParent_id($this->getId(), "ORDER BY name");
        foreach ($database_subfolders as $subfolder) {
            //check FolderType of subfolder
            $subfolders[] = $subfolder->getTypedFolder();
        }
        return $subfolders;
    }

    /**
     * @return FileType[]
     */
    public function getFiles(): array
    {
        $filerefs = FileRef::findByFolder_id($this->getId(), "ORDER BY name");
        $files = [];
        foreach ($filerefs as $fileref) {
            $class = $fileref->file['filetype'];
            if (class_exists($class) && is_a($class, "StandardFile", true)) {
                $files[] = new $class($fileref);
            } else {
                $files[] = new UnknownFileType($fileref);
            }
        }
        NotificationCenter::postNotification("StandardFolderCollectsFiles", $this, $user_data = null);
        return $files;
    }

    /**
     * Returns the parent-folder as a StandardFolder
     *
     * @return FolderType|null
     */
    public function getParent(): ?FolderType
    {
        return $this->folderdata->parentfolder
             ? $this->folderdata->parentfolder->getTypedFolder()
             : null;
    }

    public function addFile(FileType $file, ?string $user_id = null): ?FileType
    {
        $file = $file->convertToStandardFile();

        $filename = $original_filename = $file->getFilename();
        $ext = pathinfo($original_filename, PATHINFO_EXTENSION);
        if ($ext) {
            $name = substr($original_filename, 0, -mb_strlen('.' . $ext));
        } else {
            $name = $original_filename;
        }
        $files = $this->getFiles();
        $c = 1;

        //rename file if name already exists in folder:
        do {
            $name_available = true;
            foreach ($files as $folderfile) {
                if ($folderfile->getFilename() === $filename) {
                    $name_available = false;
                    $filename = $name . '[' . ++$c . ']';
                    if ($ext) {
                        $filename .= '.' . $ext;
                    }
                    break;
                }
            }
        } while (!$name_available);
        return $file->addToFolder($this, $filename, $user_id);
    }

    /**
     * @param string $file_ref_id
     * @return bool|array
     */
    public function deleteFile(string $file_ref_id): bool|array
    {
        $file_ref = $this->folderdata->file_refs->find($file_ref_id);

        if (!$this->isFileWritable($file_ref->id, $GLOBALS['user']->id)) {
            return [sprintf(
                _('Ungenügende Berechtigungen zum Löschen der Datei %s in Ordner %s!'),
                $file_ref->name,
                $this->name
            )];
        }

        if ($file_ref) {
            return $file_ref->delete();
        }

        return 0;
    }

    /**
     * @param FolderType $foldertype
     * @return FolderType|null
     */
    public function createSubfolder(FolderType $foldertype): ?FolderType
    {
        if (!$foldertype->user_id) {
            $foldertype->user_id = $GLOBALS['user']->id;
        }
        $type = get_class($foldertype);
        if (!$type::availableInRange($this->folderdata['range_id'], $foldertype->user_id)) {
            $foldertype = new StandardFolder($foldertype);
        }
        $foldertype->range_id   = $this->folderdata['range_id'];
        $foldertype->range_type = $this->folderdata['range_type'];
        $foldertype->parent_id  = $this->folderdata['id'];
        if ($foldertype->store()) {
            return $foldertype;
        }

        #In case something went wrong while storing the subfolder:
        return null;
    }

    /**
     * @param string $subfolder_id
     * @return bool
     */
    public function deleteSubfolder(string $subfolder_id): bool
    {
        $subfolders = $this->folderdata->subfolders;

        if ($subfolders) {
            foreach ($subfolders as $subfolder) {
                if ($subfolder->id === $subfolder_id) {
                    //we found the subfolder that shall be deleted
                    return $subfolder->delete();
                }
            }
        }

        //if no subfolders are present or the subfolder can't be found
        //we return false:
        return false;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->folderdata->delete();
    }

    /**
     * @param string $file_ref_id
     * @param string $user_id
     * @return bool
     */
    public function isFileDownloadable(string $file_ref_id, string $user_id): bool
    {
        if ($this->range_type === 'user') {
            return $user_id === $this->range_id;
        }

        if (in_array($this->range_type, ['course', 'institute'])) {
            $fileref = FileRef::find($file_ref_id);

            if (is_object($fileref->terms_of_use)) {
                //terms of use are defined for this file!
                return $this->isReadable($user_id)
                    && $fileref->terms_of_use->isDownloadable($this->range_id, $this->range_type,true, $user_id);
            } else {
                return $this->isReadable($user_id)
                    && $GLOBALS['perm']->have_studip_perm('user', $this->range_id, $user_id);
            }
        }

        return false;
    }

    /**
     * @param string $file_ref_id
     * @param string $user_id
     * @return bool
     */
    public function isFileEditable(string $file_ref_id, string $user_id): bool
    {
        if ($this->range_type === 'user') {
            return $user_id === $this->range_id;
        }

        $fileref = FileRef::find($file_ref_id);

        return $fileref->user_id === $user_id
            || $GLOBALS['perm']->have_studip_perm('tutor', $this->range_id, $user_id);
    }

    /**
     * Checks if a user has write permissions to a file.
     *
     * For standard folders write permissions are granted
     * if the user is the owner of the file or if the user has at least
     * tutor permissions on the Stud.IP object specified by range_id
     * (such objects may be courses or institutes for example).
     *
     * @param string $file_ref_id
     * @param string $user_id
     * @return bool
     */
    public function isFileWritable(string $file_ref_id, string $user_id): bool
    {
        return $this->isFileEditable($file_ref_id, $user_id);
    }

    /**
     * returns the object for the range_id of the folder
     *
     * @return Course|Institute|User
     */
    public function getRangeObject()
    {
        $range = null;
        if ($this->range_type && $this->range_id) {
            if (Context::getId() === $this->range_id) {
                $range = Context::get();
            } else {
                $range = call_user_func([ucfirst($this->range_type), 'find'], $this->range_id);
            }
        }
        return $range;
    }

    /**
     * Returns an associative array of additional colums with the index the id of the column
     * and their values as the localized names of the columns
     *
     * @return array('col1' => _("Anfragestatus"))
     */
    public function getAdditionalColumns(): array
    {
        return [];
    }

    /**
     * Returns the content for that additional column, if it exists. You can return null a string
     * or a Flexi\Template as the content.
     *
     * @param string $column_index
     *
     * @return \Flexi\Template|string|null
     */
    public function getContentForAdditionalColumn(string $column_index): \Flexi\Template|string|null
    {
        return null;
    }

    /**
     * Returns an integer or text that marks the value the content of the given column should be
     * ordered by.
     *
     * @param string $column_index
     * @return int : order value
     */
    public function getAdditionalColumnOrderWeigh(string $column_index): int
    {
        return 1;
    }

    /**
     * Returns an array of Studip\Button or Studip\LinkButton objects that get displayed
     * underneath the files-table.
     *
     * @return array of Studip\Button or Studip\LinkButton
     */
    public function getAdditionalActionButtons(): array
    {
        return [];
    }

    /**
     * @see FolderType::copySettings()
     */
    public function copySettings(): array
    {
        return ['description' => $this->description];
    }

    public function countDownloads(?FileRef $ref = null): bool
    {
        if (!$ref) {
            return true;
        }

        $user = User::findCurrent();
        return !$user || $ref->user_id !== $user->id;
    }
}

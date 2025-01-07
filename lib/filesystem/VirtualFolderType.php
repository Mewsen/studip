<?php
/**
 * VirtualFolderType.php
 *
 * This is a FolderType implementation for folders that dont exist in
 * the database table folders, e.g. folders from plugins
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author    Rasmus Fuhse <fuhse@data-quest.de>
 * @copyright 2016 Stud.IP Core-Group
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category  Stud.IP
 */
class VirtualFolderType implements FolderType
{
    /**
     * @var array
     */
    protected $folderdata;
    /**
     * @var string
     */
    protected $plugin_id;

    /**
     * @var array
     */
    protected $files      = [];
    /**
     * @var array
     */
    protected $subfolders = [];

    /**
     * VirtualFolderType constructor.
     * @param array $folderdata
     * @param null $plugin_id
     */
    public function __construct($folderdata = [], $plugin_id = null)
    {
        $this->folderdata = $folderdata;
        //Make sure the description field is not empty so that folders
        //of this type are compatible with StandardFolder types:
        if (empty($this->folderdata['description'])) {
            $this->folderdata['description'] = '';
        }
        $this->plugin_id  = $plugin_id;
    }

    public static function getTypeName(): string
    {
        return _('Virtueller Ordner');
    }

    /**
     * @param SimpleORMap|string $range_id_or_object
     * @param string             $user_id
     * @return bool
     */
    public static function availableInRange(SimpleORMap|string $range_id_or_object, string $user_id): bool
    {
        return false;
    }

    /**
     * @param string $role
     * @return Icon
     */
    public function getIcon(string $role = 'info'): Icon
    {
        return Icon::create('folder-empty', $role);
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->folderdata['id'];
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        return $this->folderdata[$attribute] ?? null;
    }

    /**
     * @param $attribute
     * @param $value
     */
    public function __set($attribute, $value)
    {
        $this->folderdata[$attribute] = $value;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isVisible(string $user_id): bool
    {
        return true;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isReadable(string $user_id): bool
    {
        return true;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isWritable(string $user_id): bool
    {
        return false;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isEditable(string $user_id): bool
    {
        return false;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isSubfolderAllowed(string $user_id): bool
    {
        return false;
    }

    /**
     * @return \Flexi\Template|string|null
     */
    public function getDescriptionTemplate(): \Flexi\Template|string|null
    {
        return null;
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
     */
    public function setDataFromEditTemplate(array|ArrayAccess $folderdata): FolderType|MessageBox
    {
        return MessageBox::error('Not applicable for virtual folder type');
    }

    /**
     * @param $uploadedfile
     * @param string $user_id
     * @return string|null
     */
    public function validateUpload(FileType $file, string $user_id): ?string
    {
        return false;
    }

    /**
     * @return array
     */
    public function getSubfolders(): array
    {
        return $this->subfolders;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return FolderType|null
     */
    public function getParent(): ?FolderType
    {
        if (!$this->folderdata['parent_id']) {
            return null;
        }

        if ($this->plugin_id) {
            return PluginManager::getInstance()->getPluginById($this->plugin_id)->getFolder($this->folderdata->parent_id);
        }

        return $this->folderdata->parent_id
             ? $this->folderdata->parentfolder->getTypedFolder()
             : null;
    }

    /**
     * @param array|ArrayAccess $file
     * @return FileType|null
     */
    public function addFile(FileType $file, ?string $user_id = null): ?FileType
    {
        $this->files[] = $file;
        return end($this->files);
    }

    /**
     * @param string $file_ref_id
     * @return bool|array
     */
    public function deleteFile(string $file_ref_id): bool|array
    {
        return true;
    }

    /**
     * @param FolderType $foldertype
     * @return FolderType|null
     */
    public function createSubfolder(FolderType $foldertype): ?FolderType
    {
        $this->subfolders[] = $foldertype;
        return $foldertype;
    }

    /**
     * @param string $subfolder_id
     * @return bool
     */
    public function deleteSubfolder(string $subfolder_id): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return true;
    }

    public function store(): bool
    {
        return 0;
    }

    /**
     * @param string $file_ref_id
     * @param string $user_id
     * @return bool
     */
    public function isFileDownloadable(string $file_ref_id, string $user_id): bool
    {
        return true;
    }

    /**
     * @param string $file_ref_id
     * @param string $user_id
     * @return bool
     */
    public function isFileEditable(string $file_ref_id, string $user_id): bool
    {
        return false;
    }

    /**
     * @param string $file_ref_id
     * @param string $user_id
     * @return bool
     */
    public function isFileWritable(string $file_ref_id, string $user_id): bool
    {
        return false;
    }

    public function getAdditionalColumns(): array
    {
        return [];
    }

    public function getContentForAdditionalColumn(string $column_index): \Flexi\Template|string|null
    {
        return null;
    }

    public function getAdditionalColumnOrderWeigh(string $column_index): int
    {
        return 0;
    }

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


    public function countDownload(FileRef $ref): bool
    {
        return true;
    }

    public function displayDownloads(): bool
    {
        return true;
    }
}

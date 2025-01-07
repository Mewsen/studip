<?php
/**
 * UnknownFolderType.php
 *
 * this folder type implementation is used when a folder type entry in
 * the database is no longer available in the main system
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      André Noack <noack@data-quest.de>
 * @copyright   2016 Stud.IP Core-Group
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
class UnknownFolderType implements FolderType
{
    /**
     * @var Folder
     */
    protected $folderdata;

    /**
     * StandardFolder constructor.
     * @param Folder|null $folderdata
     */
    public function __construct($folderdata)
    {
        if ($folderdata instanceof Folder) {
            $this->folderdata = $folderdata;
        } else {
            $this->folderdata = new Folder();
        }
    }

    /**
     * @return string
     */
    public static function getTypeName(): string
    {
        return _('Unbekannter Ordner Typ');
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
     * @return Icon
     */
    public function getIcon(string $role = 'info'): Icon
    {
        return Icon::create('folder-broken', $role);
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->folderdata->getId();
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        if ($attribute === 'name') {
            return $this->folderdata['name'] . sprintf(
                _(' (unbekannter Typ: %s)'),
                $this->folderdata['folder_type']
            );
        }

        return $this->folderdata[$attribute];
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
        return false;
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
     *
     */
    public function getDescriptionTemplate(): \Flexi\Template|string|null
    {
        return '';
    }


    /**
     *
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
        return MessageBox::error('Not applicable for unknown folder type');
    }

    /**
     * @param $uploadedfile
     * @param string $user_id
     */
    public function validateUpload(FileType $file, string $user_id): ?string
    {
        return null;
    }

    public function addFile(FileType $file, ?string $user_id = null): ?FileType
    {
        return null;
    }

    /**
     * @return array
     */
    public function getSubfolders(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return [];
    }

    /**
     * @return FolderType|null
     */
    public function getParent(): ?FolderType
    {
        return $this->folderdata->parentfolder
             ? $this->folderdata->parentfolder->getTypedFolder()
             : null;
    }

    /**
     * @param string $file_ref_id
     * @return bool
     */
    public function deleteFile(string $file_ref_id): bool
    {
        return false;
    }

    /**
     * @param FolderType $foldertype
     */
    public function createSubfolder(FolderType $foldertype): ?FolderType
    {
        return null;
    }

    /**
     * @param string $subfolder_id
     * @return bool
     */
    public function deleteSubfolder(string $subfolder_id): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function store(): bool
    {
        return false;
    }

    /**
     * @param string $file_ref_id
     * @param string $user_id
     * @return bool
     */
    public function isFileDownloadable(string $file_ref_id, string $user_id): bool
    {
        return false;
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

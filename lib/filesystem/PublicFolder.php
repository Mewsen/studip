<?php
/**
 * PublicFolder.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author    André Noack <noack@data-quest.de>
 * @copyright 2017 Stud.IP Core-Group
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category  Stud.IP
 */
class PublicFolder extends StandardFolder
{

    public static $sorter = 1;

    /**
     * Returns a localised name of the PublicFolder type.
     *
     * @return string The localised name of this folder type.
     */
    static public function getTypeName(): string
    {
        return _('Ein Ordner für öffentlich zugängliche Daten');
    }

    /**
     * @param SimpleORMap|string $range_id_or_object
     * @param string             $user_id
     * @return bool
     */
    public static function availableInRange(SimpleORMap|string $range_id_or_object, string $user_id): bool
    {
        $range_id = is_object($range_id_or_object) ? $range_id_or_object->id : $range_id_or_object;
        return $range_id === $user_id;
    }


    /**
     * @param string $role
     * @return Icon
     */
    public function getIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        $shape = $this->is_empty
               ? 'folder-public-empty'
               : 'folder-public-full';

        return Icon::create($shape, $role);
    }


    /**
     * @param $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        if ($attribute === 'viewable') {
            return !empty($this->folderdata['data_content']['viewable']);
        }
        return $this->folderdata[$attribute];
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        if ($name === 'viewable') {
            return $this->folderdata['data_content']['viewable'] = $value;
        }
        return $this->folderdata[$name] = $value;
    }

    /**
     * PublicFolders are visible for the owner, or for all if viewable flag is set
     *
     * @param string $user_id The user who wishes to see the folder.
     *
     * @return bool True
     */
    public function isVisible(string $user_id): bool
    {
        return $this->viewable || $this->range_id === $user_id;
    }

    /**
     * PublicFolders are readable for the owner, or for all if viewable flag is set
     *
     * @param string $user_id The user who wishes to read the folder.
     *
     * @return bool True
     */
    public function isReadable(string $user_id): bool
    {
        return $this->isVisible($user_id);
    }

    /**
     * Returns a description template for PublicFolders.
     *
     * @return \Flexi\Template|string|null A string describing this folder type.
     */
    public function getDescriptionTemplate(): \Flexi\Template|string|null
    {
        return $this->viewable
             ? _('Dateien aus diesem Ordner werden auf Ihrer Profilseite zum Download angeboten.')
             : _('Dateien aus diesem Ordner sind für alle Stud.IP Nutzer zugreifbar.');

    }

    /**
     * Files in PublicFolders are always downloadable.
     *
     * @param FileRef $file_ref The ID to a FileRef.
     * @param string  $user_id  The user who wishes to downlaod the file.
     *
     * @return bool True
     */
    public function isFileDownloadable(FileRef $file_ref, string $user_id): bool
    {
        //public folder => everyone can download a file
        return true;
    }

    /**
     * Files in PublicFolders are editable for the owner only.
     *
     * @param FileRef $file_ref The ID to a FileRef.
     * @param string  $user_id  The user who wishes to edit the file.
     *
     * @return bool True, if the user is the owner of the file, false otherwise.
     */
    public function isFileEditable(FileRef $file_ref, string $user_id): bool
    {
        //only the owner may edit files
        return $this->range_id === $user_id;
    }

    /**
     * Files in PublicFolders are writable for the owner only.
     *
     * @param FileRef $file_ref The ID to a FileRef.
     * @param string  $user_id  The user who wishes to write to the file.
     *
     * @return bool True, if the user is the owner of the file, false otherwise.
     */
    public function isFileWritable(FileRef $file_ref, string $user_id): bool
    {
        //only the owner may delete files
        return $this->range_id === $user_id;
    }

    /**
     * Returns the edit template for this folder type.
     *
     * @return \Flexi\Template|null
     */
    public function getEditTemplate(): ?\Flexi\Template
    {
        $template = $GLOBALS['template_factory']->open('filesystem/public_folder/edit.php');
        $template->public_folder_viewable = $this->viewable;
        return $template;
    }

    /**
     * Sets the data from a submitted edit template.
     *
     * @param array|ArrayAccess $folderdata The data from the edit template.
     *
     * @return FolderType|MessageBox A "reference" to this PublicFolder.
     */
    public function setDataFromEditTemplate(array|ArrayAccess $folderdata): FolderType|MessageBox
    {
        $this->viewable = (int) $folderdata['public_folder_viewable'];
        return parent::setDataFromEditTemplate($folderdata);
    }
}

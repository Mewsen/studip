<?php
/**
 * CoursePublicFolder.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author    Elmar Ludwig <elmar.ludwig@uos.de>
 * @copyright 2017 Stud.IP Core-Group
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category  Stud.IP
 */
class CoursePublicFolder extends StandardFolder
{
    public static $sorter = 7;


    /**
     * Returns a localised name of the CoursePublicFolder type.
     *
     * @return string The localised name of this folder type.
     */
    public static function getTypeName(): string
    {
        return _('Ordner für öffentlich zugängliche Dateien');
    }

    /**
     * @param SimpleORMap|string $range_id_or_object
     * @param string             $user_id
     * @return bool
     */
    public static function availableInRange(SimpleORMap|string $range_id_or_object, string $user_id): bool
    {
        $course = Course::toObject($range_id_or_object);
        if ($course && !$course->isNew()) {
            return Seminar_Perm::get()->have_studip_perm('tutor', $course->id, $user_id);
        }
        return false;
    }

    public function getIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        $shape = $this->is_empty
               ? 'folder-public-empty'
               : 'folder-public-full';

        return Icon::create($shape, $role);
    }


    /**
     * Determines, if the folder is accessible worldwide.
     *
     * @return bool True, if the folder is accessible worldwide,
     *     false otherwise.
     */
    public function hasWorldwideAccess()
    {
        return !empty($this->folderdata['data_content']['worldwide_access']);
    }


    /**
     * CoursePublicFolders are visible for all logged in users.
     * If ENABLE_FREE_ACCESS is set to 'courses_only' or '1', the folder is
     * visible for everyone. In case, the worldwide access option for the
     * folder is set, the folder is visible worldwide.
     *
     * @param string $user_id The user who wishes to see the folder.
     *
     * @return bool True
     */
    public function isVisible(string $user_id): bool
    {
        if ($user_id === null || $user_id === 'nobody') {
            if ($this->hasWorldwideAccess()) {
                return true;
            }
            $range = $this->getRangeObject();
            return Config::get()->ENABLE_FREE_ACCESS && isset($range) && $range->lesezugriff == 0;
        }

        return true;
    }

    /**
     * CoursePublicFolders are readable for all logged in users.
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
     * Returns a description template for CoursePublicFolders.
     *
     * @return \Flexi\Template|string|null A string describing this folder type.
     */
    public function getDescriptionTemplate(): \Flexi\Template|string|null
    {
        return _('Dateien aus diesem Ordner werden auf der Detailseite der Veranstaltung zum Download angeboten.');
    }


    /**
     * Returns the edit template for this folder type.
     *
     * @return \Flexi\Template|null
     */
    public function getEditTemplate(): ?\Flexi\Template
    {
        $template = $GLOBALS['template_factory']->open('filesystem/course_public_folder/edit.php');
        $template->worldwide_access = $this->hasWorldwideAccess();
        return $template;
    }

    /**
     * Sets the data from a submitted edit template.
     *
     * @param array|ArrayAccess $folderdata The data from the edit template.
     *
     * @return FolderType|MessageBox
     */
    public function setDataFromEditTemplate(array|ArrayAccess $folderdata): FolderType|MessageBox
    {
        $this->folderdata['data_content']['worldwide_access'] = $folderdata['course_public_folder_worldwide_access'];
        return parent::setDataFromEditTemplate($folderdata);
    }


    /**
     * Files in CoursePublicFolders are downloadable for all logged in users.
     *
     * @param FileRef $file_ref The ID to a FileRef.
     * @param string  $user_id  The user who wishes to downlaod the file.
     *
     * @return bool
     */
    public function isFileDownloadable(FileRef $file_ref, string $user_id): bool
    {
        return $this->isVisible($user_id);
    }
}

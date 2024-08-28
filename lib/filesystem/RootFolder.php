<?php
/**
 * RootFolder.php
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
class RootFolder extends StandardFolder
{

    /**
     * @return string
     */
    public static function getTypeName()
    {
        return _('Hauptordner');
    }

    public static function availableInRange($range_id_or_object, $user_id)
    {
        return false;
    }

    /**
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        if ($attribute === 'name') {
            $range = $this->getRangeObject();
            return isset($range) ? $range->getFullName('short') : '';
        }
        return $this->folderdata[$attribute];
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isWritable($user_id)
    {
        if (
            ($this->range_type === 'user' && $this->range_id === $user_id)
            || $this->isEditable($user_id)
        ) {
            return true;
        }

        if (!Seminar_Perm::get()->have_studip_perm('autor', $this->range_id, $user_id)) {
            return false;
        }

        //The user has autor permissions. This is a special case since the upload to the root folder
        //may be denied globally and allowed locally, or it may be allowed globally and denied locally.
        //Also, this only affects courses, not study groups or root folders in other range types (institutes etc.).
        if ($this->range_type !== 'course') {
            //Upload allowed.
            return true;
        }

        //The root folder belongs to a course object.
        $course = Course::find($this->range_id);
        $locked_status = null;
        if (isset($this->folderdata['data_content']['locked'])) {
            $locked_status = $this->folderdata['data_content']['locked'] === 1;
        }
        if ($course->isStudygroup()) {
            //Study groups are not affected by the global PREVENT_ROOT_FOLDER_UPLOADS_BY_STUDENTS_IN_COURSES config.
            return !$locked_status;
        }
        //At this point, only the settings for real courses are left to be checked:
        if ($locked_status !== null) {
            //The locked status for the folder is set. Uploading to the folder is allowed
            //when the locked status is not '1'.
            return !$locked_status;
        }

        // The locked status for the folder is not set. Therefore, the global configuration
        // is relevant for checking if upload is allowed:
        return !Config::get()->PREVENT_ROOT_FOLDER_UPLOADS_BY_STUDENTS_IN_COURSES;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isEditable($user_id)
    {
        return Seminar_Perm::get()->have_studip_perm('tutor', $this->range_id, $user_id);
    }

    /**
     * Returns the parent-folder as a StandardFolder
     * @return FolderType
     */
    public function getParent()
    {
        return null;
    }

    /**
     * @return bool|number
     */
    public function store()
    {
        $this->folderdata['parent_id'] = '';
        return $this->folderdata->store();
    }

    /**
     * @return Flexi\Template
     */
    public function getEditTemplate()
    {
        $template = $GLOBALS['template_factory']->open('filesystem/root_folder/edit');
        $template->folder = $this;
        return $template;
    }

    /**
     * @param array $request
     * @return FolderType|MessageBox
     */
    public function setDataFromEditTemplate($request)
    {
        $locked_status = null;
        if (isset($request['locked'])) {
            //The locked status is defined in one way or another.
            $locked_status = $request['locked'] ? 1 : 0;
        }
        $this->folderdata['data_content'] = [
            'locked' => $locked_status
        ];
        return $this;
    }
}

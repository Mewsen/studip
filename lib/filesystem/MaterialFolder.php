<?php
/**
 * HiddenFolder.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author    Dominik Feldschnieders <dofeldsc@uos.de>
 * @copyright 2016 Stud.IP Core-Group
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category  Stud.IP
 */
class MaterialFolder extends PermissionEnabledFolder
{

    public static $sorter = 3;

    public static function availableInRange(SimpleORMap|string $range_id_or_object, string $user_id): bool
    {
        $range_id = is_object($range_id_or_object) ? $range_id_or_object->id : $range_id_or_object;
        return Seminar_Perm::get()->have_studip_perm('tutor', $range_id, $user_id);
    }

    /**
     * MaterialFolder constructor.
     */
    public function __construct($folderdata = null)
    {
        parent::__construct($folderdata);

        $this->permission = 5;
    }

    /**
     * This function returns the suitable Icon for this folder type (GroupFolder)
     *
     * @return Icon The icon object for this folder type
     */
    public function getIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        return Icon::create('download', $role);
    }

    /**
     * Returns the name of the MaterialFolder type.
     *
     * @return string the name of the MaterialFolder type
     */
    static public function getTypeName(): string
    {
        return _('Materialordner zum Anbieten von Inhalten zum Download');
    }

    /**
     * Returns the description template for a instance of a MaterialFolder type
     *
     * @return \Flexi\Template|string|null A description template for a instance of the type MaterialFolder
     */
    public function getDescriptionTemplate(): \Flexi\Template|string|null
    {
        $template = $GLOBALS['template_factory']->open('filesystem/material_folder/description.php');

        $template->type       = self::getTypeName();
        $template->folder     = $this;
        $template->folderdata = $this->folderdata;

        return $template;
    }
}

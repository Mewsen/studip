<?php
/**
 * PermissionEnabledFolder.php
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
class PermissionEnabledFolder extends StandardFolder
{
    protected $permission = 7;
    protected $perms = ['x' => 1, 'w' => 2, 'r' => 4, 'f' => 8];
    protected $must_have_perm;

    public static function availableInRange(SimpleORMap|string $range_id_or_object, string $user_id): bool
    {
        return false;
    }

    public static function getTypeName(): string
    {
        return _('Ordner mit Zugangsbeschränkung');
    }

    public function __construct($folderdata = null)
    {
        parent::__construct($folderdata);

        if (isset($this->folderdata['data_content']['permission'])) {
            $this->permission = $this->folderdata['data_content']['permission'];
        }

        $this->must_have_perm = $this->range_type === 'course' ? 'tutor' : 'autor';
    }

    public function getPermissionString()
    {
        $perms = $this->perms;
        array_pop($perms);

        $r = array_flip($perms);
        foreach($perms as $v => $p) {
            if (!($this->permission & $p)) {
                $r[$p] = '-';
            }
        }
        return implode(array_reverse($r));
    }

    public function checkPermission($perm, $user_id = null)
    {
        if ($user_id && is_object($GLOBALS['perm']) && $GLOBALS['perm']->have_studip_perm($this->must_have_perm, $this->range_id, $user_id)) {
            return true;
        }

        return (bool)($this->permission & $this->perms[$perm]);
    }

    public function getIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        $shape = $this->is_empty
               ? 'folder-lock-empty'
               : 'folder-lock-full';
        return Icon::create($shape, $role);
    }

    public function isVisible(string $user_id = null): bool
    {
        return $this->checkPermission('x', $user_id)
            && parent::isVisible($user_id);
    }

    public function isReadable(string $user_id = null): bool
    {
        return $this->checkPermission('r', $user_id)
            && parent::isReadable($user_id);
    }

    public function isWritable(string $user_id = null): bool
    {
        return $this->checkPermission('w', $user_id)
            && parent::isWritable($user_id);
    }

    public function isSubfolderAllowed(string $user_id): bool
    {
        return $this->checkPermission('f', $user_id);
    }

    public function getDescriptionTemplate(): \Flexi\Template|string|null
    {
        $template = $GLOBALS['template_factory']->open('filesystem/permission_enabled_folder/description.php');

        $template->type   = self::getTypeName();
        $template->folder = $this;
        $template->folderdata = $this->folderdata;
        return $template;
    }

    public function validateUpload(FileType $file, string $user_id): ?string
    {
        if (!$this->isWritable($user_id)) {
            return _('Der Dateiordner ist nicht beschreibbar.');
        }

        return parent::validateUpload($file, $user_id);
    }

    /**
     * @return FileType[]
     */
    public function getFiles(): array
    {
        return array_filter(parent::getFiles(), function($file) {
            return $this->isFileVisible($file->getFileRef(), $GLOBALS['user']->id);
        });
    }

    /**
     * Determines if a user may see the file.
     * @param FileRef|string $fileref_or_id
     * @param string $user_id
     * @return bool
     */
    public function isFileVisible($fileref_or_id, $user_id)
    {
        return $this->isReadable($user_id);
    }

    /**
     * @param string $file_ref_id
     * @param string $user_id
     * @return bool
     */
    public function isFileDownloadable(string $file_ref_id, string $user_id): bool
    {
        $fileref = FileRef::find($file_ref_id);

        if ($fileref) {
            if ($this->isVisible($user_id) && $this->isFileVisible($fileref, $user_id)) {
                return $fileref->terms_of_use->isDownloadable($this->range_id, $this->range_type, true, $user_id);
            }
        }

        return false;
    }

    /**
     * @see FolderType::copySettings()
     */
    public function copySettings(): array
    {
        return [
            'description' => $this->description,
            'data_content' => $this->data_content
        ];
    }

    public function countDownloads(?FileRef $ref = null): bool
    {
        return ($this->permission & $this->perms['r'])
            && parent::countDownloads($ref);
    }
}

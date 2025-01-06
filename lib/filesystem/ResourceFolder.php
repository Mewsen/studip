<?php


/**
 * This is a folder type for files associated with Resource objects.
 * Its purpose is to allow users to link or add relevant files
 * to a Resource.
 * An an example, one could attach a fire escape plan to a Building resource.
 */
class ResourceFolder extends StandardFolder
{
    public static function getTypeName(): string
    {
        return _('Ressourcen-Dateiordner');
    }

    public static function availableInRange(SimpleORMap|string $range_id_or_object, string $user_id): bool
    {
        //A resource folder is not available for course, user, institute
        //or message objects. Only when a range_id of a resource is given
        //or a resource object we may return true.

        if ($range_id_or_object instanceof Resource) {
            //$range_id_or_object contains a Resource object.
            return true;
        } else {
            //$range_id_or_object contains an ID.
            //If we find a resource object for that ID
            //we can return true, otherwise we return false.
            return Resource::exists($range_id_or_object);
        }
    }

    public function getIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        return Icon::create('resource', $role);
    }

    public function isVisible(string $user_id): bool
    {
        if ($user_id == 'nobody') {
            return false;
        }
        //Get the resource object:
        $resource = Resource::find($this->range_id);
        $user = User::find($user_id);

        if (($resource instanceof Resource) && ($user instanceof User)) {
            return true;
        } elseif ($user instanceof User) {
            //Check global permissions:
            return ResourceManager::userHasGlobalPermission(
                $user,
                'admin'
            );
        }
        return false;
    }

    public function isReadable(string $user_id): bool
    {
        if ($user_id == 'nobody') {
            return false;
        }
        //Get the resource object:
        $resource = Resource::find($this->range_id);
        $user = User::find($user_id);

        if (($resource instanceof Resource) && ($user instanceof User)) {
            return true;
        } elseif ($user instanceof User) {
            //Check global permissions:
            return ResourceManager::userHasGlobalPermission(
                $user,
                'admin'
            );
        }
        return false;
    }

    public function isWritable(string $user_id): bool
    {
        $user = User::find($user_id);

        //Check global permissions: The user has to be
        //a global resource admin or a root user.
        return ResourceManager::userHasGlobalPermission(
            $user,
            'admin'
        );
    }

    public function isEditable(string $user_id): bool
    {
        //Thou shalt not edit ResourceFolder folder types!
        return false;
    }

    public function isSubfolderAllowed(string $user_id): bool
    {
        //Furthermore, thou shalt not create subfolders in a Resource folder!
        return false;
    }

    public function getSubfolder()
    {
        //No subfolders allowed, resulting in:
        return [];
    }

    public function setDataFromEditTemplate(array|ArrayAccess $folderdata): FolderType|MessageBox
    {
        return MessageBox::error(
            _('Ressourcenordner dürfen nicht geändert werden!')
        );
    }

    public function createSubfolder(FolderType $foldertype): ?FolderType
    {
        //No subfolders allowed, resulting in:
        return null;
    }

    public function deleteSubfolder(string $subfolder_id): bool
    {
        //No subfolders allowed, resulting in:
        return false;
    }

    public function isFileDownloadable(string $file_ref_id, string $user_id): bool
    {
        return $this->isReadable($user_id);
    }

    public function isFileEditable(string $file_ref_id, string $user_id): bool
    {
        return $this->isWritable($user_id);
    }

    public function isFileWritable(string $file_ref_id, string $user_id): bool
    {
        return $this->isWritable($user_id);
    }
}

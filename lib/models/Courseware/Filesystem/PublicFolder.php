<?php

namespace Courseware\Filesystem;

use ArrayAccess;
use Courseware\Instance;
use File;
use FileRef;
use FileType;
use Flexi\Template;
use Folder;
use FolderType;
use Icon;
use MessageBox;
use Request;
use SimpleORMap;
use StandardFolder;

class PublicFolder extends StandardFolder
{
    public static function findOrCreateTopFolder(Instance $instance): PublicFolder
    {
        if (!($folder = self::findTopFolder($instance))) {
            $folder = self::createTopFolder($instance);
        }

        return $folder;
    }

    public static function findTopFolder(Instance $instance): ?PublicFolder
    {
        if ($folder = Folder::findOneByrange_id($instance->getRoot()->id)) {
            return new PublicFolder($folder);
        }

        return null;
    }

    public static function createTopFolder(Instance $instance): PublicFolder
    {
        return new PublicFolder(Folder::createTopFolder($instance->getRoot()->id, 'courseware', PublicFolder::class));
    }

    protected $folder;

    /**
     * {@inheritdoc}
     */
    public function __construct($folder = null)
    {
        $this->folder = $folder instanceof Folder ? $folder : Folder::build($folder);
        $this->folder['folder_type'] = get_class($this);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($attribute)
    {
        return $this->folder[$attribute];
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return _('Ein Ordner für öffentlich zugängliche Dateien einer Courseware');
    }

    /**
     * {@inheritdoc}
     */
    public static function availableInRange(SimpleORMap|string $range_id_or_object, string $user_id): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        return Icon::create('folder-public-full', $role);
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?string
    {
        return $this->folder->id;
    }

    /**
     * {@inheritdoc}
     */
    public function isVisible(string $user_id): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable(string $user_id): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(string $user_id): bool
    {
        // TODO
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEditable(string $user_id): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSubfolderAllowed(string $user_id): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescriptionTemplate(): Template|string|null
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getSubfolders(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?FolderType
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setDataFromEditTemplate(array|ArrayAccess $folderdata): FolderType|MessageBox
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile(string $file_ref_id): bool|array
    {
        $fileRefs = $this->folder->file_refs;

        if (is_array($fileRefs)) {
            foreach ($fileRefs as $fileRef) {
                if ($fileRef->id === $file_ref_id) {
                    return $fileRef->delete();
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function store(): bool
    {
        return $this->folder->store();
    }

    /**
     * {@inheritdoc}
     */
    public function createSubfolder(FolderType $foldertype): ?FolderType
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSubfolder(string $subfolder_id): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(): bool
    {
        return $this->folder->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function isFileDownloadable(FileRef $file_ref, string $user_id): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isFileEditable(FileRef $file_ref, string $user_id): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isFileWritable(FileRef $file_ref, string $user_id): bool
    {
        return false;
    }
}

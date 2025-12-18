<?php

namespace Community;

use User;
use JSONArrayObject;
use FileRef;

/**
 * CommunityGroupPinboardItem model.
 *
 * @author Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 *
 * @property int $id database column
 * @property int $group_id database column
 * @property string $owner_id database column
 * @property string $item_type database column
 * @property JSONArrayObject $payload database column
 * @property string $file_ref_id database column
 * @property int $position database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property \User $user belongs_to \User
 * @property CommunityGroup $group belongs_to CommunityGroup
 */

class CommunityGroupPinboardItem extends \SimpleORMap
{
    /**
     * @inheritdoc
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'community_group_pinboard_items';

        $config['serialized_fields']['payload'] = JSONArrayObject::class;

        $config['belongs_to']['group'] = [
            'class_name' => CommunityGroup::class,
            'foreign_key' => 'group_id',
        ];

        $config['belongs_to']['owner'] = [
            'class_name' => User::class,
            'foreign_key' => 'owner_id',
        ];

        $config['additional_fields']['file'] = [
            'get' => 'getFile',
            'set' => 'setFile'
        ];

        parent::configure($config);
    }

    public function getFile(): ?FileRef
    {
        if (!$this->file_ref_id) {
            return null;
        }

        return FileRef::find($this->file_ref_id);
    }

    public function setFile($file)
    {
        if (is_null($file)) {
            $this->file_ref_id = null;
        }

        if (is_a($file, FileRef::class)) {
            $this->file_ref_id = $file->getId();
        }
    }

    /**
     * Adds a new item to the group's pinboard.
     *
     * @param string $user_id The owner of the item
     * @param string $type The type of the item (text, link, file)
     * @param array|JSONArrayObject $payload The content data
     * @return CommunityGroupPinboardItem|null
     */
    public function addPinboardItem(string $user_id, string $type, $payload = []): ?CommunityGroupPinboardItem
    {
        $item = new CommunityGroupPinboardItem();
        $item->group_id = $this->id;
        $item->owner_id = $user_id;
        $item->item_type = $type;
        // Ensure we have a JSONArrayObject
        if (!($payload instanceof JSONArrayObject)) {
            $payload = new JSONArrayObject($payload);
        }
        $item->payload = $payload;

        return $item->store() ? $item : null;
    }
}

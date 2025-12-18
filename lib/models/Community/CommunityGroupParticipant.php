<?php

namespace Community;

use User;

/**
 * CommunityGroupParticipant model.
 *
 * @author Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 *
 * @property int $group_id database column
 * @property string $user_id database column
 * @property string $role database column
 * @property string $status database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property \User $user belongs_to \User
 * @property CommunityGroup $group belongs_to CommunityGroup
 */

class CommunityGroupParticipant extends \SimpleORMap
{
    // Participant role constants
    const ROLE_MODERATOR = 'moderator';
    const ROLE_FOLLOWER = 'follower';

    // Participant status constants
    const STATUS_MEMBER = 'member';
    const STATUS_PENDING = 'pending';
    const STATUS_BANNED = 'banned';

    /**
     * @inheritdoc
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'community_group_participants';

        $config['belongs_to']['group'] = [
            'class_name' => CommunityGroup::class,
            'foreign_key' => 'group_id',
        ];

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
        ];

        parent::configure($config);
    }

    /**
     * Helper to find a participant by a concatenated JSON:API ID.
     * Expected format: "groupid_userid"
     * * @param string $id The concatenated ID string.
     * @return CommunityGroupParticipant|null
     */
    public static function findByJsonApiId(string $id): ?self
    {
        $ids = explode('_', $id);
        if (count($ids) !== 2) {
            return null;
        }

        return self::findOneBySQL('group_id = ? AND user_id = ?', $ids);
    }

    /**
     * Returns a unique ID string for JSON:API.
     * * @return string
     */
    public function getJsonApiId(): string
    {
        return "{$this->group_id}_{$this->user_id}";
    }

    /**
     * Returns the full name of the participant.
     * Useful for sorting or direct display in templates.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->user ? $this->user->getFullName() : _('Unknown User');
    }

    /**
     * Checks if the participant has moderator roles.
     * * @return bool
     */
    public function isModerator(): bool
    {
        return $this->role === self::ROLE_MODERATOR;
    }
}

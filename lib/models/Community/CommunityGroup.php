<?php

namespace Community;

use User;

/**
 * CommunityGroup model.
 *
 * @author Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 *
 * @property int $id database column
 * @property string $name database column
 * @property string $description database column 
 * @property string $creator_id database column
 * @property boolean $is_private database column
 * @property string $status database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property \User $creator belongs_to \User
 * @property \SimpleORMapCollection $participants has_many CommunityGroupParticipant
 * @property \SimpleORMapCollection $pinboard_items has_many CommunityGroupPinboardItem
 */

class CommunityGroup extends \SimpleORMap
{
    // Group status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_DELETED = 'deleted';

    /**
     * @inheritdoc
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'community_groups';

        $config['belongs_to']['creator'] = [
            'class_name' => User::class,
            'foreign_key' => 'creator_id',
        ];

        $config['has_many']['participants'] = [
            'class_name' => CommunityGroupParticipant::class,
            'assoc_foreign_key' => 'group_id',
            'on_delete' => 'delete',
        ];

        $config['has_many']['pinboard_items'] = [
            'class_name' => CommunityGroupPinboardItem::class,
            'assoc_foreign_key' => 'group_id',
            'on_delete' => 'delete',
            'order_by' => 'ORDER BY mkdate DESC',
        ];

        parent::configure($config);
    }

    /**
     * Checks if the group is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Internal helper to find a participant in the already loaded collection.
     *
     * @param string $user_id
     * @return CommunityGroupParticipant|null
     */
    protected function getParticipant(string $user_id): ?CommunityGroupParticipant
    {
        return $this->participants->findOneBy('user_id', $user_id);
    }

    /**
     * Checks if a user is an active member of the group.
     *
     * @param string $user_id
     * @return bool
     */
    public function isMember(string $user_id): bool
    {
        return $this->getParticipant($user_id)?->status === CommunityGroupParticipant::STATUS_MEMBER;
    }

    /**
     * Checks if a user is a pending member of the group.
     *
     * @param string $user_id
     * @return bool
     */
    public function isPending(string $user_id): bool
    {
        return $this->getParticipant($user_id)?->status === CommunityGroupParticipant::STATUS_PENDING;
    }

    /**
     * Checks if a user has moderator privileges.
     *
     * @param string $user_id
     * @return bool
     */
    public function isModerator(string $user_id): bool
    {
        $participant = $this->getParticipant($user_id);
        return $participant
            && $participant->status === CommunityGroupParticipant::STATUS_MEMBER
            && $participant->isModerator();
    }

    /**
     * Returns all active members (status 'member') of this group.
     * This serves as the base for more specific role-based getters.
     *
     * @return \SimpleORMapCollection|CommunityGroupParticipant[]
     */
    public function getActiveParticipants(): \SimpleORMapCollection
    {
        return $this->participants->filter(function (CommunityGroupParticipant $participant) {
            return $participant->status === CommunityGroupParticipant::STATUS_MEMBER;
        });
    }

    /**
     * Returns all active moderators of this group.
     *
     * @return \SimpleORMapCollection|CommunityGroupParticipant[]
     */
    public function getModerators(): \SimpleORMapCollection
    {
        return $this->getActiveParticipants()->filter(function (CommunityGroupParticipant $participant) {
            return $participant->isModerator();
        });
    }

    /**
     * Returns all active followers of this group.
     *
     * @return \SimpleORMapCollection|CommunityGroupParticipant[]
     */
    public function getFollowers(): \SimpleORMapCollection
    {
        return $this->getActiveParticipants()->filter(function (CommunityGroupParticipant $participant) {
            return !$participant->isModerator();
        });
    }

    /**
     * Returns the number of active members.
     *
     * @return int
     */
    public function getMemberCount(): int
    {
        return count($this->getActiveParticipants());
    }

    /**
     * Handles a user's request to join the group.
     * If the group is private, the status is set to 'pending'.
     *
     * @param string $user_id
     * @return string The resulting status ('member', 'pending', or existing status)
     */
    /**
     * Handles a user's request to join the group.
     * * @param string $user_id The Stud.IP user_id
     * @return CommunityGroupParticipant|null The participant object or null on failure
     */
    public function addMember(string $user_id): ?CommunityGroupParticipant
    {
        if (!$this->isActive()) {
            return null;
        }

        $participant = $this->participants->findOneBy('user_id', $user_id);

        if (!$participant) {
            $participant = new CommunityGroupParticipant();
            $participant->group_id = $this->id;
            $participant->user_id = $user_id;
        }

        // Prevent updates if user is banned
        if ($participant->status === CommunityGroupParticipant::STATUS_BANNED) {
            return $participant;
        }

        $participant->status = $this->is_private
            ? CommunityGroupParticipant::STATUS_PENDING
            : CommunityGroupParticipant::STATUS_MEMBER;
        $participant->role = CommunityGroupParticipant::ROLE_FOLLOWER;

        return $participant->store() ? $participant : null;
    }

    /**
     * Finds all community groups for a specific user, optionally filtered by participant status.
     *
     * @param string $user_id The ID of the user.
     * @param array|string $status The status(es) to filter by (defaults to MEMBER and MODERATOR).
     * @return CommunityGroup[] An array of CommunityGroup objects.
     */
    public static function findByUserId(string $user_id, $status = [CommunityGroupParticipant::STATUS_MEMBER, CommunityGroupParticipant::STATUS_MODERATOR]): array
    {
        return self::findAndMapBySQL(
            function ($group) {
                return $group;
            },
            "INNER JOIN community_group_participants ON (community_group_participants.group_id = community_groups.id)
                    WHERE community_group_participants.user_id = ?
                        AND community_group_participants.status IN (?)
                    ORDER BY community_groups.name ASC",
            [$user_id, (array) $status]
        );
    }
}

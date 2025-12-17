<?php

namespace JsonApi\Routes\Community;

use User;
use \Community\CommunityGroup as CommunityGroup;
use \Community\CommunityGroupParticipant as CommunityGroupParticipant;
use \Community\CommunityGroupPinboardItem as CommunityGroupPinboardItem;

/**
 * Community routes Authority class.
 *
 * @author Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */

class Authority
{
    public static function canCreateCommunityGroup(User $user): bool
    {
        return $GLOBALS['perm']->have_perm(perm: 'author');
    }

    public static function canShowCommunityGroup(User $user, CommunityGroup $group): bool
    {
        if ($GLOBALS['perm']->have_perm(perm: 'root')) {
            return true;
        }
        if (!$group->is_private) {
            return true;
        }

        return $group->isMember($user->id) || $group->isPending($user->id);
    }

    public static function canUpdateCommunityGroup(User $user, CommunityGroup $group): bool
    {
        if ($GLOBALS['perm']->have_perm(perm: 'root')) {
            return true;
        }

        return $group->isModerator($user->id);
    }

    public static function canDeleteCommunityGroup(User $user, CommunityGroup $group): bool
    {
        return self::canUpdateCommunityGroup($user, $group);
    }

    public static function canCreateCommunityGroupParticipant(User $user, CommunityGroup $group): bool
    {
        // group must be active
        if (!$group->isActive()) {
            return false;
        }

        return true;
    }

    public static function canShowCommunityGroupParticipant(User $user, CommunityGroup $group): bool
    {
        if (!$group->is_private) {
            return true;
        }

        if ($group->isMember($user->id)) {
            return true;
        }

        return $GLOBALS['perm']->have_perm('root');
    }

    public static function canDeleteCommunityGroupParticipant(User $user, CommunityGroupParticipant $participant, CommunityGroup $group): bool
    {
        if ($user->id === $participant->user_id) {
            return true;
        }

        if ($group->isModerator($user->id)) {
            return true;
        }

        return $GLOBALS['perm']->have_perm(perm: 'root');
    }

    public static function canUpdateCommunityGroupParticipant(User $user, CommunityGroupParticipant $participant, CommunityGroup $group): bool
    {
        if ($group->isModerator($user->id)) {
            return true;
        }

        return $GLOBALS['perm']->have_perm('root');
    }

    public static function canCreatePinboardItem(User $user, CommunityGroup $group): bool
    {
        if ($GLOBALS['perm']->have_perm('root')) {
            return true;
        }

        return $group->isMember($user->id) || $group->isModerator($user->id);
    }
}
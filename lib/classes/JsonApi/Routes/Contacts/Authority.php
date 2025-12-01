<?php

namespace JsonApi\Routes\Contacts;

use User;
use ContactGroup;

class Authority
{
    public static function canIndexGroups(User $user)
    {
        return $GLOBALS['perm']->have_perm('user'); // TODO: What to check here?
    }

    public static function canCreateGroups(User $user)
    {
        return $GLOBALS['perm']->have_perm('user'); // TODO: What to check here?
    }

    public static function canShowGroups(User $user, ContactGroup $resource)
    {
        return $resource->owner->id === $user->id;
    }

    public static function canDeleteGroups(User $user, ContactGroup $resource)
    {
        return $resource->owner->id === $user->id;
    }

    public static function canUpdateGroups(User $user, ContactGroup $resource)
    {
        return $resource->owner->id === $user->id;
    }

    public static function canManageGroups(User $user, ContactGroup $resource)
    {
        return $resource->owner->id === $user->id;
    }

    public static function canAddUsersToGroup(User $user, User $userToAdd)
    {
        if ($GLOBALS['perm']->have_perm('root', $user->id)) {
            return true;
        }

        if ($userToAdd->locked) {
            return false;
        }

        if (get_visibility_by_id($userToAdd->id)) {
            return true;
        }

        return false;
    }

    public static function canDownloadUserVCard(User $user, User $observedUser)
    {
        if ($user->id === $observedUser->id) {
            return true;
        }

        if ($GLOBALS['perm']->have_perm('root', $user->id)) {
            return true;
        }

        if (get_visibility_by_id($observedUser->id)) {
            return true;
        }

        return false;
    }
}

<?php

namespace JsonApi\Routes\Users;

use User;

class Authority
{
    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function canIndexUsers(User $user)
    {
        return true;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function canShowUser(User $user, User $userToShow)
    {
        if ($userToShow->id === $user->id || $GLOBALS['perm']->have_perm('root', $user->id)) {
            return true;
        }

        if ($userToShow->locked) {
            return false;
        }

        if (get_visibility_by_id($userToShow->id)) {
            return true;
        }

        return false;
    }

    public static function canEditUser(User $user, User $userToShow)
    {
        return $user->id === $userToShow->id;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function canDeleteUser(User $user, User $userToDelete)
    {
        if (!$GLOBALS['perm']->have_perm('root', $user->id)) {
            return false;
        }

        if ($userToDelete->id === $user->id) {
            return false;
        }

        return true;
    }

    public static function canPublishScore(User $user, User $targetedUser): bool
    {
        return !empty(\Config::get()->SCORE_ENABLE) && $targetedUser->id === $user->id;
    }

    public static function canUnpublishScore(User $user, User $targetedUser): bool
    {
        return !empty(\Config::get()->SCORE_ENABLE) && $targetedUser->id === $user->id;
    }
}

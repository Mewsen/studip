<?php

namespace JsonApi\Routes\MassMail;

use MassMail\MassMailPermission;
use User;

class Authority
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function canShowMassMailPermissions(User $user, MassMailPermission $permission): bool
    {
        return MassMailPermission::has($user->id, true);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function canIndexMassMailPermissions(User $user): bool
    {
        return MassMailPermission::has($user->id, true);
    }

    public static function canIndexMassMailMessages(User $user): bool
    {
        return MassMailPermission::has($user->id);
    }
}

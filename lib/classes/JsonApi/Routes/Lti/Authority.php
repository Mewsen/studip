<?php

namespace JsonApi\Routes\Lti;

use Range;
use User;

class Authority
{
    public static function canShowRegistration(Range $range, ?User $user): bool
    {
        return $range->isAccessibleToUser($user?->user_id);
    }

    public static function canShowLti(User $user, ?Range $range = null): bool
    {
        return (bool) $range?->isAccessibleToUser($user->user_id);
    }

    public static function canIndexLtiRegistrations(User $user): bool
    {
        return static::canShowLti($user);
    }
}

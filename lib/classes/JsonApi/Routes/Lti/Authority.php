<?php

namespace JsonApi\Routes\Lti;

use Range;
use User;

class Authority
{
    public static function canShowLti(User $user, ?Range $range = null): bool
    {
        return $range?->isAccessibleToUser($user->user_id);
    }

    public static function canIndexLtiTools(User $user): bool
    {
        return static::canShowLti($user);
    }
}

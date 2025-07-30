<?php

namespace JsonApi\Routes\Forum;

use Range;
use User;

class ForumAuthority
{
    public static function canShowForum(Range $range, ?User $user = null): bool
    {
        return $range->isAccessibleToUser($user?->id);
    }
}

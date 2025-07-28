<?php
namespace JsonApi\Routes\Forum;

use Range;
use User;

class Authority
{
    public static function canShowForum(?User $user, Range $range): bool
    {
        return $range->isAccessibleToUser($user?->user_id);
    }
}

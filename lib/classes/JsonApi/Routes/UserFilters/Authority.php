<?php

namespace JsonApi\Routes\UserFilters;

use Config, User, UserFilter;

class Authority
{
    public static function canEditUserFilters(User $user, UserFilter $filter): bool
    {
        return $filter->canEdit($user);
    }
}

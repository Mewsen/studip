<?php

namespace JsonApi\Routes\UserFilters;

use Config;
use User;

class Authority
{
    public static function canEditUserFilters(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('admin', $user->id)
            || (
                Config::get()->ALLOW_DOZENT_COURSESET_ADMIN
                && $GLOBALS['perm']->have_perm('dozent', $user->id)
            );
    }
}

<?php

namespace JsonApi\Routes\Institutes;

use Institute;
use User;

class Authority
{
    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function canEditInstitute(User $user, Institute $institute)
    {
        return $GLOBALS['perm']->have_studip_perm('admin', $institute->id, $user->id);
    }
}

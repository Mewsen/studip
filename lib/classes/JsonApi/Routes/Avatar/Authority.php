<?php

namespace JsonApi\Routes\Avatar;

use Avatar;
use User;
use Course;
use Institute;

class Authority
{
    public static function canShowAvatarOfRange(User $user, Avatar $resource): bool
    {
        return true;
    }

    public static function canUpdateAvatarOfUser(User $user): bool
    {
        return $user->hasPermissionLevel('user', $user);
    }
    public static function canUpdateAvatarOfInstitute(User $user, Institute $institute): bool
    {
        return $user->hasPermissionLevel('admin', $institute);
    }
    public static function canUpdateAvatarOfSeminar(User $user, Course $course): bool
    {
        return $user->hasPermissionLevel('tutor', $course);
    }
}
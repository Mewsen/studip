<?php

namespace JsonApi\Routes\Themes;

use User;

class Authority
{
    public static function canIndexThemes(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('root', $user->id);
    }
    public static function canShowTheme(User $user): bool
    {
        return self::canIndexThemes($user);
    }

    public static function canDeleteTheme(User $user): bool
    {
        return self::canIndexThemes($user);
    }

    public static function canUpdateTheme(User $user): bool
    {
        return self::canIndexThemes($user);
    }

    public static function canCreateTheme(User $user): bool
    {
        return self::canIndexThemes($user);
    }
}
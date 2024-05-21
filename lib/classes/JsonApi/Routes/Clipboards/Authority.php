<?php
namespace JsonApi\Routes\Clipboards;

use User;

final class Authority
{
    public static function canCreateClipboard(User $user): bool
    {
        return true;
    }

    public static function canAccessClipboard(User $user, \Clipboard $clipboard): bool
    {
        return $user->id === $clipboard->user_id
            || $user->perms === 'root';
    }

    public static function canUpdateClipboard(User $user, \Clipboard $clipboard): bool
    {
        return self::canAccessClipboard($user, $clipboard);
    }

    public static function canDeleteClipboard(User $user, \Clipboard $clipboard): bool
    {
        return self::canUpdateClipboard($user, $clipboard);
    }
}

<?php
namespace JsonApi\Routes\ShortUrls;

use User;

final class Authority
{
    public static function canCreateShortUrl(User $user): bool
    {
        return true;
    }

    public static function canAccessShortUrl(User $user, \ShortUrl $short_url): bool
    {
        return $user->id === $short_url->user_id;
    }

    public static function canUpdateShortUrl(User $user, \ShortUrl $short_url): bool
    {
        return self::canAccessShortUrl($user, $short_url);
    }

    public static function canDeleteShortUrl(User $user, \ShortUrl $short_url): bool
    {
        return self::canUpdateShortUrl($user, $short_url);
    }
}

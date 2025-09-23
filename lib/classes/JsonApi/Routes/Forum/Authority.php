<?php
namespace JsonApi\Routes\Forum;

use Forum\Posting;
use Range;
use User;

class Authority
{
    public static function canShowForum(?User $user, Range $range): bool
    {
        return $range->isAccessibleToUser($user?->user_id);
    }

    public static function canEditPost(User $user, Posting $posting, $isDiscussionClosed = false): bool
    {
        return (!$isDiscussionClosed && $posting->user_id === $user->user_id) || $GLOBALS['perm']->have_studip_perm('tutor', $posting->range_id, $user->id);
    }

    public static function canDeletePost(User $user, Posting $posting, $isDiscussionClosed = false): bool
    {
        return self::canEditPost($user, $posting, $isDiscussionClosed);
    }
}

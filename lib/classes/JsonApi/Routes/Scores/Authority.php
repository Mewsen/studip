<?php

namespace JsonApi\Routes\Scores;

use User;
class Authority
{
    public static function canIndexScores(User $user): bool
    {
        return !empty(\Config::get()->SCORE_ENABLE) && $GLOBALS['perm']->have_perm('user', $user->id);
    }
}

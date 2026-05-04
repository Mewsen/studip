<?php

namespace JsonApi\Routes\ProfileCategories;

use Kategorie;
use User;

class Authority
{
    public static function canShowCategory(User $user, Kategorie $category): bool
    {
        return \Visibility::verify('kat_' . $category->id, $category->range_id, $user->id);
    }
}

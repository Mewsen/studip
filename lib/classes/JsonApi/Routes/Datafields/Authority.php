<?php

namespace JsonApi\Routes\Datafields;

use DataField;
use User;

class Authority
{
    public static function canShowDatafield(User $user, DataField $datafield)
    {
        return $datafield->accessAllowed($user->perms);
    }
}

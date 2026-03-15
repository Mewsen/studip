<?php

namespace JsonApi\Authorities\Courseware;

use Courseware\StructuralElement;
use JsonApi\Authorities\SORMAuthority;
use JsonApi\SORM;
use User;

final class StructuralElementAuthority implements SORMAuthority
{
    /**
     * @param User|null $user
     * @param StructuralElement $sorm
     *
     * @return bool
     */
    public function mayCreate(?User $user, SORM $sorm): bool
    {
        return $sorm->canEdit($user);
    }

    /**
     * @param User|null $user
     * @param StructuralElement $sorm
     *
     * @return bool
     */
    public function mayAccess(?User $user, SORM $sorm): bool
    {
        return $sorm->canRead($user);
    }

    /**
     * @param User|null $user
     * @param StructuralElement $sorm
     *
     * @return bool
     */
    public function mayEdit(?User $user, SORM $sorm): bool
    {
        return $sorm->canEdit($user);
    }

    /**
     * @param User|null $user
     * @param StructuralElement $sorm
     *
     * @return bool
     */
    public function mayDelete(?User $user, SORM $sorm): bool
    {
        return $sorm->canEdit($user);
    }
}

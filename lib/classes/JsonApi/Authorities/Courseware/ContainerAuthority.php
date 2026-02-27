<?php

namespace JsonApi\Authorities\Courseware;

use Courseware\Container;
use JsonApi\Authorities\SORMAuthority;
use JsonApi\SORM;
use User;

final class ContainerAuthority implements SORMAuthority
{
    /**
     * @param User|null $user
     * @param Container $sorm
     *
     * @return bool
     */
    public function mayCreate(?User $user, SORM $sorm): bool
    {
        return $sorm->getStructuralElement()->canEdit($user);
    }

    /**
     * @param User|null $user
     * @param Container $sorm
     *
     * @return bool
     */
    public function mayAccess(?User $user, SORM $sorm): bool
    {
        return $sorm->getStructuralElement()->canRead($user);
    }

    /**
     * @param User|null $user
     * @param Container $sorm
     *
     * @return bool
     */
    public function mayEdit(?User $user, SORM $sorm): bool
    {
        return $sorm->getStructuralElement()->canEdit($user);
    }

    /**
     * @param User|null $user
     * @param Container $sorm
     *
     * @return bool
     */
    public function mayDelete(?User $user, SORM $sorm): bool
    {
        return $sorm->getStructuralElement()->canEdit($user);
    }
}

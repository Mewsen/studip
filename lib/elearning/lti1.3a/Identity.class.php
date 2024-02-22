<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\User\UserIdentityInterface;
use OAT\Library\Lti1p3Core\Util\Collection\CollectionInterface;

class Identity implements UserIdentityInterface
{
    protected \User $user;

    public function __construct(\User $user)
    {
        $this->user = $user;
    }

    #[\Override] public function getIdentifier(): string
    {
        return $this->user->id;
    }

    #[\Override] public function getName(): ?string
    {
        return $this->user->getFullName();
    }

    #[\Override] public function getEmail(): ?string
    {
        return $this->user->email;
    }

    #[\Override] public function getGivenName(): ?string
    {
        return $this->user->vorname;
    }

    #[\Override] public function getFamilyName(): ?string
    {
        return $this->user->nachname;
    }

    #[\Override] public function getMiddleName(): ?string
    {
        return '';
    }

    #[\Override] public function getLocale(): ?string
    {
        return $this->user->preferred_language;
    }

    #[\Override] public function getPicture(): ?string
    {
        return \Avatar::getAvatar($this->user->id)->getURL();
    }

    #[\Override] public function getAdditionalProperties(): CollectionInterface
    {
        return [];
    }

    #[\Override] public function normalize(): array
    {
        // TODO: Implement normalize() method.
        return [];
    }
}

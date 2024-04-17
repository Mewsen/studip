<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Message\Payload\MessagePayloadInterface;
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
        return \Avatar::getAvatar($this->user->id)->getURL(\Avatar::MEDIUM);
    }

    #[\Override] public function getAdditionalProperties(): CollectionInterface
    {
        return [];
    }

    #[\Override] public function normalize(): array
    {
        return [
            MessagePayloadInterface::CLAIM_SUB => $this->getIdentifier(),
            MessagePayloadInterface::CLAIM_USER_NAME => $this->getName(),
            MessagePayloadInterface::CLAIM_USER_EMAIL => $this->getEmail(),
            MessagePayloadInterface::CLAIM_USER_GIVEN_NAME => $this->getGivenName(),
            MessagePayloadInterface::CLAIM_USER_FAMILY_NAME => $this->getFamilyName(),
            MessagePayloadInterface::CLAIM_USER_MIDDLE_NAME => $this->getMiddleName(),
            MessagePayloadInterface::CLAIM_USER_LOCALE => $this->getLocale(),
            MessagePayloadInterface::CLAIM_USER_PICTURE => $this->getPicture()
        ];
    }
}

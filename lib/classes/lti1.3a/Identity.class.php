<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Message\Payload\MessagePayloadInterface;
use OAT\Library\Lti1p3Core\User\UserIdentityInterface;
use OAT\Library\Lti1p3Core\Util\Collection\CollectionInterface;


class Identity implements UserIdentityInterface
{
    protected \User $user;

    protected array $allowed_optional_fields = [];

    public function __construct(\User $user, \LtiDeployment $deployment)
    {
        $this->user = $user;

        $privacy_settings = \LtiDeploymentPrivacySettings::findOneBySQL(
            '`deployment_id` = :deployment_id AND `user_id` = :user_id',
            ['deployment_id' => $deployment->id, 'user_id' => $user->id]
        );
        if ($privacy_settings) {
            $this->allowed_optional_fields = explode(',', $privacy_settings->allowed_optional_fields);
        }
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
        if (!in_array('lang', $this->allowed_optional_fields)) {
            return '';
        }
        return $this->user->preferred_language;
    }

    #[\Override] public function getPicture(): ?string
    {
        if (!in_array('avatar_url', $this->allowed_optional_fields)) {
            return '';
        }
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

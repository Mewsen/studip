<?php
namespace Studip\LTI13a;

use Avatar;
use LtiToolPrivacySettings;
use User;
use OAT\Library\Lti1p3Core\Message\Payload\MessagePayloadInterface;
use OAT\Library\Lti1p3Core\User\UserIdentityInterface;
use OAT\Library\Lti1p3Core\Util\Collection\CollectionInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;

class Identity implements UserIdentityInterface
{
    protected User $user;

    protected array $allowed_optional_fields = [];

    public function __construct(User $user, RegistrationInterface $registration)
    {
        $this->user = $user;

        $privacy_settings = LtiToolPrivacySettings::findOneBySQL(
            '`registration_id` = :registration_id AND `user_id` = :user_id',
            ['registration_id' => $registration->getIdentifier(), 'user_id' => $user->id]
        );
        if ($privacy_settings) {
            $this->allowed_optional_fields = explode(',', $privacy_settings->allowed_optional_fields);
        }
    }

    public function getIdentifier(): string
    {
        return $this->user->id;
    }

    public function getName(): ?string
    {
        return $this->user->getFullName();
    }

    public function getEmail(): ?string
    {
        return $this->user->email;
    }

    public function getGivenName(): ?string
    {
        return $this->user->vorname;
    }

    public function getFamilyName(): ?string
    {
        return $this->user->nachname;
    }

    public function getMiddleName(): ?string
    {
        return '';
    }

    public function getLocale(): ?string
    {
        if (!in_array('lang', $this->allowed_optional_fields)) {
            return '';
        }
        return $this->user->preferred_language;
    }

    public function getPicture(): ?string
    {
        if (!in_array('avatar_url', $this->allowed_optional_fields)) {
            return '';
        }
        return Avatar::getAvatar($this->user->id)->getURL(Avatar::MEDIUM);
    }

    public function getAdditionalProperties(): CollectionInterface
    {
        return [];
    }

    public function normalize(): array
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

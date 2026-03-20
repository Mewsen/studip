<?php
namespace Studip\Lti\LTI1p3;

use User;
use Avatar;
use Lti\RegistrationPrivacySettings;
use OAT\Library\Lti1p3Core\User\UserIdentity AS BaseUserIdentity;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;

final class UserIdentity extends BaseUserIdentity
{
    private array $optionalFields = [];

    public function __construct(
        private User $user,
        private RegistrationInterface $registration
    ) {
        $this->optionalFields = $this->loadOptionalFields();

        parent::__construct(
            $this->user->username,
            $this->user->getFullName(),
            $this->user->email,
            $this->user->vorname,
            $this->user->nachname,
            null,
            $this->getUserLocale(),
            $this->getUserProfilePicture()
        );
    }

    private function getUserLocale(): ?string
    {
        if (!$this->hasOptionalField('lang')) {
            return null;
        }

        return explode('_', $this->user->preferred_language)[0] ?? null;
    }

    private function getUserProfilePicture(): ?string
    {
        if (!$this->hasOptionalField('avatar_url')) {
            return null;
        }

        return Avatar::getAvatar($this->user->id)->getURL(Avatar::MEDIUM);
    }

    private function loadOptionalFields(): array
    {
        $privacySettings = RegistrationPrivacySettings::findOneBySQL(
            "`registration_id` = :registration_id AND `user_id` = :user_id",
            [
                'registration_id' => $this->registration->getIdentifier(),
                'user_id' => $this->user->id,
            ]
        );

        if (!$privacySettings || empty($privacySettings->allowed_optional_fields)) {
            return [];
        }

        return explode(',', $privacySettings->allowed_optional_fields);
    }

    private function hasOptionalField(string $field): bool
    {
        return in_array($field, $this->optionalFields, true);
    }
}

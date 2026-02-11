<?php
namespace Studip\Lti\LTI1p3;

use Range;
use Course;
use Metrics;
use CourseMember;
use User;
use Lti\Publication;
use Lti\PublicationUser;
use Lti\UserIdentityMapping;
use Studip\Lti\Enum\UserIdentityMappingContext;
use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\User\UserIdentityInterface;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use Studip\Authentication\Manager as AuthenticationManager;

final class UserManager
{
    private Publication $publication;
    private array $localRoles;

    private string $registrationId;
    private Range $range;
    private ?User $user = null;
    private ?UserIdentityInterface $userIdentity = null;

    /**
     * @throws LtiExceptionInterface
     */
    public function enroll(Publication $publication, array $localRoles, string $registrationId): self
    {
        $this
            ->setPublication($publication)
            ->setLocalRoles($localRoles)
            ->setRegistrationId($registrationId)
            ->syncUser()
            ->syncRangeMember();

        return $this;
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function authenticate(?User $user = null): self
    {
        $user ??= $this->getUser();

        if (!$user) {
            throw new LtiException('Authentication failed: no user could be identified.');
        }

        auth()->setAuthenticatedUser($user);
        Metrics::increment('core.login.succeeded');
        sess()->regenerateId(AuthenticationManager::DEFAULT_KEPT_SESSION_VARIABLES);

        return $this;
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function syncUser(?User $user = null): self
    {
        if (!$this->userIdentity) {
            return $this;
        }

        $user ??= $this->getUser();

        if (!$user) {
            if (!$this->userIdentity->getEmail()) {
                throw new LtiException('Failed to enroll user: Missing email address.');
            }

            $userIdentityMapping = UserIdentityMapping::findOneBySQL(
                "context = :context AND external_email = :external_email AND external_user_id = :external_user_id AND registration_id = :registration_id",
                [
                    'context' => UserIdentityMappingContext::ResourceLink->value,
                    'external_email' => $this->userIdentity->getEmail(),
                    'external_user_id' => $this->userIdentity->getIdentifier(),
                    'registration_id' => $this->registrationId
                ]
            );

            if ($userIdentityMapping) {
                $user = $userIdentityMapping->user;
            } else {
                $user = new User();
                $username = $this->userIdentity->getIdentifier() . '_' . $this->registrationId . '_' . bin2hex(random_bytes(6));
                $user->setData([
                    'username' => strtolower($username),
                    'Email' => $this->userIdentity->getEmail(),
                    'auth_plugin' => 'LTI13a'
                ]);
            }
        }

        if ($user->auth_plugin === 'LTI13a') {
            $user->setData([
                'Vorname' => $this->userIdentity->getGivenName() ?? $user?->Vorname ?? 'Anonym',
                'Nachname' => $this->userIdentity->getFamilyName() ?? $user?->Nachname ?? 'Anonym',
                'perms' => $this->resolveLocalContextRole()
            ]);
        }

        $user->store();

        $this->setUser($user)->syncUserIdentityMapping();

        return $this;
    }

    public function syncRangeMember(?User $user = null): self
    {
        $user ??= $this->getUser();

        if ($user && $this->range instanceof Course) {
            $courseMember = CourseMember::findOneBySQL("user_id = :user_id AND Seminar_id = :range_id",
                [
                    'user_id' => $user->id,
                    'range_id' => $this->range->id
                ]
            );

            if (!$courseMember) {
                $courseMember = $this->range->addMember($user, $this->resolveLocalContextRole(), false);
                $courseMember->comment = _('Eingeschrieben über LTI13a.');
            }

            $courseMember->status = $this->resolveLocalContextRole();
            $courseMember->store();

            PublicationUser::firstOrCreate([
                'user_id' => $user->id,
                'publication_id' => $this->publication->id
            ]);
        }

        return $this;
    }

    public function syncUserIdentityMapping(?string $context = null): self
    {
        UserIdentityMapping::updateOrCreate([
            'registration_id' => $this->registrationId,
            'user_id' => $this->user->id,
            'external_user_id' => $this->userIdentity->getIdentifier(),
            'external_email' => $this->userIdentity->getEmail(),
            'context' => $context ?? UserIdentityMappingContext::ResourceLink->value
        ]);

        return $this;
    }

    public function resolveLocalContextRole(): string
    {
        $publicationConfigs = $this->publication->getConfigValues();

        return match ($this->localRoles['course']) {
            'dozent' => $publicationConfigs['instructor_role'] ?? 'dozent',
            'autor'  => $publicationConfigs['instructor_role'] ?? 'autor',
            default  => $this->localRoles['course'] ?? 'user'
        };
    }

    public function getPublication(): Publication
    {
        return $this->publication;
    }

    public function setPublication(Publication $publication): self
    {
        $this->publication = $publication;
        $this->range = $publication->range;

        return $this;
    }

    public function getLocalRoles(): array
    {
        return $this->localRoles;
    }

    public function setLocalRoles(array $localRoles): self
    {
        $this->localRoles = $localRoles;

        return $this;
    }

    public function getRegistrationId(): string
    {
        return $this->registrationId;
    }

    public function setRegistrationId(string $registrationId): self
    {
        $this->registrationId = $registrationId;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getUserIdentity(): ?UserIdentityInterface
    {
        return $this->userIdentity;
    }

    public function setUserIdentity(UserIdentityInterface $userIdentity): self
    {
        $this->userIdentity = $userIdentity;
        return $this;
    }
}

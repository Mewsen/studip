<?php
namespace Studip\LTI13a;

use Course;
use Lti\Enum\UserIdentityMappingContext;
use User;
use Metrics;
use Range;
use CourseMember;
use Lti\Publication;
use Lti\UserIdentityMapping;
use Lti\PublicationUser;
use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Core\User\UserIdentityInterface;
use Studip\Authentication\Manager as AuthenticationManager;

final class UserManager
{
    private Publication $publication;
    private array $localRoles;

    private string $registrationId;
    private Range $range;
    private ?User $user;
    private ?UserIdentityInterface $userIdentity;

    private ?CourseMember $courseMember;

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
        $user ??= $this->user;

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

        $user ??= User::findOneBySQL(
            "JOIN lti_publication_users USING(user_id)
                    WHERE email = :email
                    AND auth_plugin = 'LTI13a'
                    AND lti_publication_users.registration_id = :registration_id
                    AND lti_publication_users.external_user_id = :external_user_id",
            [
                'email' => $this->userIdentity->getEmail(),
                'external_user_id' => $this->userIdentity->getIdentifier(),
                'registration_id' => $this->registrationId
            ]
        );

        if (!$user) {
            if (!$this->userIdentity->getGivenName() || !$this->userIdentity->getFamilyName()) {
                throw new LtiException('Failed to enroll user: Missing name information.');
            }

            $user = new User();
            $username = $this->userIdentity->getIdentifier() . '_' . $this->registrationId . '_' . bin2hex(random_bytes(6));
            $user->setData([
                'username' => strtolower($username),
                'Email' => $this->userIdentity->getEmail(),
                'auth_plugin' => 'LTI13a'
            ]);
        }

        $user->setData([
            'Vorname' => $this->userIdentity->getGivenName() ?? $user->Vorname,
            'Nachname' => $this->userIdentity->getFamilyName() ?? $user->Nachname,
            'perms' => $user->auth_plugin === 'standard' ? $user->perms : $this->resolveLocalContextRole()
        ]);

        $user->store();
        $this->setUser($user);

        return $this;
    }

    public function syncRangeMember(?User $user = null): self
    {
        $user ??= $this->user;

        if ($user && $this->range instanceof Course) {
            $this->courseMember = $this->range->addMember($user, $this->resolveLocalContextRole(), false);
            $this->courseMember->comment = _('Eingeschrieben über LTI13a.');
            $this->courseMember->store();

            PublicationUser::firstOrCreate([
                'user_id' => $user->id,
                'publication_id' => $this->publication->id
            ]);

            $this->setUser($user)->syncUserIdentityMapping();
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

    public function getCourseMember(): ?CourseMember
    {
        return $this->courseMember;
    }

    public function setCourseMember(CourseMember $courseMember): self
    {
        $this->courseMember = $courseMember;
        return $this;
    }
}

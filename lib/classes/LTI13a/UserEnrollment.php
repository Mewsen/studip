<?php
namespace Studip\LTI13a;

use Lti\PublicationUser;
use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use User;
use Range;
use CourseMember;
use Lti\Publication;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Result\LaunchValidationResultInterface;
use OAT\Library\Lti1p3Core\User\UserIdentityInterface;

final class UserEnrollment
{
    protected UserIdentityInterface $userIdentity;
    protected Range $range;
    public function __construct(
        protected LaunchValidationResultInterface $request,
        protected Publication $publication
    ) {
        $this->range = $publication->range;
        $this->userIdentity = $request->getPayload()->getUserIdentity();
    }

    public function enroll(): User
    {
        $user = $this->syncUser();

        $this->syncCourseMember($user);

        return $user;
    }

    /**
     * @throws LtiExceptionInterface
     *
     */
    private function syncUser(): User
    {

        $user = User::findOneBySQL(
            "email = :email AND auth_plugin = 'LTI13a'",
            ['email' => $this->userIdentity->getEmail()]
        );

        if (!$user) {
            if (!$this->userIdentity->getGivenName() || !$this->userIdentity->getFamilyName()) {
                throw new LtiException('Failed to enroll user: Missing name information.');
            }

            $user = new User();
            $username = 'lti13a.'.str_replace('@', '_', $this->userIdentity->getEmail());
            $user->setData([
                'username' => strtolower($username),
                'Email' => $this->userIdentity->getEmail(),
                'auth_plugin' => 'LTI13a'
            ]);
        }

        $user->setData([
            'Vorname' => $this->userIdentity->getGivenName() ?? $user->Vorname,
            'Nachname' => $this->userIdentity->getFamilyName() ?? $user->Nachname,
            'perms' => $this->resolveLocalContextRole()
        ]);
        $user->store();

        PublicationUser::updateOrCreate([
            'publication_id' => $this->publication->id,
            'user_id' => $user->id
        ]);

        return $user;
    }

    private function syncCourseMember(User $user): CourseMember
    {
        $courseMember = CourseMember::findOneBySQL(
            "Seminar_id = :seminar_id AND user_id = :user_id",
            [
                'seminar_id' => $this->range->id,
                'user_id' => $user->id
            ]
        );

        if (!$courseMember) {
            (new PublicationValidator($this->publication))->validateEnrollment();

            $courseMember = new CourseMember();
            $courseMember->setData([
                'seminar_id' => $this->range->id,
                'user_id' => $user->id,
                'comment' => _('Eingeschrieben über LTI13a.')
            ]);
        }

        $courseMember->setData([
            'status' => $this->resolveLocalContextRole()
        ]);
        $courseMember->store();

        return $courseMember;
    }

    private function resolveLocalContextRole(): string
    {
        $localRole = RoleMapper::toLocal($this->request->getPayload()->getRoles());
        $publicationConfigs = $this->publication->getConfigValues();

        return match ($localRole['course']) {
            'dozent' => $publicationConfigs['dozent_role'] ?? 'dozent',
            'autor'  => $publicationConfigs['autor_role'] ?? 'autor',
            default  => $localRole['course'] ?? 'user'
        };
    }
}

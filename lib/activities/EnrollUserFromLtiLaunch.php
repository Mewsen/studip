<?php
namespace Studip\Activity;

use User;
use Lti\Publication;
use Studip\LTI13a\UserEnrollment;

final class EnrollUserFromLtiLaunch
{
    public static function enroll(string $event, User $user, array $eventData): void
    {
        if (empty($_SESSION['callbacks'][$eventData['callback_id']])) {
            return;
        }

        $ltiCallbackData = $_SESSION['callbacks'][$eventData['callback_id']];
        if (
            $ltiCallbackData['context'] !== 'lti'
            || $ltiCallbackData['action'] !== 'enroll_user'
            || $ltiCallbackData['expires_at'] < time()
        ) {
            return;
        }

        $publication = Publication::find($ltiCallbackData['publication_id']);
        $userEnrollment = new UserEnrollment($publication, $ltiCallbackData['local_roles'], $ltiCallbackData['registration_id']);
        $userEnrollment
            ->setUser($user)
            ->syncRangeMember();

        unset($_SESSION['callbacks'][$eventData['callback_id']]);
    }
}

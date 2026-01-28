<?php
namespace Studip\Activity;

use Lti\Enum\UserIdentityMappingContext;
use User;
use Lti\Publication;
use Studip\LTI13a\UserManager;

final class LtiCallbackProcessor
{
    public static function handle(string $event, User $user, array $eventData): void
    {
        if (!static::isValidEventData($eventData)) {
            return;
        }

        $ltiCallbackData = $_SESSION['callbacks'][$eventData['callback_id']];
        $userManager = new UserManager();
        $userManager
            ->setUser($user)
            ->setUserIdentity($ltiCallbackData['user_identity']);

        switch ($ltiCallbackData['action']) {
            case 'enroll_user':
                $publication = Publication::find($ltiCallbackData['publication_id']);

                $userManager
                    ->enroll($publication, $ltiCallbackData['local_roles'], $ltiCallbackData['registration_id'])
                    ->syncRangeMember();
                break;
            case 'deeplink_callback':
                $userManager
                    ->setRegistrationId($ltiCallbackData['registration_id'])
                    ->syncUserIdentityMapping(UserIdentityMappingContext::DeepLink->value);
                break;
        }
    }

    private static function isValidEventData(array $eventData): bool
    {
        if (empty($_SESSION['callbacks'][$eventData['callback_id']])) {
            return false;
        }

        $ltiCallbackData = $_SESSION['callbacks'][$eventData['callback_id']];
        if (
            $ltiCallbackData['context'] !== 'lti'
            || $ltiCallbackData['expires_at'] < time()
        ) {
            return false;
        }

        return true;
    }
}

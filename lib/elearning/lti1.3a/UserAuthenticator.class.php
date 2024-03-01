<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Security\User\UserAuthenticatorInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Security\User\Result\UserAuthenticationResultInterface;
use OAT\Library\Lti1p3Core\Security\User\Result\UserAuthenticationResult;

class UserAuthenticator implements UserAuthenticatorInterface
{

    #[\Override] public function authenticate(RegistrationInterface $registration, string $loginHint): UserAuthenticationResultInterface
    {
        $state = \Request::get('state');
        if (!$state) {
            return new UserAuthenticationResult(false, null);
        }
        $state_key = sprintf('lti1.3_state_%s', $state);
        $ids = explode('_', $_SESSION[$state_key]);

        $user = \User::find($ids[1]);

        $identity = null;
        if ($user instanceof \User) {
            $identity = new Identity($user);
        }

        return new UserAuthenticationResult($user instanceof \User, $identity);
    }
}

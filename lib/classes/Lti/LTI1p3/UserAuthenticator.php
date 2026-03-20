<?php
namespace Studip\Lti\LTI1p3;

use User;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Security\User\Result\UserAuthenticationResult;
use OAT\Library\Lti1p3Core\Security\User\Result\UserAuthenticationResultInterface;
use OAT\Library\Lti1p3Core\Security\User\UserAuthenticatorInterface;

final class UserAuthenticator implements UserAuthenticatorInterface
{
    public function authenticate(RegistrationInterface $registration, string $loginHint): UserAuthenticationResultInterface
    {
        $user = User::find($loginHint);

        return new UserAuthenticationResult(
            $user !== null,
            new UserIdentity($user, $registration)
        );
    }
}

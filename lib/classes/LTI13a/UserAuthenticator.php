<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Security\User\UserAuthenticatorInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Security\User\Result\UserAuthenticationResultInterface;
use OAT\Library\Lti1p3Core\Security\User\Result\UserAuthenticationResult;
use Psr\Log\LoggerInterface;

class UserAuthenticator implements UserAuthenticatorInterface
{
    protected $logger = null;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[\Override]
    public function authenticate(RegistrationInterface $registration, string $loginHint): UserAuthenticationResultInterface
    {
        $user = \User::find($loginHint);

        $identity = null;
        $tool = null;
        if ($registration instanceof Registration) {
            $tool = $registration->getLtiTool();
        } else {
            $tool = \LtiTool::find($registration->getIdentifier());
        }
        if ($user && $tool) {
            $identity = new Identity($user, $tool);
        }

        return new UserAuthenticationResult($user instanceof \User, $identity);
    }
}

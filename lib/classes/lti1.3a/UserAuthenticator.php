<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Security\User\UserAuthenticatorInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Security\User\Result\UserAuthenticationResultInterface;
use OAT\Library\Lti1p3Core\Security\User\Result\UserAuthenticationResult;

class UserAuthenticator implements UserAuthenticatorInterface
{
    protected $logger = null;

    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[\Override] public function authenticate(RegistrationInterface $registration, string $loginHint): UserAuthenticationResultInterface
    {
        $user = \User::find($loginHint);

        $identity = null;
        $deployment = null;
        if ($registration instanceof \Studip\LTI13a\Registration) {
            $deployment = $registration->getLtiDeployment();
        } else {
            $deployment = \LtiDeployment::find($registration->getIdentifier());
        }
        if ($user instanceof \User && $deployment instanceof \LtiDeployment) {
            $identity = new Identity($user, $deployment);
        }
        if ($this->logger) {
            //$this->logger->debug($user instanceof \User);
            //$this->logger->debug($loginHint);
            $this->logger->debug(var_export($identity->normalize(), true));
        }

        return new UserAuthenticationResult($user instanceof \User, $identity);
    }
}

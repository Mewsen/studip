<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;

class RegistrationManager implements RegistrationRepositoryInterface
{
    #[\Override] public function find(string $identifier): ?RegistrationInterface
    {
        $lti_link = \LtiDeployment::find($identifier);
        if (!$lti_link) {
            return null;
        }
        return new Registration($lti_link);
    }

    /**
     * @inheritDoc
     */
    #[\Override] public function findAll(): array
    {
        $lti_links = \LtiDeployment::findBySQL('TRUE');
        $registrations = [];
        foreach ($lti_links as $lti_link) {
            $registrations[] = new Registration($lti_link);
        }
        return $registrations;
    }

    #[\Override] public function findByClientId(string $clientId): ?RegistrationInterface
    {
        $splitted_id = explode($clientId);
        if (count($splitted_id) < 2) {
            //Array too short.
            return null;
        }
        //Array index 0: STUDIP_INSTALLATION_ID
        //Array index 1: LTI link ID
        if ($splitted_id[1]) {
            $lti_link = \LtiDeployment::find($splitted_id[1]);
            if ($lti_link) {
                return new Registration($lti_link);
            }
        }
        return null;
    }

    #[\Override] public function findByPlatformIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        // TODO: Implement findByPlatformIssuer() method.
        die('RegistrationManager::findByPlatformIssuer: TODO');
        return null;
    }

    #[\Override] public function findByToolIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        // TODO: Implement findByToolIssuer() method.
        die('RegistrationManager::findByToolIssuer: TODO');
        return null;
    }
}

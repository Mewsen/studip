<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;

class RegistrationManager implements RegistrationRepositoryInterface
{
    #[\Override]
    public function find(string $identifier): ?RegistrationInterface
    {
        //The identifier is the ID of a tool.
        $tool = \LtiTool::find($identifier);
        if (!$tool) {
            return null;
        }
        return new Registration($tool);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function findAll(): array
    {
        $tools = \LtiTool::findBySQL('TRUE');
        $registrations = [];
        foreach ($tools as $tool) {
            $registrations[] = new Registration($tool);
        }
        return $registrations;
    }

    #[\Override]
    public function findByClientId(string $clientId): ?RegistrationInterface
    {
        //Find a registration by its client-ID. The client-ID is equivalent to the tool-ID in Stud.IP.
        if (!$clientId) {
            //Nothing to search for.
            return null;
        }
        $tool = \LtiTool::find($clientId);
        if ($tool) {
            return new Registration($tool);
        }
        return null;
    }

    #[\Override]
    public function findByPlatformIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        //Only handle requests for registrations of this Stud.IP:
        if ($issuer !== \Config::get()->STUDIP_INSTALLATION_ID) {
            //Invalid issuer.
            return null;
        }
        return $this->findByClientId($clientId);
    }

    #[\Override]
    public function findByToolIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        //Tool registrations are not supported at this moment.
        return null;
    }
}

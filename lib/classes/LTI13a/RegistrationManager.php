<?php
namespace Studip\LTI13a;

use Lti\Deployment;
use Lti\Registration;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;

class RegistrationManager implements RegistrationRepositoryInterface
{
    public function find(string $identifier): ?RegistrationInterface
    {
        return Registration::find($identifier)?->toLti1p3Registration();
    }

    public function findAll(): array
    {
        $registrations = Registration::all();
        return array_map(fn($r) => $r->toLti1p3Registration(), $registrations);
    }

    public function findByClientId(string $clientId): ?RegistrationInterface
    {
        $deployment = Deployment::findOneBySQL("`client_id` = ?", [$clientId]);

        if (!$deployment) {
            return null;
        }

        return Registration::findByDeploymentId($deployment->id)?->toLti1p3Registration($deployment);
    }

    public function findByPlatformIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        $deployment = Deployment::findOneBySQL("`client_id` = ?", [$clientId]);

        if (!$deployment) {
            return null;
        }

        return Registration::findByDeploymentIdAndIssuer($deployment->id, $issuer)?->toLti1p3Registration($deployment);
    }

    public function findByToolIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        $deployment = Deployment::findOneBySQL("`client_id` = ?", [$clientId]);

        if (!$deployment) {
            return null;
        }

        return Registration::findByDeploymentIdAndIssuer($deployment->id, $issuer)?->toLti1p3Registration($deployment);
    }
}

<?php
namespace Studip\LTI13a;

use Lti\Deployment;
use Lti\Registration;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use Studip\Lti\Enum\PublicationStatus;
use Studip\Lti\Enum\RegistrationStatus;

final class RegistrationManager implements RegistrationRepositoryInterface
{
    public function find(string $identifier): ?RegistrationInterface
    {
        return Registration::findOneBySQL(
            "id = :id AND status = :status",
            [
                'id' => $identifier,
                'status' => RegistrationStatus::Active->value
            ]
        )?->toLti1p3Registration();
    }

    public function findAll(): array
    {
        $registrations = Registration::all();
        return array_map(fn($r) => $r->toLti1p3Registration(), $registrations);
    }

    public function findByClientId(string $clientId): ?RegistrationInterface
    {
        $deployment = Deployment::findOneBySQL("`client_id` = ?", [$clientId]);
        $registration = $deployment->registration;
        if (!$registration || $registration->status !== RegistrationStatus::Active->value) {
            return null;
        }

        return $registration->toLti1p3Registration($deployment);
    }

    public function findByPlatformIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        $deployment = Deployment::findOneBySQL("`client_id` = ?", [$clientId]);
        $registration = $deployment->registration;
        if (
            !$registration
            || $registration->status !== RegistrationStatus::Active->value
            || $registration->getConfigValues()['issuer'] !== $issuer
        ) {
            return null;
        }

        return $registration->toLti1p3Registration($deployment);
    }

    public function findByToolIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        $deployment = Deployment::findOneBySQL("`client_id` = ?", [$clientId]);
        $registration = $deployment->registration;
        if (
            !$registration
            || $registration->status !== RegistrationStatus::Active->value
            || $registration->getConfigValues()['audience'] !== $issuer
        ) {
            return null;
        }

        return $registration->toLti1p3Registration($deployment);
    }
}

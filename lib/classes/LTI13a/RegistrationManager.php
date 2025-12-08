<?php
namespace Studip\LTI13a;

use Lti\Registration;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;

class RegistrationManager implements RegistrationRepositoryInterface
{
    public function find(string $identifier): ?RegistrationInterface
    {
        return Registration::find($identifier)?->getLti1p3Registration();
    }

    public function findAll(): array
    {
        $registrations = Registration::findBySQL("TRUE");
        return array_map(fn($r) => $r->getLti1p3Registration(), $registrations);
    }

    public function findByClientId(string $clientId): ?RegistrationInterface
    {
        return Registration::findOneBySQL("`client_id` = ?", [$clientId])?->getLti1p3Registration();
    }

    public function findByPlatformIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        return Registration::findOneBySQL(
            "JOIN `lti_registration_configs` configs ON (`lti_registrations`.`id` = `configs`.`registration_id`)
                WHERE `lti_registrations`.`role` = 'platform'
                AND `configs`.`name` = 'issuer'
                AND  `configs`.`value` = :issuer
                AND `lti_registrations`.`client_id` = :client_id",
            [
                'issuer' => $issuer,
                'client_id' => $clientId
            ]
        )?->getLti1p3Registration();
    }

    public function findByToolIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        // TODO:: check select query
        return Registration::findOneBySQL("`client_id` = ?", [$clientId])?->getLti1p3Registration();
    }
}

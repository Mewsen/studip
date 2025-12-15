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
        $registrations = Registration::findBySQL("TRUE");
        return array_map(fn($r) => $r->toLti1p3Registration(), $registrations);
    }

    public function findByClientId(string $clientId): ?RegistrationInterface
    {
        $deployment = Deployment::findOneBySQL("`client_id` = ?", [$clientId]);

        return Registration::findOneBySQL(
            "
                JOIN `lti_deployments` deployments ON (`lti_registrations`.`id` = `deployments`.`registration_id`)
                WHERE `deployments`.`id` = :deployment_id
            ",
            [
                'deployment_id' => $deployment?->id
            ]
        )?->toLti1p3Registration($deployment);
    }

    public function findByPlatformIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        $deployment = Deployment::findOneBySQL("`client_id` = ?", [$clientId]);

        return Registration::findOneBySQL(
            "JOIN `lti_registration_configs` configs ON (`lti_registrations`.`id` = `configs`.`registration_id`)
                JOIN `lti_deployments` deployments ON (`lti_registrations`.`id` = `deployments`.`registration_id`)
                WHERE `lti_registrations`.`role` = 'platform'
                AND `configs`.`name` = 'issuer'
                AND  `configs`.`value` = :issuer
                AND `deployments`.`id` = :deployment_id",
            [
                'issuer' => $issuer,
                'deployment_id' => $deployment?->id
            ]
        )?->toLti1p3Registration($deployment);
    }

    public function findByToolIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        dd('findByToolIssuer', $issuer, $clientId);
        $deployment = Deployment::findOneBySQL("`client_id` = ?", [$clientId]);

        // TODO:: check select query
        return Registration::findOneBySQL(
            "
                JOIN `lti_deployments` deployments ON (`lti_registrations`.`id` = `deployments`.`registration_id`)
                WHERE `deployments`.`id` = :deployment_id
            ",
            [
                'deployment_id' => $deployment?->id
            ]
        )?->toLti1p3Registration($deployment);
    }
}

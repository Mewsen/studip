<?php

namespace Studip\SAML;

use Config;

class SetupInformation
{
    private const CONFIG_KEY = 'SAML_CONFIG';

    public function getConfiguration(): array
    {
        $config = Config::get();
        $samlConfig = json_decode($config->{self::CONFIG_KEY} ?? '{}', true);

        return [
            'entityId' => $samlConfig['entityId'] ?? '',
            'assertionConsumerService' => $samlConfig['assertionConsumerService'] ?? '',
            'singleLogoutService' => $samlConfig['singleLogoutService'] ?? '',
            'nameIdFormat' => $samlConfig['nameIdFormat'] ?? '',
            'x509cert' => $samlConfig['x509cert'] ?? '',
            'privateKey' => $samlConfig['privateKey'] ?? '',
            'security' => [
                'authnRequestsSigned' => $samlConfig['security']['authnRequestsSigned'] ?? false,
                'wantMessagesSigned' => $samlConfig['security']['wantMessagesSigned'] ?? false,
                'wantAssertionsSigned' => $samlConfig['security']['wantAssertionsSigned'] ?? false,
            ],
        ];
    }

    public function updateConfiguration(array $config): void
    {
        $existingConfig = $this->getConfiguration();
        $updatedConfig = array_merge($existingConfig, $config);

        $configInstance = Config::get();
        $configInstance->{self::CONFIG_KEY} = json_encode($updatedConfig);
        $configInstance->store(self::CONFIG_KEY, $configInstance->{self::CONFIG_KEY});
    }
}
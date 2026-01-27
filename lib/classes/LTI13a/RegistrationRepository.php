<?php
namespace Studip\LTI13a;

use Lti\Deployment;
use Lti\Registration;
use OAT\Library\Lti1p3Core\Platform\Platform;
use OAT\Library\Lti1p3Core\Tool\Tool;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Tool\ToolInterface;
use OAT\Library\Lti1p3Core\Platform\PlatformInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;

final class RegistrationRepository implements RegistrationInterface
{
    public function __construct(
        protected Registration $registration,
        protected ?Deployment $deployment = null
    ) {
        $this->deployment ??= $registration->getDefaultDeployment();
    }

    public function getIdentifier(): string
    {
        return $this->registration->id;
    }

    public function getClientId(): string
    {
        return $this->deployment->client_id;
    }

    public function getPlatform(): PlatformInterface
    {
        $registrationConfigs = $this->registration->getConfigValues();

        if ($this->registration->role === 'platform') {
            return new Platform(
                $this->deployment->client_id,
                $this->registration->name,
                $registrationConfigs['issuer'],
                $registrationConfigs['auth_login_url'],
                $registrationConfigs['token_url']
            );
        }

        return PlatformManager::getPlatformConfiguration();
    }

    public function getTool(): ToolInterface
    {
        $registrationConfigs = $this->registration->getConfigValues();

        if ($this->registration->role === 'tool') {
            return new Tool(
                $this->deployment->client_id,
                $this->registration->name,
                $registrationConfigs['launch_url'],
                $registrationConfigs['auth_init_url'],
                $registrationConfigs['launch_url'],
                $registrationConfigs['deep_linking_url']
            );
        }

        return ToolManager::getToolConfiguration();
    }

    public function getDeploymentIds(): array
    {
        return array_map(fn($d) => $d->deployment_key, $this->registration->deployments ?? []);
    }

    public function hasDeploymentId(string $deploymentId): bool
    {
        foreach ($this->registration->deployments ?? [] as $d) {
            if ($d->deployment_key === $deploymentId) {
                return true;
            }
        }

        return false;
    }

    public function getDefaultDeploymentId(): ?string
    {
        return $this->deployment->deployment_key;
    }

    public function getPlatformKeyChain(): ?KeyChainInterface
    {
        if ($this->registration->role === 'platform') {
            if ($this->registration->config_values['jwks_url']) {
                return null;
            }

            return $this->registration->getKeyring()?->toKeyChain();
        }

        return PlatformManager::getKeyring()->toKeyChain();
    }

    public function getToolKeyChain(): ?KeyChainInterface
    {
        if ($this->registration->role === 'tool') {
            if ($this->registration->config_values['jwks_url']) {
                return null;
            }

            return $this->registration->getKeyring()?->toKeyChain();
        }

        return ToolManager::getKeyring()?->toKeyChain();
    }

    public function getPlatformJwksUrl(): ?string
    {
        return $this->registration->config_values['jwks_url'] ?? PlatformManager::getJwksUrl();
    }

    public function getToolJwksUrl(): ?string
    {
        return $this->registration->config_values['jwks_url'] ?? ToolManager::getJwksUrl();
    }
}

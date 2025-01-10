<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Tool\ToolInterface;
use OAT\Library\Lti1p3Core\Platform\PlatformInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;

class Registration implements RegistrationInterface
{
    public function __construct(
        protected ?\LtiTool $tool
    ) {
    }

    public function setLtiTool(\LtiTool $tool)
    {
        $this->tool = $tool;
    }

    public function getLtiTool() : ?\LtiTool
    {
        return $this->tool;
    }

    #[\Override]
    public function getIdentifier(): string
    {
        if (!$this->tool) {
            return '';
        }
        return $this->tool->id;
    }

    #[\Override]
    public function getClientId(): string
    {
        return $this->tool->id ?? '';
    }

    #[\Override]
    public function getPlatform(): PlatformInterface
    {
        return PlatformManager::getPlatformConfiguration();
    }

    #[\Override]
    public function getTool(): ToolInterface
    {
        if (!$this->tool) {
            throw new \Studip\LTIException(
                'No LTI tool link present.',
                \Studip\LTIException::REGISTRATION_NOT_LINKED_TO_TOOL
            );
        }
        return $this->tool->getToolData();
    }

    #[\Override]
    public function getDeploymentIds(): array
    {
        if (!$this->tool) {
            return [];
        }
        return \DBManager::get()->fetchFirst("SELECT `id` FROM `lti_deployments` WHERE `tool_id` = ?", [$this->tool->id]);
    }

    #[\Override]
    public function hasDeploymentId(string $deploymentId): bool
    {
        if (!$this->tool) {
            return false;
        }
        return \LtiDeployment::countBySql(
            "`tool_id` = :tool_id AND `id` = :deployment_id",
            ['tool_id' => $this->tool->id, 'deployment_id' => $deploymentId]
        ) > 0;
    }

    #[\Override]
    public function getDefaultDeploymentId(): ?string
    {
        //There is no default deployment-ID in Stud.IP:
        return null;
    }

    #[\Override]
    public function getPlatformKeyChain(): ?KeyChainInterface
    {
        $platform_keyring = PlatformManager::getPlatformKeyring();
        if (!$platform_keyring) {
            $platform_keyring = PlatformManager::generatePlatformKeyring();
        }
        return $platform_keyring->toKeyChain();
    }

    #[\Override]
    public function getToolKeyChain(): ?KeyChainInterface
    {
        if (!$this->tool) {
            return null;
        }
        $keyring = $this->tool->getKeyring();
        if (!$keyring) {
            $keyring = $this->tool->getKeyring(true);
        }
        return $keyring->toKeyChain();
    }

    #[\Override]
    public function getPlatformJwksUrl(): ?string
    {
        return PlatformManager::getJwksUrl();
    }

    #[\Override]
    public function getToolJwksUrl(): ?string
    {
        return $this->tool->jwks_url ?? null;
    }
}

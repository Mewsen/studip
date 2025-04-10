<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Tool\ToolInterface;
use OAT\Library\Lti1p3Core\Platform\PlatformInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;

class Registration implements RegistrationInterface
{
    /**
     * @var \LtiTool|null The LTI tool for the registration instance.
     */
    protected ?\LtiTool $tool = null;

    /**
     * @var \LtiPlatform|null The LTI platform for the registration instance.
     */
    protected ?\LtiPlatform $platform = null;

    public function __construct(
        \LtiTool|\LtiPlatform|null $item,
        protected ?\LtiResourceLink $link = null
    ) {
        if ($item instanceof \LtiTool) {
            $this->tool = $item;
        }
        if ($item instanceof \LtiPlatform) {
            $this->platform = $item;
        }
    }

    public function setLtiTool(\LtiTool $tool)
    {
        $this->tool = $tool;
    }

    public function getLtiTool() : ?\LtiTool
    {
        return $this->tool;
    }

    public function setLtiPlatform(\LtiPlatform $platform)
    {
        $this->platform = $platform;
    }

    public function getLtiPlatform() : ?\LtiPlatform
    {
        return $this->platform;
    }

    public function setLtiResourceLink(\LtiResourceLink $link)
    {
        $this->link = $link;
    }

    public function getLtiResourceLink() : ?\LtiResourceLink
    {
        return $this->link;
    }

    #[\Override]
    public function getIdentifier(): string
    {
        if ($this->tool) {
            return $this->tool->id;
        }

        if ($this->platform) {
            return $this->platform->id;
        }

        if ($this->link) {
            return $this->tool->id . '_' . $this->link->id;
        }

        return '';
    }

    #[\Override]
    public function getClientId(): string
    {
        return $this->getIdentifier();
    }

    #[\Override]
    public function getPlatform(): PlatformInterface
    {
        return PlatformManager::getPlatformConfiguration();

//        if ($this->platform) {
//            return $this->platform->getPlatformData();
//        } elseif ($this->tool) {
//            //Use the global platform configuration for Stud.IP as LTI platform.
//            return PlatformManager::getPlatformConfiguration();
//        }
//        //If no platform or tool is present, the registration is not linked to a platform.
//        throw new \Studip\LTIException(
//            'No LTI platform present.',
//            \Studip\LTIException::REGISTRATION_NOT_LINKED_TO_PLATFORM
//        );
    }

    #[\Override]
    public function getTool(): ToolInterface
    {
        if ($this->tool) {
            return $this->tool->getToolData();
        } elseif ($this->platform) {
            //Return the global tool:
            return \LtiTool::getGlobalTool();
        }
        //If no tool or platform is present, the registration is not linked to a tool.
        throw new \Studip\LTIException(
            'No LTI tool link present.',
            \Studip\LTIException::REGISTRATION_NOT_LINKED_TO_TOOL
        );
    }

    #[\Override]
    public function getDeploymentIds(): array
    {
        if ($this->tool) {
            return \DBManager::get()->fetchFirst("SELECT `id` FROM `lti_deployments` WHERE `tool_id` = ?", [$this->tool->id]);
        }

        if ($this->link) {
            return [$this->link->deployment_id];
        }

        return [];
    }

    #[\Override]
    public function hasDeploymentId(string $deploymentId): bool
    {
        if ($this->tool) {
            return \LtiDeployment::countBySql(
                    "`tool_id` = :tool_id AND `id` = :deployment_id",
                    ['tool_id' => $this->tool->id, 'deployment_id' => $deploymentId]
                ) > 0;
        }

        if ($this->link) {
            return $this->link->deployment_id == $deploymentId;
        }

        return false;
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
        if ($this->platform) {
            //TODO: return the platform keyring.
        } elseif ($this->tool) {
            $platform_keyring = PlatformManager::getPlatformKeyring();
            if (!$platform_keyring) {
                $platform_keyring = PlatformManager::generatePlatformKeyring();
            }
            return $platform_keyring->toKeyChain();
        }

        return null;
    }

    #[\Override]
    public function getToolKeyChain(): ?KeyChainInterface
    {
        if ($this->tool) {
            if ($this->tool->jwks_url) {
                return null;
            }
            $keyring = $this->tool->getKeyring();
            if (!$keyring) {
                throw new LtiException('Failed to load public key for tool ' . $this->tool->id);
            }
            return $keyring->toKeyChain();
        } elseif ($this->platform) {
            //Return the global tool keychain.
            $keyring = \LtiTool::getGlobalToolKeyring(true);
            if ($keyring) {
                return $keyring->toKeyChain();
            }
        }

        return null;
    }

    #[\Override]
    public function getPlatformJwksUrl(): ?string
    {
        if ($this->platform) {
            return $this->platform->jwks_url ?? null;
        } else {
            return PlatformManager::getJwksUrl();
        }
    }

    #[\Override]
    public function getToolJwksUrl(): ?string
    {
        if ($this->tool) {
            return $this->tool->jwks_url ?? null;
        } else {
            //Return the global JWKS URL for Stud.IP as LTI tool.
            return \LtiTool::getGlobalJwksUrl();
        }
    }
}

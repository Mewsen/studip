<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Tool\ToolInterface;
use OAT\Library\Lti1p3Core\Platform\PlatformInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;

class Registration implements RegistrationInterface
{
    protected ?\LtiData $lti_link = null;

    public function __construct(?\LtiData $lti_link)
    {
        $this->lti_link = $lti_link;
    }

    public function setLtiLink(\LtiData $lti_link)
    {
        $this->lti_link = $lti_link;
    }

    public function getLtiLink() : ?\LtiData
    {
        return $this->lti_link;
    }

    #[\Override] public function getIdentifier(): string
    {
        if (!$this->lti_link) {
            return '';
        }
        return $this->lti_link->id;
    }

    #[\Override] public function getClientId(): string
    {
        if (!$this->lti_link) {
            return '';
        }
        if (!$this->lti_link->course_id) {
            return '';
        }
        return \Config::get()->STUDIP_INSTALLATION_ID . '_' . $this->lti_link->id;
    }

    #[\Override] public function getPlatform(): PlatformInterface
    {
        return \Studip\LTI13a\PlatformManager::getPlatformConfiguration();
    }

    #[\Override] public function getTool(): ToolInterface
    {
        if (!$this->lti_link || !$this->lti_link->tool) {
            throw new \StudipException('No LTI tool link present.');
        }
        return $this->lti_link->tool->getToolData();
    }

    #[\Override] public function getDeploymentIds(): array
    {
        if (!$this->lti_link) {
            return [];
        }
        return $this->lti_link->getDeploymentIds();
    }

    #[\Override] public function hasDeploymentId(string $deploymentId): bool
    {
        if (!$this->lti_link) {
            return false;
        }
        return $this->lti_link->hasDeploymentId($deploymentId);
    }

    #[\Override] public function getDefaultDeploymentId(): ?string
    {
        //TODO
        return null;
    }

    #[\Override] public function getPlatformKeyChain(): ?KeyChainInterface
    {
        $platform_keyring = \Studip\LTI13a\PlatformManager::getPlatformKeyring();
        if (!$platform_keyring) {
            $platform_keyring = \Studip\LTI13a\PlatformManager::generatePlatformKeyring();
        }
        return $platform_keyring->toKeyChain();
    }

    #[\Override] public function getToolKeyChain(): ?KeyChainInterface
    {
        if (!$this->lti_link || !$this->lti_link->tool) {
            return null;
        }
        $keyring = $this->lti_link->tool->getKeyring();
        if (!$keyring) {
            $keyring = $this->lti_link->tool->getKeyring(true);
        }
        return $keyring;
    }

    #[\Override] public function getPlatformJwksUrl(): ?string
    {
        //TODO
        return null;
    }

    #[\Override] public function getToolJwksUrl(): ?string
    {
        //TODO
        return null;
    }
}

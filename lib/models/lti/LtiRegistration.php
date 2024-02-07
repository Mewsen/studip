<?php

class LtiRegistration extends SimpleORMap
    implements \OAT\Library\Lti1p3Core\Registration\RegistrationInterface
{


    public function getIdentifier(): string
    {
        return $this->id;
    }

    public function getClientId(): string
    {
        return $this->client_id;
    }

    public function getPlatform(): \OAT\Library\Lti1p3Core\Platform\PlatformInterface
    {
        return \Studip\LTI13a\PlatformManager::getPlatformConfiguration();
    }

    public function getTool(): \OAT\Library\Lti1p3Core\Tool\ToolInterface
    {
        return new \LtiTool($this->tool_id);
    }
    public function getDeploymentIds(): array
    {
        $db = DBManager::get();
        $stmt = $db->prepare(
            "SELECT `deployment_id`
            FROM `lti_deployments`
            WHERE `tool_id` = :tool_id"
        );
        $stmt->execute(['tool_id' => $this->tool_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function hasDeploymentId(string $deploymentId): bool
    {
        return count($this->deployments) > 1;
    }

    public function getDefaultDeploymentId(): ?string
    {
        return md5($this->id . $this->client_id . random_bytes(64));
    }

    public function getPlatformKeyChain(): ?\OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface
    {
        $keyring = \Studip\LTI13a\PlatformManager::getPlatformKeyring();
        if ($keyring) {
            return $keyring->toKeyChain();
        }
        return null;
    }

    public function getToolKeyChain(): ?\OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface
    {
        $tool = LtiTool::find($this->tool_id);
        if ($tool) {
            $keyring = $tool->getKeyring();
            if ($keyring) {
                return $keyring->toKeyChain();
            }
        }
        return null;
    }

    public function getPlatformJwksUrl(): ?string
    {
        // TODO: Implement getPlatformJwksUrl() method.
    }

    public function getToolJwksUrl(): ?string
    {
        // TODO: Implement getToolJwksUrl() method.
    }
}

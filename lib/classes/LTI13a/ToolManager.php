<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Tool\Tool;
use OAT\Library\Lti1p3Core\Registration\Registration;

class ToolManager
{
    public static function getToolConfiguration(string $tool_id) : ?Tool
    {
        $tool = \LtiTool::find($tool_id);
        if (!$tool) {
            return null;
        }

        return new Tool(
            $tool->getIdentifier(),
            $tool->getName(),
            $tool->getAudience(),
            $tool->getOidcInitiationUrl(),
            $tool->getLaunchUrl(),
            $tool->getDeepLinkingUrl()
        );
    }

    public function getToolRegistration(string $tool_id) :?Registration
    {
        $platform_config = PlatformManager::getPlatformConfiguration();
        $tool_config = self::getToolConfiguration($tool_id);
        if (!$platform_config || !$tool_config) {
            return null;
        }

        $platform_keyring = \Keyring::findOneBySQL("`range_type` = 'global' AND `range_id` = 'lti13a_platform'");
        $tool_keyring = \Keyring::findOneBySQL(
            "`range_type` = 'lti-tool' AND 'range_id = :tool_id",
            ['tool_id' => $tool_id]
        );

        return new Registration(
            sprintf(
                '%s_%s',
                $platform_config->getIdentifier(),
                $tool_config->getIdentifier()
            ),
            $GLOBALS['user']->id,
            $platform_config,
            $tool_config,
            [], //TODO
            $platform_keyring ? $platform_keyring->toKeyChain() : null,
            $tool_keyring ? $tool_keyring->toKeyChain() : null
        );
    }
}

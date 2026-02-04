<?php
namespace Studip\Lti\LTI1p3;

use Config;
use Keyring;
use URLHelper;
use OAT\Library\Lti1p3Core\Tool\Tool;
use OAT\Library\Lti1p3Core\Tool\ToolInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyInterface;

final class ToolManager
{
    public static function getToolConfiguration(): ToolInterface
    {
        $config = Config::get();
        return new Tool(
            $config->STUDIP_INSTALLATION_ID,
            $config->UNI_NAME_CLEAN,
            $GLOBALS['ABSOLUTE_URI_STUDIP'],
            URLHelper::getURL('dispatch.php/enroll/lti/auth_init', null, true),
            URLHelper::getURL('dispatch.php/enroll/lti/launch', null, true),
            URLHelper::getURL('dispatch.php/enroll/lti/launch_deeplink', null, true)
        );
    }

    public static function getKeyring(): ?Keyring
    {
        $keyring = Keyring::findOneBySQL("`range_type` = 'global' AND `range_id` = 'lti13a_tool'");
        if (!$keyring) {
            $keyring = Keyring::generate('lti13a_tool', 'global');
        }

        return $keyring;
    }

    public static function getPrivateKey(): KeyInterface
    {
        return self::getKeyring()->toKeyChain()->getPrivateKey();
    }

    public static function getPublicKey(): KeyInterface
    {
        return self::getKeyring()->toKeyChain()->getPublicKey();
    }

    public static function getJwksUrl(): string
    {
        return URLHelper::getURL('dispatch.php/enroll/lti/jwks');
    }
}

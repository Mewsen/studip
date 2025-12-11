<?php
namespace Studip\LTI13a;

use Config;
use Keyring;
use URLHelper;
use OAT\Library\Lti1p3Core\Tool\Tool;
use OAT\Library\Lti1p3Core\Tool\ToolInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyInterface;

class ToolManager
{
    public static function getToolConfiguration(): ToolInterface
    {
        $config = Config::get();
        return new Tool(
            $config->STUDIP_INSTALLATION_ID,
            $config->UNI_NAME_CLEAN,
            $GLOBALS['ABSOLUTE_URI_STUDIP'],
            URLHelper::getURL('dispatch.php/enrol/lti/auth_init', null, true),
            URLHelper::getURL('dispatch.php/enrol/lti/lunch', null, true),
            URLHelper::getURL('dispatch.php/enrol/lti/launch_deeplink', null, true)
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
        return static::getKeyring()->toKeyChain()->getPrivateKey();
    }

    public static function getPublicKey(): KeyInterface
    {
        return static::getKeyring()->toKeyChain()->getPublicKey();
    }

    public static function getJwksUrl(): string
    {
        return URLHelper::getURL('dispatch.php/enrol/lti/jwks');
    }
}

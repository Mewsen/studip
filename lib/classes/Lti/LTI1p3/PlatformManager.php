<?php
namespace Studip\Lti\LTI1p3;

use Config;
use Keyring;
use URLHelper;
use OAT\Library\Lti1p3Core\Platform\Platform;
use OAT\Library\Lti1p3Core\Security\Key\KeyInterface;
use OAT\Library\Lti1p3Core\Platform\PlatformInterface;
use OAT\Library\Lti1p3DeepLinking\Settings\DeepLinkingSettings;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLinkInterface;

final class PlatformManager
{
    public static function getPlatformConfiguration(): PlatformInterface
    {
        $config = Config::get();

        return new Platform(
            $config->STUDIP_INSTALLATION_ID,
            $config->UNI_NAME_CLEAN,
            rtrim($GLOBALS['ABSOLUTE_URI_STUDIP'], '/'),
            URLHelper::getURL('dispatch.php/lti/1p3/login', null, true),
            URLHelper::getURL('dispatch.php/lti/1p3/token', null, true)
        );
    }


    public static function getDeepLinkingConfiguration(): DeepLinkingSettings
    {
        return new DeepLinkingSettings(
            URLHelper::getURL('dispatch.php/lti/1p3/index/store_contents'),
            [LtiResourceLinkInterface::TYPE],
            ['window', 'iframe', 'embed'],
            'text/html',
            true,
            false,
            Config::get()->UNI_NAME_CLEAN
        );
    }

    public static function getKeyring(): ?Keyring
    {
        $keyring = Keyring::findOneBySQL("`range_type` = 'global' AND `range_id` = 'lti13a_platform'");
        if (!$keyring) {
            $keyring = Keyring::generate('lti13a_platform', 'global');
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

    public static function getDeepLinkingReturnUrl(string $linkId, string $courseId = ''): string
    {
        $params = [];

        if ($courseId) {
            $params['cid'] = $courseId;
        }
        return URLHelper::getURL('dispatch.php/lti/1p3/index/store_content/' . $linkId, $params, true);
    }

    public static function getJwksUrl(): string
    {
        return URLHelper::getURL('dispatch.php/lti/1p3/jwks', null, true);
    }
}

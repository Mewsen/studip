<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Platform\Platform;
use OAT\Library\Lti1p3DeepLinking\Settings\DeepLinkingSettings;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLinkInterface;

class PlatformManager
{
    /**
     * Generates an object containing the configuration to use this Stud.IP
     * as LTI 1.3A platform.
     *
     * @return Platform The platform configuration.
     */
    public static function getPlatformConfiguration() : Platform
    {
        $c = \Config::get();

        return new Platform(
            $c->STUDIP_INSTALLATION_ID,
            $c->UNI_NAME_CLEAN,
            $GLOBALS['ABSOLUTE_URI_STUDIP'],
            \URLHelper::getURL('dispatch.php/lti/auth/oidc_init', null, true),
            \URLHelper::getURL('dispatch.php/lti/auth/oauth2_token', null, true)
        );
    }

    /**
     * Generates an object containing the settings for using this Stud.IP
     * as a platform that connects to an LTI tool via Deep Linking.
     *
     * @param string $tool_id An optional LTI tool ID that is used to construct
     *     the platform return URL.
     *
     * @return DeepLinkingSettings The settings for deep linking.
     */
    public static function getDeepLinkingConfiguration(string $tool_id = '') : DeepLinkingSettings
    {
        $c = \Config::get();

        return new DeepLinkingSettings(
            self::getDeepLinkingReturnUrl($tool_id),
            [
                LtiResourceLinkInterface::TYPE
            ],
            ['window', 'iframe'],
            'text/html',
            true,
            false,
            $c->UNI_NAME_CLEAN,
            ''
        );
    }

    /**
     * Returns the keyring for the platform.
     *
     * @return \Keyring|null The keyring for the platform or null if no such keyring exists.
     */
    public static function getPlatformKeyring() : ?\Keyring
    {
        return \Keyring::findOneBySQL("`range_type` = 'global' AND `range_id` = 'lti13a_platform'");
    }

    public static function generatePlatformKeyring() : \Keyring
    {
        return \Keyring::generate('lti13a_platform', 'global');
    }

    public static function getLtiRoleClaimForStudipRole(string $role) : string
    {
        if (in_array($role, ['tutor', 'dozent', 'admin', 'root'])) {
            //Lecturer/admin
            return 'http://purl.imsglobal.org/vocab/lis/v2/membership#Mentor';
        } elseif (in_array($role, ['user', 'autor'])) {
            //Learner
            return  'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner';
        }
        //Invalid role:
        return '';
    }

    /**
     * Generates the URL for returning from the tool in an LTI deep linking process.
     *
     * @param string $tool_id The optional LTI Tool-ID to append to the URL.
     *
     * @return string The URL for returning from an LTI deep linking process.
     */
    public static function getDeepLinkingReturnUrl(string $tool_id = '') : string
    {
        return \URLHelper::getURL('dispatch.php/course/lti/save_link/' . $tool_id, null, true);
    }

    /**
     * Returns the URL from which the JSON web key set (JWKS) can be retrieved.
     *
     * @return string The JWKS URL.
     */
    public static function getJwksUrl() : string
    {
        return \URLHelper::getURL('lti/auth/jwks');
    }
}

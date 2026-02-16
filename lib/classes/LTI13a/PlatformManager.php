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
     * @param string $link_id The Stud.IP LTI Resource Link ID that is used to construct
     *     the platform return URL.
     *
     * @param string $course_id An optional Stud.IP course for which to get
     *     the deep linking configuration.
     *
     * @return DeepLinkingSettings The settings for deep linking.
     */
    public static function getDeepLinkingConfiguration(string $link_id, string $course_id = '') : DeepLinkingSettings
    {
        $c = \Config::get();

        return new DeepLinkingSettings(
            self::getDeepLinkingReturnUrl($link_id, $course_id),
            [LtiResourceLinkInterface::TYPE],
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

    /**
     * Generates the URL for returning from the tool in an LTI deep linking process.
     *
     * @param string $link_id The Stud.IP LTI Resource Link ID to append to the URL.
     *
     * @param string $course_id An optional Stud.IP course for which to generate
     *      the deep linking return URL.
     *
     * @return string The URL for returning from an LTI deep linking process.
     */
    public static function getDeepLinkingReturnUrl(string $link_id, string $course_id = '') : string
    {
        $params = ['link_id' => $link_id];
        if ($course_id) {
            $params['cid'] = $course_id;
        }
        return \URLHelper::getURL('dispatch.php/course/lti/save_link/' . $link_id, $params, true);
    }

    /**
     * Returns the URL from which the JSON web key set (JWKS) can be retrieved.
     *
     * @return string The JWKS URL.
     */
    public static function getJwksUrl() : string
    {
        return \URLHelper::getURL('dispatch.php/lti/auth/jwks');
    }
}

<?php

namespace Studip\LTI13a;

use Config;
use Keyring;
use OAT\Library\Lti1p3Core\Platform\Platform;
use OAT\Library\Lti1p3Core\Platform\PlatformInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyInterface;
use OAT\Library\Lti1p3DeepLinking\Settings\DeepLinkingSettings;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLinkInterface;
use URLHelper;

class PlatformManager
{
    /**
     * Generates an object containing the configuration to use this Stud.IP
     * as LTI 1.3A platform.
     *
     * @return PlatformInterface
     */
    public static function getPlatformConfiguration(): PlatformInterface
    {
        // TODO: Fix oidcs and token urls
        $c = Config::get();
        return new Platform(
            $c->STUDIP_INSTALLATION_ID,
            $c->UNI_NAME_CLEAN,
            $GLOBALS['ABSOLUTE_URI_STUDIP'],
            URLHelper::getURL('dispatch.php/lti/auth/login', null, true),
            URLHelper::getURL('dispatch.php/lti/auth/token', null, true)
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
    public static function getDeepLinkingConfiguration(string $link_id, string $course_id = ''): DeepLinkingSettings
    {
        $c = Config::get();

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
        return static::getKeyring()->toKeyChain()->getPrivateKey();
    }

    public static function getPublicKey(): KeyInterface
    {
        return static::getKeyring()->toKeyChain()->getPublicKey();
    }

    public static function getLtiRoleClaimForStudipRole(string $role): string
    {
        if (in_array($role, ['dozent', 'admin', 'root'])) {
            //Lecturer/admin
            return 'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor';
        } elseif ($role === 'tutor') {
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
     * @param string $link_id The Stud.IP LTI Resource Link ID to append to the URL.
     *
     * @param string $course_id An optional Stud.IP course for which to generate
     *      the deep linking return URL.
     *
     * @return string The URL for returning from an LTI deep linking process.
     */
    public static function getDeepLinkingReturnUrl(string $link_id, string $course_id = ''): string
    {
        $params = ['link_id' => $link_id];
        if ($course_id) {
            $params['cid'] = $course_id;
        }
        return \URLHelper::getURL('dispatch.php/course/lti/save_link/' . $link_id, $params, true);
    }

    public static function getJwksUrl(): string
    {
        return \URLHelper::getURL('dispatch.php/lti/auth/jwks');
    }
}

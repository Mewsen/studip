<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Platform\Platform;

class PlatformManager
{
    public static function getPlatformConfiguration() : Platform
    {
        $c = \Config::get();

        return new Platform(
            $c->STUDIP_INSTALLATION_ID,
            $c->UNI_NAME_CLEAN,
            $GLOBALS['ABSOLUTE_URI_STUDIP'],
            \URLHelper::getURL('dispatch.php/lti13a/oidc_init', null, true),
            \URLHelper::getURL('dispatch.php/lti13a/oauth2_token', null, true)
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
}

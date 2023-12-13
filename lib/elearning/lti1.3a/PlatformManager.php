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
            \URLHelper::getURL('dispatch.php/lti/platform_auth'),
            \URLHelper::getURL('dispatch.php/lti/oauth2_token')
        );
    }
}

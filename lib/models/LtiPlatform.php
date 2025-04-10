<?php
/*
 * This file is part of Stud.IP.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @copyright   2025
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

use OAT\Library\Lti1p3Core\Platform\Platform;


/**
 * The LtiPlatform class represents LTI 1.3A platforms that are using
 * parts of this Stud.IP as LTI tool.
 *
 * @property int $id database column
 * @property int $range_id database column
 * @property string $creator_id database column
 * @property string $name database column
 * @property string $url database column
 * @property string $oauth2_access_token_url database column
 * @property string $oidc_init_url database column
 * @property string $jwks_url database column
 * @property string $jwks_key_id database column
 */
class LtiPlatform extends SimpleORMap
{
    /**
     * @inheritDoc
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_platforms';

        $config['belongs_to']['course'] = [
            'class_name'  => Course::class,
            'foreign_key' => 'range_id'
        ];
        $config['belongs_to']['creator'] = [
            'class_name'  => User::class,
            'foreign_key' => 'creator_id'
        ];

        parent::configure($config);
    }

    public function getKeyring() : ?Keyring
    {
        return Keyring::findOneBySQL(
            "`range_type` = 'lti_platform'
            AND `range_id` = :platform_id",
            ['platform_id' => $this->id]
        );
    }

    public function getPlatformData() : Platform
    {
        return new Platform(
            $this->id,
            $this->name,
            $this->url,
            $this->oidc_init_url,
            $this->oauth2_access_token_url
        );
    }
}

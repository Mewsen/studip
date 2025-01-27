<?php
/**
 * LtiToolPrivacySettings.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @since       6.0
 */

/**
 * The LtiToolPrivacySettings class represents the privacy
 * settings a user made for a specific LTI tool deployment.
 * It not only stores the decision if the privacy statement of the tool
 * has been accepted, but also which data may be transferred to the
 * LTI tool.
 *
 * @property string deployment_id database column
 * @property string user_id database column
 * @property string accepted database column
 * @property string allowed_optional_fields database column
 * @property string mkdate database column
 * @property string chdate database column
 */
class LtiToolPrivacySettings extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_tool_privacy_settings';

        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id'
        ];
        $config['belongs_to']['tool'] = [
            'class_name'  => LtiTool::class,
            'foreign_key' => 'tool_id'
        ];

        parent::configure($config);
    }
}

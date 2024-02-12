<?php
/**
 * LtiTool.php - LTI consumer API for Stud.IP
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 *
 * @property int $id database column
 * @property string $name database column
 * @property string $launch_url database column
 * @property string $consumer_key database column
 * @property string $consumer_secret database column
 * @property string $custom_parameters database column
 * @property string $lti_version database column
 * @property int $allow_custom_url database column
 * @property int $deep_linking database column
 * @property int $send_lis_person database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property string $oauth_signature_method database column
 * @property SimpleORMapCollection|LtiData[] $links has_many LtiData
 */

class LtiTool extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_tool';

        $config['has_many']['links'] = [
            'class_name'        => LtiData::class,
            'assoc_foreign_key' => 'tool_id',
            'on_delete'         => 'delete'
        ];

        parent::configure($config);
    }

    /**
     * Find all global tools (tools with is_global set to 1).
     */
    public static function findAllGlobalTools()
    {
        return self::findBySQL("`is_global` = '1' ORDER BY name");
    }

    //ToolInterface implementation

    public function getToolData() : \OAT\Library\Lti1p3Core\Tool\Tool
    {
        return new \OAT\Library\Lti1p3Core\Tool\Tool(
            $this->id,
            $this->name,
            '', //TODO
            $this->launch_url, //TODO
            $this->launch_url,
            '' //TODO
        );
    }

    /**
     * Retrieves the keyring of the LTI tool or generates one, if explicitly requested.
     *
     * @param bool $generate Generates a new keyring for the tool if set to true.
     *     Defaults to false.
     *
     * @return Keyring|null The keyring for the tool or null if no such keyring exists.
     */
    public function getKeyring(bool $generate = false) : ?Keyring
    {
        $keyring = Keyring::findOneBySQL(
            "`range_type` = 'lti_tool' AND `range_id` = :tool_id",
            ['tool_id' => $this->id]
        );
        if ($generate && !$keyring) {
            $keyring = Keyring::generate($this->id, 'lti_tool');
        }
        return $keyring;
    }

    public function getLtiVersionString() : string
    {
        if ($this->lti_version === '1.3a') {
            return '1.3a';
        } elseif ($this->lti_version === '1.1') {
            return '1.0/1.1';
        } else {
            return _('unbekannt');
        }
    }
}

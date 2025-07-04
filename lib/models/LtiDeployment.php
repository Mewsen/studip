<?php
/**
 * LtiDeployment.php - A class that represents an LTI tool deployment.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @author      Moritz Strohm
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 *
 * @property int $id database column
 * @property int $tool_id database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property SimpleORMapCollection<LtiGrade> $grades has_many LtiGrade
 * @property LtiTool $tool belongs_to LtiTool
 */

class LtiDeployment extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_deployments';

        $config['belongs_to']['tool'] = [
            'class_name'  => LtiTool::class,
            'foreign_key' => 'tool_id'
        ];
        $config['has_many']['resource_links'] = [
            'class_name'        => LtiResourceLink::class,
            'assoc_foreign_key' => 'deployment_id',
            'on_delete'         => 'delete'
        ];
        $config['has_many']['grades'] = [
            'class_name'        => LtiGrade::class,
            'assoc_foreign_key' => 'link_id',
            'on_delete'         => 'delete'
        ];

        parent::configure($config);
    }

    public function getToolLtiVersion() : string
    {
        return $this->tool->lti_version ?? '';
    }


    /**
     * Get the launch_url of this entry.
     *
     * @deprecated
     */
    public function getLaunchURL()
    {
        if (empty($this->tool->allow_custom_url) && empty($this->tool->deep_linking) || empty($this->launch_url)) {
            return $this->tool->launch_url ?? '';
        }
        return $this->launch_url;
    }

    /**
     * Get the consumer_key of this entry.
     *
     * @deprecated
     */
    public function getConsumerKey()
    {
        return $this->tool->consumer_key ?? '';
    }

    /**
     * Get the consumer_secret of this entry.
     *
     * @deprecated
     */
    public function getConsumerSecret()
    {
        return $this->tool->consumer_secret ?? '';
    }

    /**
     * Get the oauth_signature_method of this entry.
     *
     * @deprecated
     */
    public function getOauthSignatureMethod()
    {
        return $this->tool->oauth_signature_method ?? 'sha1';
    }

    /**
     * Get the custom_parameters of this entry.
     *
     * @deprecated
     */
    public function getCustomParameters()
    {
        $parameters = '';
        if (!empty($this->tool->custom_parameters)) {
            $parameters .= $this->tool->custom_parameters . "\n";
        }
        $parameters .= $this->options['custom_parameters'] ?? '';
        return $parameters;
    }

    /**
     * Get the send_lis_person attribute of this entry.
     */
    public function getSendLisPerson()
    {
        return $this->tool->send_lis_person;
    }

    /**
     * Whether the LtiData instance uses its own (private) tool
     * or one of the globally defined LTI tools.
     *
     * @return bool True, if the LtiData instance uses its own tool, false otherwise.
     */
    public function hasOwnTool() : bool
    {
        return $this->tool && !$this->tool->is_global;
    }
}

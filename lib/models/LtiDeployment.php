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
 * @property int $registration_id database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property SimpleORMapCollection<LtiGrade> $grades has_many LtiGrade
 * @property \Lti\Registration $registration belongs_to Registration
 */

class LtiDeployment extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_deployments';

        $config['belongs_to']['registration'] = [
            'class_name'  => \Lti\Registration::class,
            'foreign_key' => 'registration_id'
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

    /**
     * Get the launch_url of this entry.
     *
     * @deprecated
     */
    public function getLaunchURL()
    {
        return $this->registration->config_values['launch_url'];
    }

    /**
     * Get the consumer_key of this entry.
     *
     * @deprecated
     */
    public function getConsumerKey()
    {
        return $this->registration->config_values['consumer_key'] ?? '';
    }

    /**
     * Get the consumer_secret of this entry.
     *
     * @deprecated
     */
    public function getConsumerSecret()
    {
        return $this->registration->config_values['consumer_secret'] ?? '';
    }

    /**
     * Get the oauth_signature_method of this entry.
     *
     * @deprecated
     */
    public function getOauthSignatureMethod()
    {
        return $this->registration->config_values['oauth_signature_method'] ?? 'sha1';
    }

    /**
     * Get the custom_parameters of this entry.
     *
     * @deprecated
     */
    public function getCustomParameters()
    {
        $parameters = '';
        if (!empty($this->registration->config_values['custom_parameters'])) {
            $parameters .= $this->registration->config_values['custom_parameters'] . "\n";
        }
        $parameters .= $this->options['custom_parameters'] ?? '';
        return $parameters;
    }

    /**
     * Get the send_lis_person attribute of this entry.
     */
    public function getSendLisPerson()
    {
        return $this->registration->config_values['send_lis_person'];
    }
}

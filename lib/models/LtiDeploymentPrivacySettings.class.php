<?php

class LtiDeploymentPrivacySettings extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_deployment_privacy_settings';

        //TODO: relations

        parent::configure($config);
    }
}

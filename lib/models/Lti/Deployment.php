<?php
namespace Lti;

use SimpleORMap;

class Deployment extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_deployments';

        $config['belongs_to']['registration'] = [
            'class_name' => Registration::class,
            'foreign_key' => 'registration_id',
            'assoc_foreign_key' => 'id'
        ];

        parent::configure($config);
    }
}

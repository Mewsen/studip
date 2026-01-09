<?php
namespace Lti;

use SimpleORMap;

class RegistrationConfig extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_registration_configs';

        $config['belongs_to']['registration'] = [
            'class_name' => Registration::class,
            'foreign_key' => 'registration_id',
            'assoc_foreign_key' => 'id'
        ];

        parent::configure($config);
    }
}

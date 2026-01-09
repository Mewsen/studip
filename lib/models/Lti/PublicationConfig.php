<?php
namespace Lti;

use SimpleORMap;

class PublicationConfig extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_publication_configs';

        $config['belongs_to']['publication'] = [
            'class_name' => Publication::class,
            'foreign_key' => 'publication_id',
            'assoc_foreign_key' => 'id'
        ];

        parent::configure($config);
    }
}

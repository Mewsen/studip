<?php
namespace Lti;

use SimpleORMap;

/**
 * @property int $id
 * @property string name
 * @property string $value
 * @property int $mkdate
 * @property int $chdate
 * @property Publication $publication
 */
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

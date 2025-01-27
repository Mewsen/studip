<?php

/**
 * @license GPL2 or any later version
 *
 * @property string $id alias column for tag_hash
 * @property int $tag_id database column
 * @property string $range_id database column
 * @property string $range_type database column
 * @property int $mkdate database column
 */
class TagRelation extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'tags_relations';
        parent::configure($config);
    }
}

<?php

/**
 * @license GPL2 or any later version
 *
 * @property int $id database column
 * @property int|null $tag_id database column
 * @property string|null $range_id database column
 * @property string|null $range_type database column
 * @property int|null $mkdate database column
 */
class TagRelation extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'tags_relations';
        parent::configure($config);
    }
}

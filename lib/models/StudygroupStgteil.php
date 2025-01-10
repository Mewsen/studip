<?php

/**
 * @license GPL2 or any later version
 *
 * @property string $id alias column for tag_hash
 * @property string $studygroup_id database column
 * @property string $stgteil_id database column
 * @property int $mkdate database column
 *
 */
class StudygroupStgteil extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'studygroup_stgteil';
        parent::configure($config);
    }
}

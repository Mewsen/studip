<?php

/**
 * @license GPL2 or any later version
 *
 * @property int $id database column
 * @property string $studygroup_id database column
 * @property string|null $stgteil_id database column
 * @property int|null $mkdate database column
 */
class StudygroupStgteil extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'studygroup_stgteil';
        parent::configure($config);
    }
}

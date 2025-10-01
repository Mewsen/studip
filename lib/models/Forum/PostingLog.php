<?php
namespace Forum;

use SimpleORMap;
use User;

/**
 * @property int $id
 * @property string $posting_id
 * @property string $user_id
 * @property string $action
 * @property int $mkdate
 *
 * @property Posting $posting
 * @property User $user
 */
class PostingLog extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_posting_logs';

        $config['belongs_to']['posting'] = [
            'class_name' => Posting::class,
            'foreign_key' => 'posting_id',
            'assoc_foreign_key' => 'posting_id'
        ];

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }
}

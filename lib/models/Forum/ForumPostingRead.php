<?php
namespace Forum;

use SimpleORMap;
use User;

/**
 * @property string $discussion_id
 * @property string $user_id
 * @property int $read_index
 * @property int $chdate
 *
 * @property ForumDiscussion $discussion
 * @property User $users
 */

class ForumPostingRead extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_posting_reads';

        $config['belongs_to']['discussion'] = [
            'class_name' => ForumDiscussion::class,
            'foreign_key' => 'discussion_id',
            'assoc_foreign_key' => 'discussion_id'
        ];

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }

    public static function updateUserReadPoint($user_id, $discussion_id, int $read_index = 0): ForumPostingRead
    {
        $postingRead = ForumPostingRead::findOneBySQL(
            "discussion_id = :discussion_id AND user_id = :user_id",
            [
                'discussion_id' => $discussion_id,
                'user_id' => $user_id
            ]
        );

        if (!$postingRead) {
            $postingRead = new ForumPostingRead();
            $postingRead->discussion_id = $discussion_id;
            $postingRead->user_id = $user_id;
        }

        if (!$read_index) {
            $read_index = $postingRead->read_index + 1;
        }

        $postingRead->read_index = $read_index;

        $postingRead->store();

        return $postingRead;
    }
}

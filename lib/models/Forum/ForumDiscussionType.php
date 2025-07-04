<?php

namespace Forum;

use SimpleORMap;

/**
 * @property string $type_id
 * @property string $name
 * @property string $icon
 * @property int $mkdate
 * @property int $chdate
 *
 * @property ForumDiscussion[] $discussions
 */

class ForumDiscussionType extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_discussion_types';

        $config['has_many']['discussions'] = [
            'class_name' => ForumDiscussion::class,
            'foreign_key' => 'type_id' ,
            'assoc_foreign_key' => 'type_id'
        ];

        parent::configure($config);
    }

    public static function getForumDiscussionType(): array
    {
        return self::findBySQL("TRUE ORDER BY `mkdate` DESC");
    }
}

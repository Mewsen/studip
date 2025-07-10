<?php

namespace Forum;

use DBManager;
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

    /**
     * @return ForumDiscussion[]
     */
    public function getDiscussions(): array
    {
        return DBManager::get()->fetchAll(
            "SELECT
                    discussions.*,
                    MAX(postings.mkdate) AS latest_post_date
                FROM forum_discussions AS discussions
                JOIN forum_postings as postings USING (discussion_id)
                WHERE discussions.type_id = :type_id
                GROUP BY discussions.discussion_id
                ORDER BY discussions.sticky DESC, latest_post_date DESC",
            ['type_id' => $this->type_id],
            ForumDiscussion::buildExisting(...)
        );
    }
}

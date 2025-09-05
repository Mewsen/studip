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
 * @property Discussion[] $discussions
 */

class DiscussionType extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_discussion_types';

        $config['has_many']['discussions'] = [
            'class_name' => Discussion::class,
            'foreign_key' => 'type_id' ,
            'assoc_foreign_key' => 'type_id'
        ];

        parent::configure($config);
    }

    /**
     * @return self[]
     */
    public static function getAll(): array
    {
        return self::findBySQL("TRUE ORDER BY `mkdate` DESC");
    }

    public function transformData(): array
    {
        return [
            'id' => $this->type_id,
            'icon' => $this->icon,
            'name' => $this->name,
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate)
        ];
    }

    /**
     * @return Discussion[]
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
            Discussion::buildExisting(...)
        );
    }
}

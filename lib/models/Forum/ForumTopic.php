<?php
namespace Forum;

use DBManager;
use SimpleORMap;
use User;

/**
 * @property string $topic_id
 * @property string $category_id
 * @property string $range_id
 * @property string $name
 * @property string $description
 * @property int $position
 * @property int $mkdate
 * @property int $chdate
 *
 * @property ForumCategory $category
 * @property ForumDiscussion[] $discussions
 * @property User[] $users
 * @property array $metadata
 */

class ForumTopic extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_topics';

        $config['belongs_to']['category'] = [
            'class_name' => ForumCategory::class,
            'foreign_key' => 'category_id',
            'assoc_foreign_key' => 'category_id'
        ];

        $config['has_many']['discussions'] = [
            'class_name' => ForumDiscussion::class,
            'foreign_key' => 'topic_id',
            'assoc_foreign_key' => 'topic_id',
        ];

        $config['additional_fields']['users']['get'] = 'getUsers';
        $config['additional_fields']['metadata']['get'] = 'getMetaData';
        $config['registered_callbacks']['after_delete'][] = 'onDelete';

        parent::configure($config);
    }

    public static function getCourseTopics($course_id)
    {
        return self::findBySQL(
            "range_id = :course_id
                GROUP BY CASE WHEN category_id IS NULL THEN topic_id ELSE category_id END
                ORDER BY position ASC, mkdate DESC",
            ["course_id" => $course_id]
        );
    }

    public static function getCourseTopic($course_id, $topic_id)
    {
        return self::findOneBySQL("range_id = ? AND topic_id = ?", [$course_id, $topic_id]);
    }

    public function getUsers($last_visit = null): array
    {
        $query = [
            "JOIN forum_postings USING(user_id)
            JOIN forum_discussions USING(discussion_id)
            WHERE forum_discussions.topic_id = :topic_id ",
            ['topic_id' => $this->topic_id]
        ];

        if ($last_visit) {
            $query[0] .= " AND forum_postings.mkdate > :last_visit";
            $query[1]["last_visit"] = $last_visit;
        }

        $users = User::findBySQL($query[0]." ORDER BY forum_postings.mkdate DESC", $query[1]);

        $unique_users = [];
        foreach ($users as $user) {
            $unique_users[$user->user_id] = $user;
        }

        return array_values($unique_users);
    }

    public function getMetaData(): array
    {
        $user_id = User::findCurrent()->user_id;
        $object_user_visit = \ObjectUserVisit::findOneBySQL(
            "object_id = :object_id AND plugin_id = :plugin_id AND user_id = :user_id",
            [
                'object_id' => $this->range_id,
                'plugin_id' => \PluginEngine::getPlugin(\CoreForum::class)->getPluginId(),
                'user_id' => $user_id,
            ]
        );

        return DBManager::get()->fetchOne(
            "SELECT
                        COUNT(DISTINCT `forum_discussions`.`discussion_id`) AS 'discussions_count',
                        COUNT(DISTINCT `forum_postings`.`posting_id`) AS 'postings_count',
                        COUNT(DISTINCT `forum_postings`.`user_id`) AS 'users_count',
                        MAX(`forum_postings`.`mkdate`) AS 'recent_activity',
                        (
                            SELECT
                                SUM(fpr.read_index)
                            FROM forum_discussions fd2
                            JOIN forum_posting_reads fpr
                                ON fpr.discussion_id = fd2.discussion_id
                               AND fpr.user_id = :user_id
                            WHERE fd2.topic_id = :topic_id
                        ) AS 'user_read_index',
                        (
                            SELECT
                                COUNT(DISTINCT fp.posting_id)
                            FROM forum_topics ft
                            JOIN forum_discussions fd USING(topic_id)
                            JOIN forum_postings fp ON fp.discussion_id = fd.discussion_id AND fp.mkdate > :last_visit
                            WHERE ft.topic_id = :topic_id
                        ) AS 'recent_postings_count'
                    FROM `forum_discussions`
                    LEFT JOIN `forum_postings` USING (`discussion_id`)
                    WHERE `forum_discussions`.`topic_id` = :topic_id",
            [
                'topic_id' => $this->topic_id,
                'user_id' => $user_id,
                'last_visit' => $object_user_visit->last_visitdate ?? 0
            ]
        );
    }

    public function transformData(): array
    {
        return [
            'topic_id' => $this->topic_id,
            'category_id' => $this->category_id,
            'range_id' => $this->range_id,
            'name' => $this->name,
            'description' => $this->description,
            'position' => $this->position,
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate)
        ];
    }

    public function onDelete()
    {
        ForumSubscription::deleteBySQL("subject_id = ?", [$this->topic_id]);
        ForumDiscussion::deleteBySQL("topic_id = ?", [$this->topic_id]);
    }
}

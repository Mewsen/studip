<?php
namespace Forum;

use DBManager;
use Range;
use User;

/**
 * @property string $category_id
 * @property string $range_id
 * @property string $name
 * @property string $description
 * @property string $color
 * @property int $position
 * @property int $mkdate
 * @property int $chdate
 *
 * @property Range $range
 * @property Topic[] $topics
 * @property array $metadata
 */
class Category extends \SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_categories';

        $config['has_many']['topics'] = [
            'class_name' => Topic::class,
            'foreign_key' => 'category_id',
            'assoc_foreign_key' => 'category_id',
            'order_by' => 'ORDER BY position ASC, mkdate DESC',
        ];

        $config['additional_fields']['range'] = [
            'set' => function (Category $category, string $field, Range $range) {
                $category->range_id = $range->getRangeId();
            },
            'get' => function (Category $category): Range {
                return get_object_by_range_id($category->range_id);
            },
        ];

        $config['additional_fields']['metadata']['get'] = 'getMetaData';

        $config['registered_callbacks']['after_delete'][] = 'onDelete';

        parent::configure($config);
    }

    /**
     * @return self[]
     */
    public static function getCourseCategories($range_id): array
    {
       return self::findBySQL("range_id = ? ORDER BY position ASC, mkdate DESC", [$range_id]);
    }

    public function getMetaData(): array
    {
        return DBManager::get()->fetchOne(
            "SELECT
                        COUNT(DISTINCT`forum_topics`.`topic_id`) AS 'topics_count',
                        COUNT(DISTINCT `forum_discussions`.`discussion_id`) AS 'discussions_count',
                        COUNT(DISTINCT `forum_postings`.`posting_id`) AS 'postings_count',
                        COUNT(DISTINCT `forum_postings`.`user_id`) AS 'users_count',
                        MAX(`forum_postings`.`mkdate`) AS 'recent_activity',
                        (
                            SELECT SUM(fpr.read_index)
                            FROM forum_topics ft2
                            LEFT JOIN forum_discussions fd2 USING (`topic_id`)
                            JOIN forum_posting_reads fpr
                                ON fpr.discussion_id = fd2.discussion_id
                               AND fpr.user_id = :user_id
                            WHERE ft2.category_id = :category_id
                        ) AS 'user_read_index'
                    FROM `forum_topics`
                    LEFT JOIN `forum_discussions` USING (`topic_id`)
                    LEFT JOIN `forum_postings` USING (`discussion_id`)
                    WHERE `forum_topics`.`category_id` = :category_id",
            [
                'category_id' => $this->category_id,
                'user_id' => User::findCurrent()->user_id
            ]
        );
    }

    public function transformData(): array
    {
        return [
            'category_id' => $this->category_id,
            'range_id' => $this->range_id,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'position' => $this->position,
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate)
        ];
    }

    public function onDelete(): void
    {
        DBManager::get()->execute(
            "Update `forum_topics` SET `category_id` = null WHERE `category_id` = ?",
            [$this->category_id]
        );
    }
}

<?php
namespace Forum;

use User;
use DBManager;
use SimpleORMap;
use Forum\DTO\ForumMember;
use Forum\DTO\ForumTag;
use Forum\Service\DiscussionNotification;

/**
 * @property string $discussion_id
 * @property string $topic_id
 * @property int $type_id
 * @property string $title
 * @property bool $sticky
 * @property int $closed_at
 * @property int $view_count
 * @property int $mkdate
 * @property int $chdate
 *
 * @property ForumTopic $topic
 * @property User $user
 * @property ForumDiscussionType $discussion_type
 * @property ForumPosting[] $postings
 * @property ForumSubscription[] $subscribers
 * @property User[] $users
 * @property ForumMember[] $members
 * @property ForumTag[] $tags
 * @property ForumCategory $category
 */
class ForumDiscussion extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_discussions';

        $config['belongs_to']['topic'] = [
            'class_name' => ForumTopic::class,
            'foreign_key' => 'topic_id',
            'assoc_foreign_key' => 'topic_id'
        ];

        $config['belongs_to']['discussion_type'] = [
            'class_name' => ForumDiscussionType::class,
            'foreign_key' => 'type_id',
            'assoc_foreign_key' => 'type_id'
        ];

        $config['has_many']['postings'] = [
            'class_name' => ForumPosting::class,
            'foreign_key' => 'discussion_id',
            'assoc_foreign_key' => 'discussion_id',
            'order_by' => 'ORDER BY mkdate',
        ];

        $config['has_many']['subscribers'] = [
            'class_name' => ForumSubscription::class,
            'foreign_key' => 'discussion_id',
            'assoc_foreign_key' => 'discussion_id'
        ];

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];

        $config['additional_fields']['range_id']['get'] = 'getRangeId';
        $config['additional_fields']['category']['get'] = 'getCategory';
        $config['additional_fields']['tags']['get'] = 'getTags';
        $config['additional_fields']['users']['get'] = 'getUsers';
        $config['additional_fields']['members']['get'] = 'getMembers';
        $config['additional_fields']['metadata']['get'] = 'getMetaData';

        $config['registered_callbacks']['after_create'][] = 'onCreate';
        $config['registered_callbacks']['after_delete'][] = 'onDelete';

        parent::configure($config);
    }

    /**
     * @return self[]
     */
    public static function getCourseDiscussions($range_id, $last_visit = 0): array
    {
        $query = [
            "SELECT
                    discussions.*,
                    MAX(postings.mkdate) AS latest_post_date
                FROM forum_discussions AS discussions
                JOIN forum_postings as postings USING (discussion_id)
                JOIN forum_topics AS topics USING (topic_id)
                WHERE topics.range_id = :range_id",
            ['range_id' => $range_id]
        ];

        if ($last_visit) {
            $query[0] .= " AND postings.mkdate > :last_visit";
            $query[1]["last_visit"] = $last_visit;
        }

        return \DBManager::get()->fetchAll(
            $query[0]." GROUP BY discussions.discussion_id ORDER BY latest_post_date DESC",
            $query[1],
            self::buildExisting(...)
        );
    }

    public function getTags(): array
    {
        return DBManager::get()->fetchAll(
            "SELECT DISTINCT `tags_relations`.`tag_id`, `tags`.`name` FROM `tags`
                        LEFT JOIN  `tags_relations` ON `tags`.`id` = `tags_relations`.`tag_id`
                        WHERE `tags_relations`.`range_id` = :discussion_id AND `tags`.`active` = TRUE
                        ORDER BY `tags`.`mkdate` DESC",
            ['discussion_id' => $this->discussion_id],
            function ($tag) {
                return ForumTag::fromArray([
                    'id' => $tag['tag_id'],
                    'name' => $tag['name']
                ]);
            }
        );
    }

    public function getCategory(): ?ForumCategory
    {
        return ForumCategory::findOneBySQL("JOIN forum_topics USING (category_id) WHERE forum_topics.topic_id = :topic_id", ['topic_id' => $this->topic_id]);
    }

    public function getRangeId(): string
    {
        return $this->topic->range_id;
    }

    public function getUsers($last_visit = null): array
    {
        $query = [
            "JOIN forum_postings USING(user_id)
            WHERE forum_postings.discussion_id = :discussion_id
            AND forum_postings.anonymous = FALSE",
            ['discussion_id' => $this->discussion_id]
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

    public function getMembers($last_visit = null): array
    {
        $users = $this->getUsers($last_visit);

        $members = [];
        foreach ($users as $user) {
            $members[] = ForumMember::fromUser($user, $this->range_id);
        }

        return $members;
    }

    public function transformData(): array
    {
        return [
            'discussion_id' => $this->discussion_id,
            'topic_id' => $this->topic_id,
            'type_id' => $this->type_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'sticky' => (int) $this->sticky,
            'closed_at' => $this->closed_at ? date('c', $this->closed_at) : '',
            'view_count' => (int) $this->view_count,
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate)
        ];
    }

    public function getMetaData(int $last_visit = 0): array
    {
        $user_id = \User::findCurrent()->user_id;

        if (!$last_visit) {
            $plugin_id = \PluginEngine::getPlugin(\CoreForum::class)->getPluginId();
            $last_visit = object_get_visit($this->topic->range_id, $plugin_id, 'last', '', $user_id);
        }

        return DBManager::get()->fetchOne(
            "SELECT
                            COUNT(`posting_id`) 'postings_count',
                            MAX(`mkdate`) 'recent_activity',
                            (
                                SELECT
                                    `read_index`
                                FROM `forum_posting_reads`
                                WHERE `discussion_id` = :discussion_id AND `user_id` = :user_id
                            ) 'user_read_index',
                            (
                                SELECT
                                    COUNT(DISTINCT fp.posting_id)
                                FROM forum_postings fp
                                WHERE fp.discussion_id = :discussion_id AND fp.mkdate > :last_visit
                            ) AS 'recent_postings_count'
                        FROM `forum_postings` WHERE `discussion_id` = :discussion_id",
            [
                'discussion_id' => $this->discussion_id,
                'user_id' => $user_id,
                'last_visit' => $last_visit
            ]
        );
    }

    public function onCreate(): void
    {
        $discussionNotification = new DiscussionNotification($this);
        $discussionNotification->notifySubscribers();
    }

    public function onDelete(): void
    {
        ForumSubscription::deleteBySQL("subject_id = ?", [$this->discussion_id]);
        ForumPosting::deleteBySQL("discussion_id = ?", [$this->discussion_id]);
        ForumPostingRead::deleteBySQL("discussion_id = ?", [$this->discussion_id]);
    }
}

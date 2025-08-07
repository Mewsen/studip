<?php
namespace Forum;

use OpenGraph;
use SimpleORMap;
use Forum\Service\PostingNotification;
use User;
use Forum\DTO\Member as MemberDTO;

/**
 * @property string $posting_id
 * @property string $discussion_id
 * @property string $range_id
 * @property string $content
 * @property boolean $anonymous
 * @property int $mkdate
 * @property int $chdate
 *
 * @property Discussion $discussion
 * @property Posting $posting
 * @property PostingReaction[] $reactions
 * @property User $user
 * @property MemberDTO $author
 */
class Posting extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_postings';

        $config['belongs_to']['discussion'] = [
            'class_name' => Discussion::class,
            'foreign_key' => 'discussion_id',
            'assoc_foreign_key' => 'discussion_id'
        ];

        $config['belongs_to']['posting'] = [
            'class_name' => Posting::class,
            'foreign_key' => 'parent_id',
            'assoc_foreign_key' => 'posting_id'
        ];

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];

        $config['has_many']['reactions'] = [
            'class_name' => PostingReaction::class,
            'foreign_key' => 'posting_id',
            'assoc_foreign_key' => 'posting_id'
        ];

        $config['additional_fields']['author']['get'] = 'getAuthor';
        $config['registered_callbacks']['after_create'][] = 'onCreate';
        $config['registered_callbacks']['after_delete'][] = 'onDelete';

        parent::configure($config);
    }

    public function getAuthor(): ?MemberDTO
    {
        if ($this->anonymous && $this->user_id !== User::findCurrent()->user_id) {
            return MemberDTO::fromArray();
        }

        $user = $this->user;
        if ($user) {
            return MemberDTO::fromUser($user, $this->range_id);
        }

        return null;
    }

    public static function getRecentPosts(array|string $range_ids): array
    {
        $single = is_string($range_ids);
        if ($single) {
            $range_ids = [$range_ids];
        }
        $query =
            "SELECT
                forum_topics.range_id,
                forum_discussions.*,
                COUNT(DISTINCT forum_postings.posting_id) AS 'posts'
            FROM forum_topics
            JOIN forum_discussions USING(topic_id)
            JOIN forum_postings USING(discussion_id)
            LEFT JOIN forum_posting_reads AS fp_reads
              ON fp_reads.discussion_id = forum_discussions.discussion_id
                AND fp_reads.user_id = :user_id
            WHERE forum_topics.range_id IN (:range_ids)
              AND forum_postings.user_id != :user_id
              AND forum_postings.mkdate > IFNULL(fp_reads.read_index, 0)
            GROUP BY forum_topics.range_id, forum_discussions.discussion_id";
        $params = [
            ':range_ids' => $range_ids,
            ':user_id' => User::findCurrent()->id,
        ];

        $res = \DBManager::get()->fetchAll($query, $params);
        $by_course = [];
        foreach ($res as $row) {
            $by_course[$row['range_id']][] = $row;
        }
        return $single ? (array_pop($by_course) ?? []) : $by_course;
    }

    public function getOpenGraphURLs(): array
    {
        $content = preg_replace("~<blockquote(.*?)>(.*)</blockquote>~si", '', $this['content']);
        return array_filter(OpenGraph::extract($content)->toArray(), fn($og) => $og['is_opengraph']);
    }

    public function onCreate(): void
    {
        $postingNotification = new PostingNotification($this);
        $postingNotification->notifySubscribers();
    }

    public function onDelete(): void
    {
        PostingReaction::deleteBySQL("posting_id = ?", [$this->posting_id]);
    }
}

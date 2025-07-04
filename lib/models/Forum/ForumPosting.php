<?php
namespace Forum;

use SimpleORMap;
use Forum\Service\PostingNotification;
use User;
use Forum\DTO\ForumMember;

/**
 * @property string $posting_id
 * @property string $discussion_id
 * @property string $range_id
 * @property string $content
 * @property boolean $anonymous
 * @property int $mkdate
 * @property int $chdate
 *
 * @property ForumDiscussion $discussion
 * @property ForumPosting $posting
 * @property ForumPostingReaction[] $reactions
 * @property User $user
 * @property ForumMember $author
 */
class ForumPosting extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_postings';

        $config['belongs_to']['discussion'] = [
            'class_name' => ForumDiscussion::class,
            'foreign_key' => 'discussion_id',
            'assoc_foreign_key' => 'discussion_id'
        ];

        $config['belongs_to']['posting'] = [
            'class_name' => ForumPosting::class,
            'foreign_key' => 'parent_id',
            'assoc_foreign_key' => 'posting_id'
        ];

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];

        $config['has_many']['reactions'] = [
            'class_name' => ForumPostingReaction::class,
            'foreign_key' => 'posting_id',
            'assoc_foreign_key' => 'posting_id'
        ];

        $config['additional_fields']['author']['get'] = 'getAuthor';
        $config['registered_callbacks']['after_create'][] = 'onCreate';
        $config['registered_callbacks']['after_delete'][] = 'onDelete';

        parent::configure($config);
    }

    public function getAuthor(): ?ForumMember
    {
        if ($this->anonymous && $this->user_id !== User::findCurrent()->user_id) {
            return ForumMember::fromArray();
        }

        $user = $this->user;
        if ($user) {
            return ForumMember::fromUser($user, $this->range_id);
        }

        return null;
    }

    public static function getRecentPosts($course_id, int $last_visit = 0): array
    {
        $query = [
            "SELECT
                forum_discussions.*,
                COUNT(DISTINCT forum_postings.posting_id) AS 'posts'
            FROM forum_topics
            JOIN forum_discussions USING(topic_id)
            JOIN forum_postings USING(discussion_id)
            WHERE forum_topics.range_id = :course_id
            ",
            [
                'course_id' => $course_id
            ]
        ];

        if ($last_visit) {
            $query[0] .= " AND forum_postings.mkdate > :last_visit";
            $query[1]["last_visit"] = $last_visit;
        }

        return \DBManager::get()->fetchAll(
            $query[0]." GROUP BY discussion_id ORDER BY forum_postings.mkdate DESC",
            $query[1]
        );
    }

    public function getOpenGraphURLs()
    {
        $content = preg_replace("~<blockquote(.*?)>(.*)</blockquote>~si", '', $this['content']);
        return \OpenGraph::extract($content);
    }

    public function onCreate()
    {
        $postingNotification = new PostingNotification($this);
        $postingNotification->notifySubscribers();
    }

    public function onDelete()
    {
        ForumPostingReaction::deleteBySQL("posting_id = ?", [$this->posting_id]);
    }
}

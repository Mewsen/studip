<?php
require_once 'ForumBaseController.php';

use Forum\ForumDiscussion;
use Forum\ForumDiscussionType;
use Forum\DTO\ForumMember;
use Forum\DTO\ForumTag;

class Course_Forum_SearchController extends Forum\ForumBaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem('course/forum');
    }

    public function index_action()
    {
        $topics = DBManager::get()->fetchAll(
            "SELECT
                    `ft`.`topic_id`, `ft`.`name`, `fc`.`color`
                FROM `forum_topics` AS `ft`
                LEFT JOIN `forum_categories` AS `fc` USING (`category_id`)
                WHERE `ft`.`range_id` = :course_id
                ORDER BY `ft`.`position` ASC, `ft`.`mkdate` DESC
            ",
            ['course_id' => $this->course_id]
        );

        $course_members = [];
        foreach (Context::get()->members as $member) {
            $course_members[] = [
                'user_id' => $member['user_id'],
                'name' => $member['Vorname'] . ' ' . $member['Nachname'],
                'avatar_url' => Avatar::getAvatar($member['user_id'])->getURL(Avatar::NORMAL),
                'profile_url' => URLHelper::getLink('dispatch.php/profile', ['username' => $member['username']], true)
            ];
        }

        $search_object = $this->buildSearchObject();
        $all_tags = array_map(fn(ForumTag $tag) => $tag->toRawArray(), ForumTag::getForumTags());
        $discussion_types = array_map(fn(ForumDiscussionType $discussion_type) => $discussion_type->toRawArray(), ForumDiscussionType::getForumDiscussionType());

        $this->render_vue_app(
            Studip\VueApp::create('forum/search/Index')
                ->withProps([
                    'search' => $search_object,
                    'discussions' =>  $this->getResult($search_object),
                    'topics' => $topics,
                    'discussion_types' => $discussion_types,
                    'tags' => $all_tags,
                    'course_members' => $course_members,
                ])
        );
    }

    private function getResult($search_object): array
    {
        if ($this->isSearchObjectEmpty($search_object)) {
            unset($_SESSION['forum'][$this->course_id]['search']);
            return [];
        }

        $query = [
            "SELECT
                    discussions.discussion_id,
                    COUNT(DISTINCT postings.posting_id) AS 'postings_count'
                FROM `forum_discussions` AS `discussions`
                LEFT JOIN `forum_postings` AS `postings` USING(`discussion_id`)
                LEFT JOIN `tags_relations` ON (`tags_relations`.`range_id` = `discussions`.`discussion_id` AND `range_type` = 'forum')
                WHERE `postings`.`range_id` = :course_id ",
            [
                'course_id' => $this->course_id
            ]
        ];

        $keyword = $search_object['keyword'];
        if ($keyword) {
            $query[0] .= " AND (discussions.title LIKE :keyword OR postings.content LIKE :keyword)";
            $query[1]["keyword"] = "%$keyword%";
        }

        if ($search_object['begin']) {
            $query[0] .= " AND postings.mkdate >= :begin";
            $query[1]['begin'] = $search_object['begin'];
        }

        if ($search_object['end']) {
            $query[0] .= " AND postings.mkdate <= :end";
            $query[1]['end'] = $search_object['end'];
        }

        if ($search_object['topic_ids']) {
            $query[0] .= " AND discussions.topic_id IN (:topic_ids)";
            $query[1]['topic_ids'] = $search_object['topic_ids'];
        }

        if ($search_object['discussion_type_ids']) {
            $query[0] .= " AND discussions.type_id IN (:type_ids)";
            $query[1]['type_ids'] = $search_object['discussion_type_ids'];
        }

        if ($search_object['tag_ids']) {
            $query[0] .= " AND tags_relations.tag_id IN (:tag_ids)";
            $query[1]['tag_ids'] = $search_object['tag_ids'];
        }

        if ($search_object['user_ids']) {
            $query[0] .= " AND postings.user_id IN (:user_ids)";
            $query[1]['user_ids'] = $search_object['user_ids'];
        }

        $query[0] .= match ($search_object['discussion_status']) {
            2 => " AND discussions.closed_at IS NULL", // opens
            3 => " AND discussions.closed_at IS NOT NULL", // closed
            default => ""
        };

        $result =  DBManager::get()->fetchAll(
            $query[0]." GROUP BY discussions.discussion_id",
            $query[1]
        );

        $discussions = ForumDiscussion::findBySQL("discussion_id IN (:discussion_ids)", ['discussion_ids' => array_column($result, 'discussion_id')]);


        return array_map(function (ForumDiscussion $discussion) use ($result) {
            $postings_count = array_find($result, fn($item) => $item['discussion_id'] === $discussion->discussion_id)['postings_count'];
            $members = array_map(fn(ForumMember $member) => $member->toRawArray(), $discussion->members);
            $tags = array_map(fn(ForumTag $tag) => $tag->toRawArray(), $discussion->tags);

            return [
                'id' => $discussion->discussion_id,
                'title' => $discussion->title,
                'closed_at' => $discussion->closed_at ? date('c', $discussion->closed_at) : null,
                'view_count' => (int) $discussion->view_count,
                'sticky' => (bool) $discussion->sticky,
                'mkdate' => date('c', $discussion->mkdate),
                'chdate' => date('c', $discussion->chdate),
                'topic' => $discussion->topic->toRawArray(),
                'category' => $discussion->category ? [
                    'name' => $discussion->category->name,
                    'color' => $discussion->category->color,
                ] : [],
                'discussion_type' => $discussion->discussion_type ? [
                    'name' => $discussion->discussion_type->name,
                    'icon' => $discussion->discussion_type->icon,
                ] : [],
                'members' => $members,
                'tags' => $tags,
                'meta' => [
                    'postings_count' => (int) $postings_count,
                    'recent_activity' => $discussion->metadata['recent_activity'] ? date('c', $discussion->metadata['recent_activity']) : null,
                ]
            ];
        }, $discussions);
    }

    private function isSearchObjectEmpty($search_object): bool {
        if (
            $search_object['keyword'] ||
            $search_object['begin'] ||
            $search_object['end'] ||
            $search_object['discussion_status'] ||
            $search_object['discussion_type_ids'] ||
            $search_object['tag_ids'] ||
            $search_object['topic_ids'] ||
            $search_object['user_ids']
        ) {
            return false;
        }

        return true;
    }

    private function buildSearchObject(): array
    {
        $request = Request::getInstance();
        if (
            $request->offsetExists('keyword') ||
            $request->offsetExists('begin') ||
            $request->offsetExists('end') ||
            $request->offsetExists('discussion_status') ||
            $request->offsetExists('discussion_type_ids') ||
            $request->offsetExists('tag_ids') ||
            $request->offsetExists('topic_ids') ||
            $request->offsetExists('user_ids')
        ) {
            $search_object =  [
                'keyword' => Request::get('keyword'),
                'begin' => Request::int('begin'),
                'end' => Request::int('end'),
                'discussion_status' => Request::int('discussion_status'),
                'discussion_type_ids' =>  Request::getArray('discussion_type_ids'),
                'tag_ids' => Request::getArray('tag_ids'),
                'topic_ids' => Request::getArray('topic_ids'),
                'user_ids' => Request::getArray('user_ids')
            ];

            $_SESSION['forum'][$this->course_id]['search'] = $search_object;
            return $search_object;
        }

        $session_search = $_SESSION['forum'][$this->course_id]['search'] ?? [];
        return [
            'keyword' => $session_search['keyword'] ?? '',
            'begin' => $session_search['begin'] ?? 0,
            'end' => $session_search['end'] ?? 0,
            'discussion_status' => $session_search['discussion_status'] ?? 0,
            'discussion_type_ids' => $session_search['discussion_type_ids'] ?? [],
            'tag_ids' => $session_search['tag_ids'] ?? [],
            'topic_ids' => $session_search['topic_ids'] ?? [],
            'user_ids' => $session_search['user_ids'] ?? []
        ];
    }
}

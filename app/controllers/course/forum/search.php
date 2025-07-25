<?php
require_once 'ForumBaseController.php';

use Forum\ForumDiscussionType;
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
                WHERE `ft`.`range_id` = :range_id
                ORDER BY `ft`.`position` ASC, `ft`.`mkdate` DESC
            ",
            ['range_id' => $this->range_id]
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

        $tags = array_map(fn(ForumTag $tag) => $tag->toRawArray(), ForumTag::getForumTags());
        $discussion_types = array_map(fn(ForumDiscussionType $discussion_type) => $discussion_type->toRawArray(), ForumDiscussionType::getForumDiscussionType());

        $this->render_vue_app(
            Studip\VueApp::create('forum/search/Index')
                ->withProps([
                    'filter' => $this->getForumFilter(),
                    'topics' => $topics,
                    'discussion_types' => $discussion_types,
                    'tags' => $tags,
                    'course_members' => $course_members,
                ])
        );
    }

    private function getForumFilter(): array
    {
        $request = Request::getInstance();
        $filter = [];
        $session_filter = $_SESSION['forum'][$this->range_id]['search_filter'] ?? [];

        if ($request->offsetExists('keyword')) {
            $filter['keyword'] = Request::get('keyword');
        } else if (isset($session_filter['keyword'])) {
            $filter['keyword'] = $session_filter['keyword'];
        }

        if ($request->offsetExists('begin')) {
            $filter['begin'] = Request::int('begin');
        } else if (isset($session_filter['begin'])) {
            $filter['begin'] = (int) $session_filter['begin'];
        }

        if ($request->offsetExists('end')) {
            $filter['end'] = Request::int('end');
        } else if (isset($session_filter['end'])) {
            $filter['end'] = (int) $session_filter['end'];
        }

        if ($request->offsetExists('status')) {
            $filter['status'] = Request::int('status');
        } else if (isset($session_filter['status'])) {
            $filter['status'] = (int) $session_filter['status'];
        }

        if ($request->offsetExists('type_ids')) {
            $filter['type_ids'] = Request::getArray('type_ids');
        } else if (isset($session_filter['type_ids'])) {
            $filter['type_ids'] = $session_filter['type_ids'];
        }

        if ($request->offsetExists('tag_ids')) {
            $filter['tag_ids'] = Request::getArray('tag_ids');
        } else if (isset($session_filter['tag_ids'])) {
            $filter['tag_ids'] = $session_filter['tag_ids'];
        }

        if ($request->offsetExists('topic_ids')) {
            $filter['topic_ids'] = Request::getArray('topic_ids');
        } else if (isset($session_filter['topic_ids'])) {
            $filter['topic_ids'] = $session_filter['topic_ids'];
        }

        if ($request->offsetExists('user_ids')) {
            $filter['user_ids'] = Request::getArray('user_ids');
        } else if (isset($session_filter['user_ids'])) {
            $filter['user_ids'] = $session_filter['user_ids'];
        }

        return $filter;
    }
}

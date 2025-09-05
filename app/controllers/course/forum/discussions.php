<?php

use Studip\Markup;
use Forum\Discussion;
use Forum\DiscussionType;
use Forum\Posting;
use Forum\PostingRead;
use Forum\Subscription;
use Forum\DTO\Member as MemberDTO;
use Forum\DTO\Tag as TagDTO;
use Forum\Topic;

class Course_Forum_DiscussionsController extends Forum\BaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if ($action === 'index') {
            Navigation::activateItem('course/forum/discussions');
        } else {
            Navigation::activateItem('course/forum/topics');
        }
    }

    public function index_action() {

        $metadata = DBManager::get()->fetchOne(
            "SELECT
              COUNT(posting_id) as 'postings_count',
              COUNT(DISTINCT user_id) as 'users_count' ,
              MAX(mkdate) as 'recent_activity'
            FROM forum_postings WHERE range_id = :range_id",
            [
                'range_id' => $this->range_id
            ]
        );

        $this->render_vue_app(
            Studip\VueApp::create('forum/discussions/Index')
                ->withProps([
                    'metadata' => [
                        'postings_count' => (int) $metadata['postings_count'],
                        'users_count' => (int) $metadata['users_count'],
                        'recent_activity' => $metadata['recent_activity'] ? date('c', $metadata['recent_activity']) : null
                    ]
                ])
        );
    }

    public function show_action($discussion_id)
    {
        $discussion = Discussion::find($discussion_id);

        if (!$discussion) {
            throw new AccessDeniedException();
        }

        PageLayout::setTitle($discussion->title);

        $discussion->view_count += 1;
        $discussion->store();

        $auth = User::findCurrent();
        $posting_read = null;
        $auth_user = [];
        if ($auth) {
            $posting_read = PostingRead::findOneBySQL(
                "discussion_id = :discussion_id AND user_id = :user_id",
                [
                    'discussion_id' => $discussion->getId(),
                    'user_id' => $auth->user_id
                ]
            );

            $user_subscription = Subscription::findOneBySQL(
                "subject = :subject AND subject_id = :subject_id AND user_id = :user_id",
                [
                    'subject' => 'discussion',
                    'subject_id' => $discussion->getId(),
                    'user_id' => $auth->user_id
                ]
            );

            $auth_user = [
                'id' => $auth->id,
                'username' => $auth->username,
                'name' => $auth->getFullName(),
                'avatar_url' => Avatar::getAvatar($auth->user_id)->getURL(Avatar::NORMAL),
                'subscription' => $user_subscription ? $user_subscription->toRawArray() : []
            ];
        }

        $category = $discussion->getCategory();
        $tags = array_map(fn(TagDTO $tag) => $tag->toRawArray(), $discussion->tags);
        $members = array_map(fn(MemberDTO $member) => $member->toRawArray(), $discussion->members);

        $this->render_vue_app(
            Studip\VueApp::create('forum/discussions/Show')
                ->withProps([
                    'auth_user' => $auth_user,
                    'discussion' => [
                        ...$discussion->transformData(),
                        'topic' => $discussion->topic->toRawArray(),
                        'tags' => $tags,
                        'members' => $members,
                        'type' => !empty($discussion->discussion_type) ? $discussion->discussion_type->toRawArray() : []
                    ],
                    'category' => $category ? $category->toRawArray() : [],
                    'read_index' => (int) ($posting_read ? $posting_read->read_index : 0),
                    'redirect' => Request::option('redirect'),
                    'search_keyword' => Request::get('q', $_SESSION['forum'][$this->range_id]['search_filter']['keyword'] ?? '')
                ])
        );
    }

    public function edit_action(Discussion $discussion = null)
    {
        if ($discussion->isNew()) {
            PageLayout::setTitle(_('Neue Diskussion starten'));
        } else {
            PageLayout::setTitle(_('Diskussion bearbeiten'));
        }

        $topics = DBManager::get()->fetchAll(
            "
                SELECT
                    `ft`.`topic_id`, `ft`.`name`, `fc`.`color`
                FROM `forum_topics` AS `ft`
                LEFT JOIN `forum_categories` AS `fc` USING (`category_id`)
                WHERE `ft`.`range_id` = :range_id
                ORDER BY `ft`.`position` ASC, `ft`.`mkdate` DESC
            ",
            ['range_id' => $this->range_id]
        );

        $all_tags = array_map(fn(TagDTO $tag) => $tag->toRawArray(), TagDTO::getForumTags());
        $discussion_tags = array_map(fn(TagDTO $tag) => $tag->toRawArray(), $discussion->tags);
        $discussion_types = array_map(fn(DiscussionType $discussion_type) => $discussion_type->toRawArray(), DiscussionType::getAll());

        $this->render_vue_app(
            Studip\VueApp::create('forum/discussions/Edit')
                ->withProps([
                    'discussion' => [
                        ...$discussion->transformData(),
                        'topic_id' => !empty($discussion->topic_id) ? $discussion->topic_id : Request::option('topic_id'),
                        'tags' => $discussion_tags
                    ],
                    'topics' => $topics,
                    'tags' => $all_tags,
                    'discussion_types' => $discussion_types
                ])
        );
    }

    public function save_action($discussion_id = null)
    {
        CSRFProtection::verifyUnsafeRequest();

        if ($discussion_id) {
            $discussion = Discussion::find($discussion_id);
        } else {
            $discussion = new Discussion();
            $discussion->user_id = $this->user_id;
        }

        $discussion->title = Request::get('title');
        $discussion->closed_at = Request::bool('closed_at', false) ? time() : null;
        if ($this->is_moderator) {
            $discussion->sticky = Request::bool('sticky', false);
        }

        if (Request::get('type_id')) {
            $discussion->type_id = Request::get('type_id');
        }

        $topic = json_decode(Request::get('topic'), true);

        if (empty($topic['topic_id'])) {
            $newTopic = Topic::create([
                'range_id' => $this->range_id,
                'name' => $topic['name']
            ]);

            $topic['topic_id'] = $newTopic->topic_id;
        }

        $discussion->topic_id = $topic['topic_id'];
        $discussion->store();

        if (!$discussion_id && Request::get('content')) {
            Posting::create([
                'range_id' => $this->range_id,
                'discussion_id' => $discussion->discussion_id,
                'content' => Markup::purifyHtml(Markup::markAsHtml(Request::get('content'))),
                'user_id' => $this->user_id
            ]);
        } else {
            TagRelation::deleteBySQL("range_id = ? AND range_type = 'forum'", [$discussion->discussion_id]);
        }

        $tags = json_decode(Request::get('tags'), true);

        foreach ($tags as $tag) {
            if (empty($tag['tag_id'])) {
                $newTag = Tag::create([
                    'name' => $tag['name'],
                ]);

                $tag['tag_id'] = $newTag->id;
            }

            TagRelation::create([
                'tag_id' => $tag['tag_id'],
                'range_id' => $discussion->discussion_id,
                'range_type' => 'forum'
            ]);
        }

        PageLayout::postSuccess(_('Der Beitrag wurde gespeichert.'));

        $this->relocate(
            $discussion_id ? 'course/forum/topics/show/' . $discussion->topic_id : 'course/forum/discussions/show/' . $discussion->discussion_id
        );
    }

    public function delete_action($discussion_id)
    {
        $discussion = Discussion::find($discussion_id);

        if (!$discussion) {
            throw new AccessDeniedException();
        }

        if (!$this->is_moderator && $discussion->user_id !== $this->user_id) {
            throw new AccessDeniedException();
        }

        TagRelation::deleteBySQL("range_id = ? AND range_type = 'forum'", [$discussion->discussion_id]);
        $topic_id = $discussion->topic_id;

        $discussion->delete();

        PageLayout::postSuccess(_('Die Diskussion wurde gelöscht.'));

        $this->relocate('course/forum/topics/show/' . $topic_id);
    }
}

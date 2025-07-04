<?php
require_once 'ForumBaseController.php';

use Studip\Markup;
use Forum\ForumDiscussion;
use Forum\ForumDiscussionType;
use Forum\DTO\ForumMember;
use Forum\ForumPosting;
use Forum\ForumPostingRead;
use Forum\ForumSubscription;
use Forum\DTO\ForumTag;
use Forum\ForumTopic;

class Course_Forum_DiscussionsController extends Forum\ForumBaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem('course/forum/topics');
    }

    public function show_action($discussion_id)
    {
        $discussion = ForumDiscussion::find($discussion_id);

        if (!$discussion) {
            throw new AccessDeniedException();
        }

        PageLayout::setTitle($discussion->title);

        $auth_user = User::findCurrent();

        $discussion->view_count += 1;
        $discussion->store();

        $posting_read = ForumPostingRead::findOneBySQL(
            "discussion_id = :discussion_id AND user_id = :user_id",
            [
                'discussion_id' => $discussion->getId(),
                'user_id' => User::findCurrent()->user_id
            ]
        );

        $user_subscription = ForumSubscription::findOneBySQL(
            "subject = :subject AND subject_id = :subject_id AND user_id = :user_id",
            [
                'subject' => 'discussion',
                'subject_id' => $discussion->getId(),
                'user_id' => $auth_user->user_id
            ]
        );

        $category = $discussion->getCategory();
        $tags = array_map(fn(ForumTag $tag) => $tag->toRawArray(), $discussion->tags);
        $members = array_map(fn(ForumMember $member) => $member->toRawArray(), $discussion->members);

        $this->render_vue_app(
            Studip\VueApp::create('forum/discussions/Show')
                ->withProps([
                    'auth_user' => [
                        'id' => $auth_user->id,
                        'username' => $auth_user->username,
                        'name' => $auth_user->getFullName(),
                        'avatar_url' => Avatar::getAvatar($auth_user->user_id)->getURL(Avatar::NORMAL),
                        'subscription' => $user_subscription ? $user_subscription->toRawArray() : []
                    ],
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
                    'search_keyword' => $_SESSION['forum'][$this->course_id]['search']['keyword'] ?? ''
                ])
        );
    }

    public function edit_action(ForumDiscussion $discussion = null)
    {
        if (!$this->is_moderator) {
            throw new AccessDeniedException();
        }

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
                WHERE `ft`.`range_id` = :course_id
                ORDER BY `ft`.`position` ASC, `ft`.`mkdate` DESC
            ",
            ['course_id' => $this->course_id]
        );

        $all_tags = array_map(fn(ForumTag $tag) => $tag->toRawArray(), ForumTag::getForumTags());
        $discussion_tags = array_map(fn(ForumTag $tag) => $tag->toRawArray(), $discussion->tags);
        $discussion_types = array_map(fn(ForumDiscussionType $discussion_type) => $discussion_type->toRawArray(), ForumDiscussionType::getForumDiscussionType());

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
        if (!$this->is_moderator) {
            throw new AccessDeniedException();
        }

        CSRFProtection::verifyUnsafeRequest();

        if ($discussion_id) {
            $discussion = ForumDiscussion::find($discussion_id);
        } else {
            $discussion = new ForumDiscussion();
        }

        $discussion->title = Request::get('title');
        $discussion->closed_at = Request::bool('closed_at', false) ? time() : null;
        $discussion->sticky = Request::bool('sticky', false);

        if (Request::get('type_id')) {
            $discussion->type_id = Request::get('type_id');
        }

        $topic = json_decode(Request::get('topic'), true);

        if (empty($topic['topic_id'])) {
            $newTopic = ForumTopic::create([
                'range_id' => $this->course_id,
                'name' => $topic['name']
            ]);

            $topic['topic_id'] = $newTopic->topic_id;
        }

        $discussion->topic_id = $topic['topic_id'];
        $discussion->store();

        if (!$discussion_id && Request::get('content')) {
            ForumPosting::create([
                'range_id' => $this->course_id,
                'discussion_id' => $discussion->discussion_id,
                'content' => Markup::markAsHtml(Request::get('content')),
                'user_id' => User::findCurrent()->user_id
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
        if (!$this->is_moderator) {
            throw new AccessDeniedException();
        }

        $discussion = ForumDiscussion::find($discussion_id);

        if (!$discussion) {
            throw new AccessDeniedException();
        }

        TagRelation::deleteBySQL("range_id = ? AND range_type = 'forum'", [$discussion->discussion_id]);
        $topic_id = $discussion->topic_id;

        $discussion->delete();

        PageLayout::postSuccess(_('Die Diskussion wurde gelöscht.'));

        $this->relocate('course/forum/topics/show/' . $topic_id);
    }
}

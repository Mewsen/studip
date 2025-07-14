<?php
require_once 'ForumBaseController.php';

use Forum\ForumCategory;
use Forum\ForumSubscription;
use Forum\ForumTopic;

class Course_Forum_TopicsController extends Forum\ForumBaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        unset($_SESSION['forum'][$this->range_id]['search']);

        Navigation::activateItem('course/forum/topics');
    }

    public function index_action()
    {
        $this->render_vue_app(
            Studip\VueApp::create('forum/topics/Index')
        );
    }

    public function show_action($topic_id)
    {
        $topic = ForumTopic::find($topic_id);

        if (!$topic) {
            throw new AccessDeniedException();
        }

        PageLayout::setTitle($topic->name);

        $user_subscription = ForumSubscription::findOneBySQL(
            "subject = :subject AND subject_id = :subject_id AND user_id = :user_id",
            [
                'subject' => 'topic',
                'subject_id' => $topic->getId(),
                'user_id' => User::findCurrent()->user_id
            ]
        );

        $this->render_vue_app(
            Studip\VueApp::create('forum/topics/Show')
                ->withProps([
                    'topic' => $topic->transformData(),
                    'category' => $topic->category ? $topic->category->transformData() : [],
                    'user_subscription' => $user_subscription ? $user_subscription->toRawArray() : [],
                    'metadata' => [
                        'postings_count' => (int) $topic->metadata['postings_count'],
                        'users_count' => (int) $topic->metadata['users_count'],
                        'recent_activity' => date('c', $topic->metadata['recent_activity'])
                    ]
                ])
        );
    }

    public function edit_action($topic_id = null)
    {
        if (!$this->is_moderator) {
            throw new AccessDeniedException();
        }

        if ($topic_id) {
            PageLayout::setTitle(_('Thema bearbeiten'));
            $topic = ForumTopic::getCourseTopic($this->range_id, $topic_id);

            if (!$topic) {
                throw new AccessDeniedException();
            }
        } else {
            PageLayout::setTitle(_('Neues Thema anlegen'));
            $topic = new ForumTopic();
            $topic['category_id'] = Request::get('category_id');
        }

        $categories = DBManager::get()->fetchAll(
            "SELECT * FROM `forum_categories` WHERE `range_id` = ? ORDER BY `position` ASC, `mkdate` DESC",
            [$this->range_id]
        );

        $this->render_vue_app(
            Studip\VueApp::create('forum/topics/Edit')
                ->withProps([
                    'topic' => $topic->transformData(),
                    'categories' => $categories
                ])
        );
    }

    public function save_action($topic_id = null)
    {
        if (!$this->is_moderator) {
            throw new AccessDeniedException();
        }

        CSRFProtection::verifyUnsafeRequest();

        if ($topic_id) {
            $topic = ForumTopic::getCourseTopic($this->range_id, $topic_id);
            if (!$topic) {
                throw new AccessDeniedException();
            }
        } else {
            $topic = new ForumTopic();
            $topic->range_id = $this->range_id;
        }

        $category = json_decode(Request::get('category'), true);

        if (empty($category['category_id']) && !empty($category['name'])) {
            $newCategory = ForumCategory::create([
                'range_id' => $this->range_id,
                'color' => '#28497C',
                'name' => $category['name']
            ]);

            $category['category_id'] = $newCategory->category_id;
        } else {
            $topic->category_id = null;
        }

        if (!empty($category['category_id'])) {
            $topic->category_id = $category['category_id'];
        }

        $topic->name = Request::get('name');
        $topic->description = Request::get('description');

        $topic->store();

        PageLayout::postSuccess(_('Das Thema wurde gespeichert.'));

        $this->relocate('course/forum/topics/show/' . $topic->topic_id);
    }

    public function delete_action($topic_id)
    {
        if (!$this->is_moderator) {
            throw new AccessDeniedException();
        }

        $topic = ForumTopic::getCourseTopic($this->range_id, $topic_id);

        if (!$topic) {
            throw new AccessDeniedException();
        }

        $topic->delete();

        PageLayout::postSuccess(_('Das Thema wurde gelöscht.'));

        $this->relocate('course/forum/topics');
    }
}

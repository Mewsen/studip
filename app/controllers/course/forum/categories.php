<?php
require_once 'BaseController.php';

use Forum\Category;

class Course_Forum_CategoriesController extends Forum\BaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!RangeConfig::get($this->range_id)->FORUM_HIDE_CATEGORIES_NAVIGATION) {
            Navigation::activateItem('course/forum/categories');
        } else {
            Navigation::activateItem('course/forum/topics');
        }
    }

    public function index_action()
    {
        $this->render_vue_app(
            Studip\VueApp::create('forum/categories/Index')
        );
    }

    public function show_action($category_id)
    {
        $category = Category::find($category_id);

        if (!$category) {
            throw new AccessDeniedException();
        }

        PageLayout::setTitle($category->name);

        $this->render_vue_app(
            Studip\VueApp::create('forum/categories/Show')
                ->withProps([
                    'category' => $category->transformData(),
                    'metadata' => [
                        'postings_count' => (int) $category->metadata['postings_count'],
                        'users_count' => (int) $category->metadata['users_count'],
                        'recent_activity' => $category->metadata['recent_activity'] ? date('c', $category->metadata['recent_activity']) : null
                    ]
            ])
        );
    }

    public function edit_action($category_id = null)
    {
        if (!$this->is_moderator) {
            throw new AccessDeniedException();
        }

        if ($category_id) {
            PageLayout::setTitle(_('Kategorie bearbeiten'));
            $category = Category::findOneBySQL("range_id = ? AND category_id = ?", [$this->range_id, $category_id]);

            if (!$category) {
                throw new AccessDeniedException();
            }
        } else {
            PageLayout::setTitle(_('Neue Kategorie anlegen'));
            $category = new Category();
        }

        $this->render_vue_app(
            Studip\VueApp::create('forum/categories/Edit')
                ->withProps([
                    'category' => $category->transformData()
                ])
        );
    }

    public function save_action($category_id = null)
    {
        if (!$this->is_moderator) {
            throw new AccessDeniedException();
        }

        CSRFProtection::verifyUnsafeRequest();

        if ($category_id) {
            $category = Category::findOneBySQL("range_id = ? AND category_id = ?", [$this->range_id, $category_id]);
            if (!$category) {
                throw new AccessDeniedException();
            }
        } else {
            $category = new Category();
            $category->range_id = $this->range_id;
        }

        $category->name = Request::get('name');
        $category->description = Request::get('description');
        $category->color = Request::get('color');

        $category->store();

        PageLayout::postSuccess(sprintf(_('Die Kategorie „%s“ wurde gespeichert.'), $category->name));

        $this->relocate('course/forum/categories');
    }

    public function delete_action($category_id)
    {
        if (!$this->is_moderator) {
            throw new AccessDeniedException();
        }

        $category = Category::findOneBySQL("range_id = ? AND category_id = ?", [$this->range_id, $category_id]);

        if (!$category) {
            throw new AccessDeniedException();
        }

        $category->delete();

        PageLayout::postSuccess(_('Die Kategorie wurde gelöscht.'));

        $this->relocate('course/forum/categories');
    }
}

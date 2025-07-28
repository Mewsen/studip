<?php
require_once 'BaseController.php';

class Course_Forum_RecentController extends Forum\BaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem('course/forum/topics');
    }

    public function index_action()
    {
        PageLayout::setTitle(_('Neueste Beiträge'));

        $this->render_vue_app(
            Studip\VueApp::create('forum/recent/Index')
                ->withProps([
                    'last_visit' => Request::int('last_visit')
                ])
        );
    }
}

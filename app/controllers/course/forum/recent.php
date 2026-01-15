<?php

class Course_Forum_RecentController extends Forum\BaseController
{
    public function before_filter(&$action, &$args): void
    {
        parent::before_filter($action, $args);

        Navigation::activateItem('course/forum/topics');
    }

    public function index_action(): void
    {
        PageLayout::setTitle(_('Neueste Beiträge'));

        $this->render_vue_app(
            Studip\VueApp::create('forum/recent/Index')
                ->withProps([
                    'lastVisit' => Request::int('last_visit')
                ])
        );
    }
}

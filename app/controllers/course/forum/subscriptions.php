<?php

class Course_Forum_SubscriptionsController extends Forum\BaseController
{
    public function before_filter(&$action, &$args): void
    {
        parent::before_filter($action, $args);

        if (!$this->user_id) {
            throw new LoginException();
        }

        Navigation::activateItem('course/forum/subscriptions');
    }

    public function index_action(): void
    {
        $this->render_vue_app(
            Studip\VueApp::create('forum/subscriptions/Index')
        );
    }
}

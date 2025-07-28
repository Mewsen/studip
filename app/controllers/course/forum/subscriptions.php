<?php
require_once 'BaseController.php';

class Course_Forum_SubscriptionsController extends Forum\BaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem('course/forum/subscriptions');
    }

    public function index_action()
    {
        $this->render_vue_app(
            Studip\VueApp::create('forum/subscriptions/Index')
        );
    }
}

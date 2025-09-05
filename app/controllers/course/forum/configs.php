<?php

class Course_Forum_ConfigsController extends Forum\BaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!$this->user_id) {
            throw new LoginException();
        }

        if (! $this->is_admin) {
            throw new AccessDeniedException();
        }
    }

    public function edit_action()
    {
        $config = Context::get()->getConfiguration();

        $this->render_vue_app(
            Studip\VueApp::create('forum/configs/Edit')
                ->withProps([
                    'config' => [
                        'moderator' => $config->FORUM_MODERATION_PERMISSION,
                        'categories_navigation' => $config->FORUM_HIDE_CATEGORIES_NAVIGATION
                    ]
                ])
        );
    }

    public function save_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $config = Context::get()->getConfiguration();

        $config->store('FORUM_MODERATION_PERMISSION', trim(Request::option('moderator')));

        $config->store('FORUM_HIDE_CATEGORIES_NAVIGATION', Request::bool('categories_navigation'));

        PageLayout::postSuccess(_('Die Einstellungen wurden gespeichert.'));

        $this->relocate('course/forum/topics');
    }
}

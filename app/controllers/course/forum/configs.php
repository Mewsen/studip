<?php

class Course_Forum_ConfigsController extends Forum\BaseController
{
    public function before_filter(&$action, &$args): void
    {
        parent::before_filter($action, $args);

        if (!$this->user_id) {
            throw new LoginException();
        }

        if (! $this->is_admin) {
            throw new AccessDeniedException();
        }
    }

    public function edit_action(): void
    {
        $this->config = Context::get()->getConfiguration();
    }

    public function save_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $this->config = Context::get()->getConfiguration();

        $this->config->store('FORUM_MODERATION_PERMISSION', trim(Request::option('forum_moderation_permission')));

        $this->config->store('FORUM_HIDE_CATEGORIES_NAVIGATION', Request::bool('forum_hide_categories_navigation'));

        PageLayout::postSuccess(_('Die Einstellungen wurden gespeichert.'));

        $this->relocate('course/forum/topics');
    }
}

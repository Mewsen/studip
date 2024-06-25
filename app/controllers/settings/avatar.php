<?php

class Settings_AvatarController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        // Ensure user is logged in
        $GLOBALS['auth']->login_if($action !== 'logout' && $GLOBALS['auth']->auth['uid'] === 'nobody');

        if (!$GLOBALS['perm']->have_profile_perm('user', User::findCurrent()->id)) {
            throw new AccessDeniedException(_('Sie dürfen dieses Profil nicht bearbeiten'));
        }
    }
    public function index_action()
    {
        PageLayout::setTitle(_('Profilbild anpassen'));
        Navigation::activateItem('/profile/edit/avatar');
        $this->user_id = User::findCurrent()->id;
        $avatar = Avatar::getAvatar($this->user_id);
        $this->avatar_url = $avatar->getURL(Avatar::NORMAL);
    }
}
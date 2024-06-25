<?php

class Institute_AvatarController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        // Ensure only admins gain access to this page
        if (!$GLOBALS['perm']->have_perm("admin")) {
            throw new AccessDeniedException();
        }
    }
    public function index_action($i_id = false)
    {
        //get ID from an open Institut
        $i_view = $i_id ?: Request::option('i_view', Context::getId());

        if (!$i_view) {
            Navigation::activateItem('/admin/institute/avatar');
            require_once 'lib/admin_search.inc.php';

            // This search just died a little inside, so it should be safe to
            // continue here but we nevertheless return just to be sure
            return;
        } elseif ($i_view === 'new') {
            closeObject();
            Navigation::activateItem('/admin/institute/create');
        } else {
            Navigation::activateItem('/admin/institute/avatar');
        }

        //  allow only inst-admin and root to view / edit
        if ($i_view && !$GLOBALS['perm']->have_studip_perm('admin', $i_view) && $i_view !== 'new') {
            throw new AccessDeniedException();
        }

        PageLayout::setTitle(Context::getHeaderLine() . ' - ' . _('Einrichtungsbild ändern'));
        $this->institute_id = Context::getId();
        $avatar = InstituteAvatar::getAvatar($this->institute_id);
        $this->avatar_url = $avatar->getURL(Avatar::NORMAL);
        
    }
}
<?php

class Course_AvatarController extends AuthenticatedController
{
    public function index_action()
    {
        $this->course_id = Context::getId();
        if (!$GLOBALS['perm']->have_studip_perm('tutor', $this->course_id)) {
            throw new AccessDeniedException(_("Sie haben keine Berechtigung diese " .
                "Veranstaltung zu verändern."));
        }
        PageLayout::setTitle(Context::getHeaderLine() . ' - ' . _('Veranstaltungsbild ändern'));
        Navigation::activateItem('/course/admin/avatar');
        $avatar = CourseAvatar::getAvatar($this->course_id);
        $this->avatar_url = $avatar->getURL(Avatar::NORMAL);
    }
}
<?php
require_once __DIR__ . '/LtiBaseController.php';

use LTI\LtiBaseController;

final class Enroll_Lti_ContentsController extends LtiBaseController
{
    protected $allow_nobody = false;

    public function index_action(): void
    {
        PageLayout::setTitle(_('Inhalt auswählen'));
        PageLayout::disableHeader();
        PageLayout::disableFooter();

        $this->callbackId = Request::get('callback_id');

        $callbackData = $this->validateCallbackData($this->callbackId);
        if ($callbackData['action'] !== 'deeplink_callback') {
            throw new AccessDeniedException('Invalid callback action.');
        }

        if (!$GLOBALS['perm']->have_perm('tutor')) {
            $this->errors[] = _('Sie haben nicht die Berechtigung, diese Aktion auszuführen.');
            return;
        }

        $this->courses = Course::findBySQL(
            "JOIN seminar_user USING(Seminar_id)
                WHERE user_id = :user_id AND seminar_user.status IN ('dozent', 'tutor')
                ORDER BY mkdate DESC, Name",
            [
                'user_id' => User::findCurrent()->id
            ]
        );
    }
}

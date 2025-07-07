<?php
/**
 * Settings_NotificataionController - Administration of all user notification
 * related settings
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       2.4
 */

require_once __DIR__ . '/settings.php';

class Settings_NotificationController extends Settings_SettingsController
{
    /**
     * Set up this controller
     *
     * @param String $action Name of the action to be invoked
     * @param Array  $args   Arguments to be passed to the action method
     *
     * @throws AccessDeniedException if notifications are not globally enabled
     *                               or if the user has no access to these
     *                               notifications (admin or root accounts).
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!Config::get()->getValue('MAIL_NOTIFICATION_ENABLE')) {
            $message = _('Die Benachrichtigungsfunktion wurde in den Systemeinstellungen nicht freigeschaltet.');
            throw new AccessDeniedException($message);
        }

        if (!auth()->isAuthenticated() || $GLOBALS['perm']->have_perm('admin')) {
            throw new AccessDeniedException();
        }

        PageLayout::setHelpKeyword('Basis.MyStudIPBenachrichtigung');
        PageLayout::setTitle(_('Benachrichtigung über neue Inhalte anpassen'));
        Navigation::activateItem('/profile/settings/notification');
    }

    /**
     * Display the notification settings of a user.
     */
    public function index_action(): void
    {
        $semesters = Semester::findAllVisible();
        $seminars = MyRealmModel::getCourses(
            array_key_first($semesters),
            array_key_last($semesters),
            ['deputies_enabled' => Config::get()->getValue('DEPUTIES_ENABLE')]
        );

        if (count($seminars) === 0) {
            $message = sprintf(_('Sie haben zur Zeit keine Veranstaltungen belegt. Bitte nutzen Sie %s<b>Veranstaltung suchen / hinzufügen</b>%s um sich für Veranstaltungen anzumdelden.'),
                '<a href="' . URLHelper::getLink('dispatch.php/search/courses') . '">', '</a>');
            PageLayout::postInfo($message);
            $this->render_nothing();
            return;
        }

        $this->render_vue_app(
            Studip\VueApp::create('my-courses/NotificationConfiguration')
                ->withProps([
                    'store-url' => $this->storeURL(),
                    'modules' => collect(
                        app(ModulesNotification::class)->registered_notification_modules
                    )->map(
                        fn(array $module, int $id): array => array_merge($module, ['id' => $id])
                    )->values(),
                    'notifications' => collect($this->user->course_notifications)->reduce(
                        function (array $carry, CourseMemberNotification $notification): array {
                            $carry[$notification->seminar_id] = array_map('intval', $notification->notification_data->getArrayCopy());
                            return $carry;
                        },
                        []
                    ),
                ])
                ->withVuexStore(
                    'MyCoursesStore',
                    'mycoursesnotificationstore',
                    app(MyCoursesHelper::class)->createVueAppData('')
                )
        );
    }

    /**
     * Stores the notification settings of a user.
     */
    public function store_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $course_ids = Request::optionArray('course_ids');
        $notifications = Request::getArray('notifications');

        $changed = 0;
        foreach ($course_ids as $course_id) {
            if (!isset($notifications[$course_id])) {
                $changed += CourseMemberNotification::deleteBySQL(
                    'user_id=? AND seminar_id=?',
                    [$this->user->user_id, $course_id]
                );
            } else {
                $notify = new CourseMemberNotification([$this->user->user_id, $course_id]);
                $notify->notification_data = $notifications[$course_id];
                $changed += $notify->store();
            }
        }

        if ($changed > 0) {
            PageLayout::postSuccess(_('Die Einstellungen wurden gespeichert.'));
        }

        $this->redirect('settings/notification');
    }
}

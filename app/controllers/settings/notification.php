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

require_once 'settings.php';

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

        if (!Config::get()->MAIL_NOTIFICATION_ENABLE) {
            $message = _('Die Benachrichtigungsfunktion wurde in den Systemeinstellungen nicht freigeschaltet.');
            throw new AccessDeniedException($message);
        }

        if (!$GLOBALS['auth']->is_authenticated() || $GLOBALS['perm']->have_perm('admin')) {
            throw new AccessDeniedException();
        }

        PageLayout::setHelpKeyword('Basis.MyStudIPBenachrichtigung');
        PageLayout::setTitle(_('Benachrichtigung über neue Inhalte anpassen'));
        Navigation::activateItem('/profile/settings/notification');
    }

    /**
     * Display the notification settings of a user.
     */
    public function index_action()
    {
        $group_field = 'sem_number';
        $semesters = Semester::findAllVisible();
        $seminars = MyRealmModel::getCourses(
            array_key_first($semesters),
            array_key_last($semesters),
            ['deputies_enabled' => Config::get()->DEPUTIES_ENABLE]
        );

        if (!count($seminars)) {
            $message = sprintf(_('Sie haben zur Zeit keine Veranstaltungen belegt. Bitte nutzen Sie %s<b>Veranstaltung suchen / hinzufügen</b>%s um sch für Veranstaltungen anzumdelden.'),
                '<a href="' . URLHelper::getLink('dispatch.php/search/courses') . '">', '</a>');
            PageLayout::postInfo($message);
            $this->render_nothing();
            return;
        }
        $modules_notification = new ModulesNotification();
        $enabled_modules = $modules_notification->registered_notification_modules;

        $groups = [];
        $my_sem = [];
        foreach ($seminars as $seminar) {
            $su = CourseMember::find([$seminar->id, User::findCurrent()->id]);

            if (!$su && Config::get()->DEPUTIES_ENABLE) {
                $su = Deputy::find([$seminar->id, User::findCurrent()->id]);
            }

            if (!$su) {
                continue;
            }

            $my_sem[$seminar['Seminar_id']] = [
                'obj_type'       => "sem",
                'sem_nr'         => $seminar->veranstaltungsnummer,
                'name'           => $seminar['Name'],
                'visible'        => $seminar['visible'],
                'gruppe'         => $su->gruppe,
                'sem_status'     => $seminar->status,
                'sem_number'     => Semester::getIndexById($seminar->start_semester->id),
                'sem_number_end' => Semester::getIndexById($seminar->end_semester->id ?? '') ?: '-1',
            ];
            if ($group_field) {
                fill_groups($groups, Semester::getIndexById($seminar->start_semester->id), [
                    'seminar_id' => $seminar['Seminar_id'],
                    'sem_nr'     => $seminar->veranstaltungsnummer,
                    'name'       => $seminar['Name'],
                    'gruppe'     => $su->gruppe,
                ]);
            }
        }

        correct_group_sem_number($groups, $my_sem);


        sort_groups($group_field, $groups);
        $group_names   = get_group_names($group_field, $groups);
        $notifications = $this->user->course_notifications;
        $open          = UserConfig::get($this->user->user_id)->MY_COURSES_OPEN_GROUPS;
        $checked       = [];
        foreach ($groups as $group_id => $group_members) {
            if (!in_array($group_id, $open)) {
                continue;
            }
            foreach ($group_members as $member) {
                $checked[$member['seminar_id']] = [];
                foreach ($enabled_modules as $index => $module) {
                    $notify = $notifications->findOneBy('seminar_id', $member['seminar_id']);
                    $checked[$member['seminar_id']][$index] = $notify && in_array($index, $notify->notification_data->getArrayCopy());
                }
                $checked[$member['seminar_id']]['all'] = count($enabled_modules) === count(array_filter($checked[$member['seminar_id']]));
            }
        }

        $this->modules       = $enabled_modules;
        $this->groups        = $groups;
        $this->group_names   = $group_names;
        $this->group_field   = 'sem_number';
        $this->open          = $open;
        $this->seminars      = $my_sem;
        $this->notifications = $notifications;
        $this->checked       = $checked;
    }

    /**
     * Stores the notification settings of a user.
     */
    public function store_action()
    {
        $this->check_ticket();
        foreach (Request::getArray('m_checked') as $course_id => $checked) {
            unset($checked['empty']);
            if (!count($checked)) {
                CourseMemberNotification::deleteBySQL('user_id=? AND seminar_id=?', [$this->user->user_id, $course_id]);
            } else {
                $notify = new CourseMemberNotification([$this->user->user_id, $course_id]);
                $notify->notification_data = array_keys($checked);
                $notify->store();
            }
        }
        PageLayout::postSuccess(_('Die Einstellungen wurden gespeichert.'));
        $this->redirect('settings/notification');
    }

    /**
     * Opens a specific area.
     *
     * @param String $id Id of the area to be opened
     */
    public function open_action($id)
    {
        $open = $this->config->MY_COURSES_OPEN_GROUPS;
        if (!in_array($id, $open)) {
            $open[] = $id;
        }
        $this->config->store('MY_COURSES_OPEN_GROUPS', $open);

        $this->redirect('settings/notification');
    }

    /**
     * Closes a specific area.
     *
     * @param String $id Id of the area to be closed
     */
    public function close_action($id)
    {
        $open = $this->config->MY_COURSES_OPEN_GROUPS;
        $open = array_diff($open, [$id]);
        $this->config->store('MY_COURSES_OPEN_GROUPS', $open);

        $this->redirect('settings/notification');
    }

    public function module_icon($area)
    {
        $mapping = [
            'documents'           => 'files',
            'elearning_interface' => 'learnmodule',
            'scm'                 => 'infopage',
            'votes'               => 'vote',
            'basic_data'          => 'seminar',
            'participants'        => 'persons',
            'plugins'             => 'plugin',
        ];

        return $mapping[$area] ?: $area;
    }
}

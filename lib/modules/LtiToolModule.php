<?php
/**
 * LtiToolModule.php - LTI consumer API for Stud.IP
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */
class LtiToolModule extends CorePlugin implements StudipModule, SystemPlugin, PrivacyPlugin
{
    /**
     * Initialize the LtiToolModule.
     */
    public function __construct()
    {
        parent::__construct();

        if ($GLOBALS['perm']->have_perm('root')) {
            Navigation::addItem('/admin/config/lti', new Navigation(_('LTI-Tools'), 'dispatch.php/admin/lti'));
        }

        NotificationCenter::on('UserDidDelete', function ($event, $user) {
            LtiGrade::deleteBySQL('user_id = ?', [$user->id]);
        });
        NotificationCenter::on('CourseDidDelete', function ($event, $course) {
            LtiDeployment::deleteBySQL('course_id = ?', [$course->id]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getIconNavigation($course_id, $last_visit, $user_id)
    {
        if ($user_id === 'nobody') {
            return null;
        }

        $changed = LtiDeployment::countBySQL('course_id = ? AND chdate > ?', [$course_id, $last_visit]);

        $icon = Icon::create('link-extern', $changed ? Icon::ROLE_NEW : Icon::ROLE_CLICKABLE);

        $navigation = new Navigation(_('LTI-Tools'), 'dispatch.php/course/lti');
        $navigation->setImage($icon);
        $navigation->setLinkAttributes(['title' => _('LTI-Tools')]);

        return $navigation;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabNavigation($course_id)
    {
        if ($GLOBALS['user']->id === 'nobody') {
            return [];
        }

        $grades = LtiDeployment::countBySQL('course_id = ?', [$course_id]);

        $navigation = new Navigation(_('LTI-Tools'));
        $navigation->setImage(Icon::create('link-extern', Icon::ROLE_INFO_ALT));
        $navigation->setActiveImage(Icon::create('link-extern', Icon::ROLE_INFO));
        $navigation->addSubNavigation('index', new Navigation(_('LTI-Tools'), 'dispatch.php/course/lti'));

        if ($grades) {
            $navigation->addSubNavigation('grades', new Navigation(_('Ergebnisse'), 'dispatch.php/course/lti/grades'));
        }

        return ['lti' => $navigation];
    }

    /**
     * {@inheritdoc}
     */
    public function getInfoTemplate($course_id)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function exportUserData(StoredUserData $storage)
    {
        $db = DBManager::get();

        $data = $db->fetchAll('SELECT * FROM lti_grade WHERE user_id = ?', [$storage->user_id]);
        $storage->addTabularData(_('LTI-Ergebnisse'), 'lti_grade', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return [
            'summary' => _('Anbindung von LTI-Tools'),
            'description' => _('Mit diesem Werkzeug können LTI-Tools eingebunden werden, '.
                               'sofern diese LTI in Version 1.0, 1.1 oder 1.3A unterstützen.'),
            'category' => _('Kommunikation und Zusammenarbeit'),
            'keywords' => implode(';', ['LTI', _('LTI-Tools'), _('E-Learning')]),
            'icon' => Icon::create('link-extern', Icon::ROLE_INFO),
            'icon_clickable' => Icon::create('link-extern'),
            'screenshots' => [
                'path' => 'assets/images/plus/screenshots/Lti',
                'pictures' => [
                    ['source' => 'Lti_tool_demo.jpg', 'title' => 'Beispiel für Wordpress-Einbindung']
                ]
            ],
            'displayname' => _('LTI-Tools')
        ];
    }
}

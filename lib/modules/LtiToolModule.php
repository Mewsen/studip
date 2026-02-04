<?php

use Lti\Grade;
use Lti\ResourceLink;

/**
 * LtiToolModule.php - LTI consumer API for Stud.IP
 *
 * @author      Elmar Ludwig
 * @author      Murtaza Sultani <sultani@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */
final class LtiToolModule extends CorePlugin implements StudipModule, SystemPlugin, PrivacyPlugin
{
    /**
     * Initialize the LtiToolModule.
     */
    public function __construct()
    {
        parent::__construct();

        if ($GLOBALS['perm']->have_perm('root')) {
            Navigation::addItem('/admin/config/lti', new Navigation(_('LTI-Registrierungen'), 'dispatch.php/admin/lti/registrations'));

            if (self::isToolSharingEnabled()) {
                Navigation::addItem('/admin/config/lti-publications', new Navigation(_('LTI-Veröffentlichungen'), 'dispatch.php/admin/lti/publications'));
            }
        }

        NotificationCenter::on('UserDidDelete', function ($event, $user) {
            Grade::deleteBySQL('user_id = ?', [$user->id]);
        });

        NotificationCenter::on('CourseDidDelete', function ($event, $course) {
            ResourceLink::deleteBySQL('course_id = ?', [$course->id]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getIconNavigation($course_id, $last_visit, $user_id): ?Navigation
    {
        if ($user_id === 'nobody') {
            return null;
        }

        $changed = ResourceLink::countBySQL('course_id = ? AND chdate > ?', [$course_id, $last_visit]);

        $icon = Icon::create('plugin', $changed ? Icon::ROLE_NEW : Icon::ROLE_CLICKABLE);

        $navigation = new Navigation(_('LTI'), 'dispatch.php/course/lti');
        $navigation->setImage($icon);
        $navigation->setLinkAttributes(['title' => _('LTI')]);

        return $navigation;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabNavigation($course_id): array
    {
        if ($GLOBALS['user']->id === 'nobody') {
            return ['lti' => null];
        }

        $grades = ResourceLink::countBySQL('course_id = ?', [$course_id]);

        $navigation = new Navigation(_('LTI'), 'dispatch.php/course/lti');
        $navigation->setImage(Icon::create('link-extern', Icon::ROLE_INFO_ALT));
        $navigation->setActiveImage(Icon::create('link-extern', Icon::ROLE_INFO));
        $navigation->addSubNavigation('index', new Navigation(_('LTI-Ressourcen'), 'dispatch.php/course/lti'));

        if ($grades) {
            $navigation->addSubNavigation('grades', new Navigation(_('Ergebnisse'), 'dispatch.php/course/lti/grades'));
        }

        if (self::isModerator($course_id)) {
            if (self::isToolSharingEnabled()) {
                $navigation->addSubNavigation('publications', new Navigation(_('LTI-Veröffentlichungen'), 'dispatch.php/admin/lti/publications'));
            }

            $navigation->addSubNavigation('registrations', new Navigation(_('LTI-Registrierungen'), 'dispatch.php/admin/lti/registrations'));
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

    public static function isToolSharingEnabled(): bool
    {
        return (bool) Config::get()->ENABLE_SHARING_COURSES_AS_LTI_TOOLS;
    }

    public static function isAdmin($userId = null): bool
    {
        return User::findCurrent()->auth_plugin === 'standard' && $GLOBALS['perm']->have_perm('root', $userId);
    }

    public static function isModerator($contextId, $userId = null): bool
    {
        return
            User::findCurrent()->auth_plugin === 'standard'
            && (self::isAdmin($userId) || $GLOBALS['perm']->have_studip_perm('tutor', $contextId, $userId));
    }

    /**
     * {@inheritdoc}
     */
    public function exportUserData(StoredUserData $storage): void
    {
        $data = DBManager::get()->fetchAll("SELECT * FROM `lti_grade` WHERE `user_id` = ?", [$storage->user_id]);
        $storage->addTabularData(_('LTI-Ergebnisse'), 'lti_grade', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): array
    {
        return [
            'summary' => _('Anbindung von LTI-Tools'),
            'description' => _('Mit diesem Werkzeug können LTI-Tools eingebunden werden, '.
                               'sofern diese LTI in Version 1.0, 1.1 oder 1.3A unterstützen.'),
            'category' => _('Kommunikation und Zusammenarbeit'),
            'keywords' => implode(';', ['LTI', _('LTI-Tools'), _('E-Learning')]),
            'icon' => Icon::create('plugin', Icon::ROLE_INFO),
            'icon_clickable' => Icon::create('plugin'),
            'screenshots' => [
                'path' => 'assets/images/plus/screenshots/Lti',
                'pictures' => [
                    0 => ['source' => 'LTI_Tool_hinzufuegen.jpg', 'title' => _('LTI-Tool hinzufügen')],
                ]
            ],
            'displayname' => _('LTI')
        ];
    }
}

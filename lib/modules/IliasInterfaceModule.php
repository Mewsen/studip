<?php
/**
 * Ilias Interface - navigation and meta data
 *
 * @author    André Noack <noack@data-quest.de>
 * @copyright 2019 Stud.IP Core-Group
 * @license   GPL version 2 or any later version
 * @since     4.3
 */

class IliasInterfaceModule extends CorePlugin implements StudipModuleExtended, SystemPlugin
{
    use IconNavigationTrait;

    public function __construct()
    {
        parent::__construct();
        if (Config::get()->ILIAS_INTERFACE_ENABLE) {
            $ilias_interface_config = Config::get()->ILIAS_INTERFACE_BASIC_SETTINGS;
            $workgroups = false;
            $learning_objects = false;
            foreach (Config::get()->ILIAS_INTERFACE_SETTINGS as $ilias_index => $ilias_config) {
                if (!empty($ilias_config['is_active'])) {
                    if (!empty($ilias_config['workgroup_category']) && User::findCurrent()->hasPermissionLevel('tutor')) {
                        $workgroups = true;
                    }
                    if (!empty($ilias_interface_config['create_objects'])
                        && !empty($ilias_interface_config['create_category'])
                        && User::findCurrent()->hasPermissionLevel($ilias_config['author_perm'])) {
                        $learning_objects = true;
                    }
                }
            }

            if (User::findCurrent()->hasPermissionLevel('root')) {
                Navigation::addItem('/admin/config/ilias_interface',
                    new Navigation(_('ILIAS-Schnittstelle'), 'dispatch.php/admin/ilias_interface'));
            }
            if (User::findCurrent()->hasPermissionLevel('autor')) {
                $ilias = new Navigation(_('ILIAS'), 'dispatch.php/my_ilias_accounts/my_courses');
                $ilias->setImage(Icon::create('ilias'));
                $ilias->setDescription(_('Schnittstelle zu ILIAS'));
                $ilias->addSubNavigation(
                    'my_courses',
                    new Navigation($workgroups ? _('Meine Kurse und Arbeitsbereiche') : _('Meine Kurse'), 'dispatch.php/my_ilias_accounts/my_courses')
                );
                if (User::findCurrent()->hasPermissionLevel('root') || !empty($ilias_interface_config['show_tools_page'])) {
                    $ilias->addSubNavigation(
                        'my_accounts',
                        new Navigation($learning_objects ? _('Meine Lernobjekte und Accounts') : _('Meine Accounts'), 'dispatch.php/my_ilias_accounts')
                    );
                }
                Navigation::addItem('/contents/my_ilias_accounts', $ilias);
            }
        }
    }

    public function isActivatableForContext(Range $context)
    {
        return Config::get()->ILIAS_INTERFACE_ENABLE && $context->getRangeType() === 'course';
    }

    public function getInfoTemplate($course_id)
    {
        return null;
    }

    public function getManyIconNavigation(array $course_ids, ?string $user_id = null): array
    {
        // TODO Test this function
        if (!Config::get()->ILIAS_INTERFACE_ENABLE) {
            return [];
        }

        $results = DBManager::get()->fetchAll(
            "SELECT a.object_id,
                       COUNT(IF(a.module_type != 'crs', module_id, NULL)) AS count_modules,
                       COUNT(IF(a.module_type = 'crs', module_id, NULL)) AS count_courses,
                       COUNT(IF((chdate > IFNULL(b.visitdate, :threshold) AND a.module_type != 'crs'), module_id, NULL)) AS neue
                FROM object_contentmodules AS a
                LEFT JOIN object_user_visits AS b
                  ON b.object_id = a.object_id
                     AND b.user_id = :user_id
                     AND b.plugin_id = :plugin_id
                WHERE a.object_id IN (:course_ids)
                GROUP BY a.object_id",
            [
                ':user_id' => $user_id,
                ':course_ids' => $course_ids,
                ':threshold' => object_get_visit_threshold(),
                ':plugin_id' => $this->getPluginId(),
            ]
        );

        $navs = [];
        foreach ($results as $result) {
            $title = CourseConfig::get($result['object_id'])->getValue('ILIAS_INTERFACE_MODULETITLE');
            $nav = new Navigation($title, 'dispatch.php/course/ilias_interface/index');
            if ($result['neue']) {
                $nav->setImage(Icon::create('learnmodule', Icon::ROLE_ATTENTION));
                $nav->setLinkAttributes([
                    'title' => sprintf(
                        ngettext(
                            '%1$d Lernobjekt, %2$d neues',
                            '%1$d Lernobjekte, %2$d neue',
                            $result['count_modules']
                        ),
                        $result['count_modules'],
                        $result['neue']
                    )
                ]);
            } elseif ($result['count_modules']) {
                $nav->setImage(Icon::create('learnmodule'));
                $nav->setLinkAttributes([
                    'title' => sprintf(
                        ngettext(
                            '%d Lernobjekt',
                            '%d Lernobjekte',
                            $result['count_modules']
                        ),
                        $result['count_modules']
                    )
                ]);
            } elseif ($result['count_courses']) {
                $nav->setImage(Icon::create('learnmodule'));
                $nav->setLinkAttributes([
                    'title' => sprintf(
                        ngettext(
                            '%d ILIAS-Kurs',
                            '%d ILIAS-Kurse',
                            $result['count_courses']
                        ),
                        $result['count_courses']
                    )
                ]);
            }
            $navs[$result['object_id']] = $nav;
        }
        return $navs;
    }

    public function getTabNavigation($course_id)
    {
        if (!Config::get()->ILIAS_INTERFACE_ENABLE) {
            return null;
        }
        $ilias_interface_config = Config::get()->ILIAS_INTERFACE_BASIC_SETTINGS;
        if (count($ilias_interface_config) === 0) {
            return null;
        }

        $moduletitle = Config::get()->ILIAS_INTERFACE_MODULETITLE;
        if (!empty($ilias_interface_config['edit_moduletitle'])) {
            $moduletitle = CourseConfig::get($course_id)->ILIAS_INTERFACE_MODULETITLE;
        }

        $navigation = new Navigation($moduletitle);
        $navigation->setImage(Icon::create('learnmodule', Icon::ROLE_INFO_ALT));
        $navigation->setActiveImage(Icon::create('learnmodule', Icon::ROLE_INFO));
        if (
            $GLOBALS['perm']->have_studip_perm('tutor', $course_id)
            || (
                $GLOBALS['perm']->have_studip_perm('autor', $course_id)
                && IliasObjectConnections::isCourseConnected($course_id)
            )
        ) {
            if (get_object_type($course_id, ['inst'])) {
                if (!empty($ilias_interface_config['create_objects'])) {
                    $navigation->addSubNavigation('view', new Navigation(_('Lernobjekte dieser Einrichtung'), 'dispatch.php/course/ilias_interface/index/' . $course_id));
                } else {
                    $navigation->addSubNavigation('view', new Navigation(_('ILIAS-Kurs zu dieser Einrichtung'), 'dispatch.php/course/ilias_interface/index/' . $course_id));
                }
            } else {
                if (!empty($ilias_interface_config['create_objects'])) {
                    $navigation->addSubNavigation('view', new Navigation(_('Lernobjekte dieser Veranstaltung'), 'dispatch.php/course/ilias_interface/index/' . $course_id));
                } else {
                    $navigation->addSubNavigation('view', new Navigation(_('ILIAS-Kurs zu dieser Veranstaltung'), 'dispatch.php/course/ilias_interface/index/' . $course_id));
                }
            }
        }

        return ['ilias_interface' => $navigation];
    }

    /**
     * @see StudipModule::getMetadata()
     */
    public function getMetadata()
    {
        return [
            'summary'          => _('Zugang zu extern erstellten ILIAS-Lernobjekten'),
            'description'      => _('Über diese Schnittstelle ist es möglich, Lernobjekte aus ' .
                'einer ILIAS-Installation (ILIAS-Version >= 5.3.8) in Stud.IP zur Verfügung ' .
                'zu stellen. Lehrende haben die Möglichkeit, in ' .
                'ILIAS Selbstlerneinheiten zu erstellen und in Stud.IP bereit zu stellen.'),
            'displayname'      => _('ILIAS-Schnittstelle'),
            'category'         => _('Inhalte und Aufgabenstellungen'),
            'keywords'         => _('Einbindung von ILIAS-Lernobjekten;
                            Zugang zu ILIAS;
                            Aufgaben- und Test-Erstellung'),
            'icon'             => Icon::create('learnmodule', Icon::ROLE_INFO),
            'icon_clickable' => Icon::create('learnmodule'),
            'descriptionshort' => _('Zugang zu extern erstellten ILIAS-Lernobjekten'),
            'descriptionlong'  => _('Über diese Schnittstelle ist es möglich, Lernobjekte aus ' .
                'einer ILIAS-Installation (> 5.3.8) in Stud.IP zur Verfügung ' .
                'zu stellen. Lehrende haben die Möglichkeit, in ' .
                'ILIAS Selbstlerneinheiten zu erstellen und in Stud.IP bereit zu stellen.'),
        ];
    }
}

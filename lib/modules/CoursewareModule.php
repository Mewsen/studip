<?php

use Courseware\Instance;
use Courseware\StructuralElement;

class CoursewareModule extends CorePlugin implements SystemPlugin, StudipModuleExtended
{
    use IconNavigationTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        NotificationCenter::on('CourseDidDelete', function ($event, $course) {
            $last_element_configs = \ConfigValue::findBySQL('field = ? AND value LIKE ?', [
                'COURSEWARE_LAST_ELEMENT',
                '%' . $course->id . '%',
            ]);
            foreach ($last_element_configs as $config) {
                $arr = json_decode($config->value, true);
                $arr = array_filter(
                    $arr,
                    function ($key) use ($course) {
                        return $key !== $course->id;
                    },
                    ARRAY_FILTER_USE_KEY
                );
                \UserConfig::get($config->range_id)->store('COURSEWARE_LAST_ELEMENT', $arr);
            }
        });
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getInfoTemplate($courseId)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabNavigation($courseId)
    {
        if ($GLOBALS['user']->id === 'nobody') {
            return [];
        }

        $navigation = new Navigation(
            _('Courseware'),
            URLHelper::getURL('dispatch.php/course/courseware/?cid=' . $courseId)
        );
        $navigation->setImage(Icon::create('courseware', Icon::ROLE_INFO_ALT));
        $navigation->addSubNavigation(
            'shelf',
            new Navigation(_('Lernmaterialien'), 'dispatch.php/course/courseware/?cid=' . $courseId)
        );
        $navigation->addSubNavigation(
            'unit',
            new Navigation(_('Inhalt'), 'dispatch.php/course/courseware/courseware?cid=' . $courseId)
        );
        $navigation->addSubNavigation(
            'activities',
            new Navigation(_('Aktivitäten'), 'dispatch.php/course/courseware/activities?cid=' . $courseId)
        );
        $navigation->addSubNavigation(
            'tasks',
            new Navigation(_('Aufgaben'), 'dispatch.php/course/courseware/tasks?cid=' . $courseId)
        );
        $navigation->addSubNavigation(
            'comments',
            new Navigation(_('Kommentare und Anmerkungen'), 'dispatch.php/course/courseware/comments_overview?cid=' . $courseId)
        );

        return ['courseware' => $navigation];
    }

    public function getManyIconNavigation(array $course_ids, ?string $user_id = null): array
    {
        if ($user_id === 'nobody') {
            return [];
        }

        $results = DBManager::get()->fetchGrouped(
            "SELECT elem.range_id,
                    COUNT(IF((blocks.chdate > IFNULL(ouv.visitdate, :threshold) AND blocks.editor_id != :user_id), elem.id, NULL)) AS neue
                FROM `cw_structural_elements` AS elem
                INNER JOIN `cw_containers` as container ON (elem.id = container.structural_element_id)
                INNER JOIN `cw_blocks` as blocks ON (container.id = blocks.container_id)
                LEFT JOIN object_user_visits AS ouv
                  ON ouv.object_id = elem.range_id
                     AND ouv.user_id = :user_id
                     AND ouv.plugin_id = :plugin_id
                WHERE elem.range_type = 'course'
                AND elem.range_id IN (:range_ids)
                AND blocks.payload != ''
                AND blocks.editor_id != :user_id
                GROUP BY elem.range_id",
            [
                'user_id' => $user_id,
                'range_ids' => $course_ids,
                'threshold' => object_get_visit_threshold(),
                'plugin_id' => $this->getPluginId(),
            ]
        );

        $navs = [];
        foreach ($course_ids as $course_id) {
            $nav = new Navigation(_('Courseware'), 'dispatch.php/course/courseware');
            $nav->setImage(Icon::create('courseware'));
            $nav->setLinkAttributes(['title' => _('Courseware'),]);

            if (!empty($results[$course_id]['neue'])) {
                $text = sprintf(
                    ngettext(
                        '%u neue Seite',
                        '%u neue Seiten',
                        $results[$course_id]['neue']
                    ),
                    $results[$course_id]['neue']
                );
                $nav->setImage(Icon::create('courseware', Icon::ROLE_ATTENTION));
                $nav->setLinkAttributes(['title' => $text]);
                $nav->setBadgeNumber($results[$course_id]['neue']);
            }

            $navs[$course_id] = $nav;
        }
        return $navs;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return [
            'summary' => _('Lernmaterialien erstellen, verteilen und erleben'),
            'description' => _('Mit Courseware können Sie interaktive, multimediale Lernmaterialien erstellen und nutzen. Diese Materialien lassen sich hierarchisch strukturieren und können aus Texten, Videos, Aufgaben, Kommunikationselementen sowie einer Vielzahl weiterer Bausteine bestehen. Fertige Lernmaterialien können exportiert und in andere Kurse oder Installationen importiert werden. Courseware eignet sich nicht nur für digitale Formate, sondern auch, um klassische Präsenzveranstaltungen durch Online-Anteile zu ergänzen. Formate wie integriertes Lernen (Blended Learning) lassen sich mit Courseware optimal umsetzen. Darüber hinaus ermöglicht Courseware E-Portfolio-Arbeiten, bei denen Lernende ihre Ergebnisse dokumentieren können, sowie Peer-Reviews zur kollaborativen Bewertung. Kollaboratives Lernen wird durch die Vergabe von Schreibrechten und den Einsatz von Courseware in Studiengruppen unterstützt.'),
            'displayname' => _('Courseware'),
            'category' => _('Lehr- und Lernorganisation'),
            'icon' => Icon::create('courseware', 'info'),
            'icon_clickable' => Icon::create('courseware'),
            'screenshots' => [
                'path' => 'assets/images/plus/screenshots/Courseware',
                'pictures' => [
                    0 => ['source' => 'Uebersicht_Lernmaterialien.jpg', 'title' => _('Übersicht Lernmaterialien')],
                    1 => ['source' => 'Lernmaterial_Inhalt.jpg', 'title' => _('Lernmaterial Inhalt')],
                    2 => ['source' => 'Lernmaterial_Inhaltsverzeichnis.jpg', 'title' => _('Inhaltsverzeichnis')],
                    3 => ['source' => 'Inhalt_bearbeiten.jpg', 'title' => _('Inhalt bearbeiten')],
                    4 => ['source' => 'Einstellung_Rechte_und_Sichtbarkeit.jpg', 'title' => _('Rechte und Sichtbarkeit')],
                    5 => ['source' => 'Courseware_Aufgabe_mit_Peer_Review.jpg', 'title' => _('Courseware Aufgabe mit Peer-Review')]
                ],
            ],
        ];
    }

    public function isActivatableForContext(Range $context)
    {
        return $context->getRangeType() === 'course';
    }
}

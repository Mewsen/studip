<?php

use Lti\Publication;
use Ramsey\Uuid\Uuid;
use Lti\Config as LtiConfig;
use Studip\Lti\Enum\LtiVersion;
use Studip\Lti\Enum\ConfigurableType;
use Studip\Lti\Enum\PublicationStatus;
use Studip\Lti\Controller\AdminBaseController;

class Admin_Lti_PublicationsController extends AdminBaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!$this->isToolSharingEnabled) {
            throw new AccessDeniedException();
        }

        if ($this->range_id) {
            Navigation::activateItem('/course/lti/publications');
        } else {
            Navigation::activateItem('/admin/config/lti-publications');
        }

        $this->buildPublicationsSidebar();
    }

    public function index_action(): void
    {
        PageLayout::setTitle(_('LTI-Veröffentlichungen'));

        $sqlQuery = [
            "`range_id` = :range_id ORDER BY `mkdate`, `name`",
            [
                'range_id' => $this->range_id
            ]
        ];

        if ($GLOBALS['perm']->have_perm('root') && !$this->range_id) {
            $sqlQuery = [
                "TRUE ORDER BY `mkdate`, `name`"
            ];
        }

        $publications = Publication::findBySQL(...$sqlQuery);

        $this->render_vue_app(
            Studip\VueApp::create('lti/publications/Index')
                ->withProps([
                    'publications' => array_map(fn ($p) => $p->transformData(['members']), $publications)
                ])
        );
    }

    public function show_action(Publication $publication): void
    {
        PageLayout::setTitle(_('Konfiguration der LTI-Veröffentlichung anzeigen'));

        $this->render_vue_app(
            Studip\VueApp::create('lti/publications/Show')
                ->withProps([
                    'publication' => $publication->transformData(['members'])
                ])
        );
    }

    public function create_action(): void
    {
        PageLayout::setTitle(_('Neue Veröffentlichung anlegen'));

        $this->render_vue_app(
            Studip\VueApp::create('lti/publications/Create')
        );
    }

    public function store_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $publication = Publication::create([
            'name' => Request::get('name'),
            'version' => Request::get('version', LtiVersion::Lti1p3a->value),
            'status' => PublicationStatus::fromBoolean(Request::bool('status', true)),
            'publication_key' => Uuid::uuid4()->toString(),
            'range_id' => $this->range_id,
            'user_id' => User::findCurrent()->id,
        ]);

        $this->syncPublicationConfigs($publication->id);

        PageLayout::postSuccess(
            sprintf(
                _('Die LTI-Veröffentlichung „%s“ wurde gespeichert.'),
                htmlReady($publication->name)
            )
        );

        $this->redirect('admin/lti/publications');
    }

    public function edit_action(Publication $publication): void
    {
        PageLayout::setTitle(_('LTI-Veröffentlichung bearbeiten'));

        $this->render_vue_app(
            Studip\VueApp::create('lti/publications/Edit')
                ->withProps([
                    'publication' => $publication->transformData()
                ])
        );
    }

    public function update_action(Publication $publication): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $publication->setData([
            'name' => Request::get('name'),
            'version' => Request::get('version', $publication->version),
            'status' => PublicationStatus::fromBoolean(Request::bool('status', PublicationStatus::get($publication->status)['value'])),
        ]);

        $publication->store();

        $this->syncPublicationConfigs($publication->id);

        PageLayout::postSuccess(
            sprintf(
                _('Die LTI-Veröffentlichung „%s“ wurde gespeichert.'),
                htmlReady($publication->name)
            )
        );

        $this->redirect('admin/lti/publications');
    }

    public function delete_action(Publication $publication): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $publicationName = $publication->name;
        $publication->delete();

        PageLayout::postSuccess(
            sprintf(
                _('Die LTI-Veröffentlichung „%s“ wurde gelöscht.'),
                htmlReady($publicationName)
            )
        );

        $this->redirect('admin/lti/publications');
    }

    public function show_members_action(Publication $publication): void
    {
        PageLayout::setTitle(_('Teilnehmenden der LTI-Veröffentlichung anzeigen'));

        $this->render_vue_app(
            Studip\VueApp::create('lti/publications/ShowMembers')
                ->withProps([
                    'publication' => $publication->transformData(['members'])
                ])
        );
    }

    private function extractPublicationConfigsFromRequest(): array
    {
        return [
            [
                'name' => 'enrollment_deadline',
                'value' => strtotime(Request::get('enrollment_deadline'))
            ],
            [
                'name' => 'start_date',
                'value' => strtotime(Request::get('start_date'))
            ],
            [
                'name' => 'end_date',
                'value' => strtotime(Request::get('end_date'))
            ],
            [
                'name' => 'maximum_enrolled_users',
                'value' => Request::int('maximum_enrolled_users')
            ],
            [
                'name' => 'instructor_role',
                'value' => Request::get('instructor_role', 'dozent')
            ],
            [
                'name' => 'student_role',
                'value' => Request::get('student_role', 'autor')
            ],
            [
                'name' => 'provisioning_mode_instructor',
                'value' => Request::int('provisioning_mode_instructor', 2)
            ],
            [
                'name' => 'provisioning_mode_student',
                'value' => Request::int('provisioning_mode_student', 1)
            ]
        ];
    }

    private function syncPublicationConfigs($publicationId): void
    {
        foreach ($this->extractPublicationConfigsFromRequest() as $config) {
            if (empty($config['value'])) {
                LtiConfig::deleteBySQL(
                    "configurable_id = :configurable_id AND configurable_type = :configurable_type AND name = :name",
                    [
                        'configurable_id' => $publicationId,
                        'configurable_type' => ConfigurableType::Publication->value,
                        'name' => strtolower($config['name'])
                    ]
                );
                continue;
            }

            LtiConfig::updateOrCreate(
                [
                    'configurable_id' => $publicationId,
                    'configurable_type' => ConfigurableType::Publication->value,
                    'name' => strtolower($config['name'])
                ],
                [
                    'value' => $config['value']
                ]
            );
        }
    }
}

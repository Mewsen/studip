<?php
require_once __DIR__ . '/AdminBaseController.php';

use LTI\AdminBaseController;
use Lti\Publication;
use Lti\PublicationConfig;
use Ramsey\Uuid\Uuid;

class Admin_Lti_PublicationsController extends AdminBaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

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

        if ($GLOBALS['perm']->have_perm('root')) {
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
            'version' => Request::get('version', '1.3a'),
            'status' => Request::bool('status', true),
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
            'status' => Request::bool('status', $publication->status)
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
                'value' => strtotime(Request::get('enrollment_deadline')),
                'is_delete' => Request::bool('enrollment_deadline')
            ],
            [
                'name' => 'start_date',
                'value' => strtotime(Request::get('start_date')),
                'is_delete' => Request::bool('start_date')
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
                'name' => 'dozent_role',
                'value' => Request::get('dozent_role')
            ],
            [
                'name' => 'autor_role',
                'value' => Request::get('autor_role')
            ]
        ];
    }

    private function syncPublicationConfigs($publicationId): void
    {
        foreach ($this->extractPublicationConfigsFromRequest() as $config) {
            if (empty($config['value'])) {
                PublicationConfig::deleteBySQL(
                    "publication_id = :publication_id AND name = :name",
                    [
                        'publication_id' => $publicationId,
                        'name' => strtolower($config['name'])
                    ]
                );
                continue;
            }

            PublicationConfig::updateOrCreate(
                [
                    'publication_id' => $publicationId,
                    'name' => strtolower($config['name'])
                ],
                [
                    'value' => $config['value']
                ]
            );
        }
    }
}

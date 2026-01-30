<?php
require_once __DIR__ . '/AdminBaseController.php';

use LTI\AdminBaseController;
use Lti\Deployment;
use Lti\Registration;
use Lti\RegistrationConfig;
use Ramsey\Uuid\Uuid;
use Studip\Lti\Enum\RegistrationStatus;
use Studip\LTI13a\PlatformManager;
use Studip\LTI13a\ToolManager;
use Studip\Markup;

class Admin_Lti_RegistrationsController extends AdminBaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if ($this->range_id) {
            Navigation::activateItem('/course/lti/registrations');
        } else {
            Navigation::activateItem('/admin/config/lti');
        }

        PageLayout::setTitle(_('LTI-Registrierungen'));

        $this->ltiRole = Request::get('role', 'tool');

        $this->buildRegistrationsSidebar();
    }

    public function index_action(): void
    {
        $sqlQuery = [
            "`role`= :role AND `range_id` IN (:range_ids) ORDER BY `mkdate`, `name`",
            [
                'role' => $this->ltiRole,
                'range_ids' => [$this->range_id, 'global']
            ]
        ];

        if ($GLOBALS['perm']->have_perm('root') && !$this->range_id) {
            $sqlQuery = [
                "`role`= :role ORDER BY `mkdate`, `name`",
                [
                    'role' => $this->ltiRole
                ]
            ];
        }

        $registrations = Registration::findBySQL(...$sqlQuery);

        $this->render_vue_app(
            Studip\VueApp::create('lti/registrations/Index')
                ->withProps([
                    'role' => $this->ltiRole,
                    'registrations' => array_map(fn ($r) => $r->transformData(['deployments']), $registrations)
                ])
        );
    }

    public function show_action(Registration $registration): void
    {
        $this->render_vue_app(
            Studip\VueApp::create('lti/registrations/Show')
                ->withProps([
                    'registration' => $registration->transformData(['deployments'])
                ])
        );
    }

    public function create_action(): void
    {
        if ($this->ltiRole === 'tool') {
            PageLayout::setTitle(_('Neues LTI-Tool registrieren'));
        } elseif ($this->ltiRole === 'platform') {
            PageLayout::setTitle(_('Neues LTI-Platform registrieren'));
        }

        $this->render_vue_app(
            Studip\VueApp::create('lti/registrations/Create')
                ->withProps([
                    'role' => $this->ltiRole
                ])
        );
    }

    public function store_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $registration = Registration::create([
            'version' => Request::get('version', '1.3a'),
            'role' => Request::get('role', 'tool'),
            'name' => Request::get('name'),
            'description' => Markup::purifyHtml(Markup::markAsHtml(Request::get('description'))),
            'status' => RegistrationStatus::fromBoolean(Request::bool('status')),
            'range_id' => $this->range_id ?? 'global'
        ]);

        $this->syncRegistrationConfigs($registration->id);

        if ($registration->role === 'tool') {
            Deployment::create([
                'is_default' => 1,
                'name' => _('Standard-Deployment'),
                'registration_id' => $registration->id,
                'deployment_key' => bin2hex(random_bytes(6)),
                'client_id' => Uuid::uuid4()->toString()
            ]);
        }

        PageLayout::postSuccess(
            sprintf(
                _('Die LTI-Registrierung „%s“ wurde gespeichert.'),
                htmlReady($registration->name)
            )
        );

        $this->redirect('admin/lti/registrations', ['role' => $registration->role]);
    }

    public function edit_action(Registration $registration): void
    {
        if ($this->ltiRole === 'tool') {
            PageLayout::setTitle(_('LTI-Tool bearbeiten'));
        } elseif ($this->ltiRole === 'platform') {
            PageLayout::setTitle(_('LTI-Platform bearbeiten'));
        }

        $this->render_vue_app(
            Studip\VueApp::create('lti/registrations/Edit')
                ->withProps([
                    'registration' => $registration->transformData()
                ])
        );
    }

    public function update_action(Registration $registration): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $registration->setData([
            'version' => Request::get('version', $registration->version),
            'role' => Request::get('role', $registration->role),
            'name' => Request::get('name'),
            'description' => Markup::purifyHtml(Markup::markAsHtml(Request::get('description'))),
            'status' => RegistrationStatus::fromBoolean(Request::bool('status'))
        ]);

        $registration->store();

        $this->syncRegistrationConfigs($registration->id);

        PageLayout::postSuccess(
            sprintf(
                _('Die LTI-Registrierung „%s“ wurde gespeichert.'),
                htmlReady($registration->name)
            )
        );

        $this->redirect('admin/lti/registrations', ['role' => $registration->role]);
    }

    public function delete_action(Registration $registration): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $registrationName = $registration->name;
        $registrationRole = $registration->role;
        $registration->delete();

        PageLayout::postSuccess(
            sprintf(
                _('Die LTI-Registrierung „%s“ wurde gelöscht.'),
                htmlReady($registrationName)
            )
        );

        $this->redirect('admin/lti/registrations', ['role' => $registrationRole]);
    }

    public function platform_data_action(): void
    {
        PageLayout::setTitle(_('LTI-Platform Daten'));

        $platformData = PlatformManager::getPlatformConfiguration();

        $this->render_vue_app(
            Studip\VueApp::create('lti/registrations/ShowPlatformData')
                ->withProps([
                    'platformData' => [
                        'id' => $platformData->getIdentifier(),
                        'name' => $platformData->getName(),
                        'audience' => $platformData->getAudience(),
                        'auth_login_url' => $platformData->getOidcAuthenticationUrl(),
                        'token_url' => $platformData->getOAuth2AccessTokenUrl(),
                        'keyset_url' => PlatformManager::getJwksUrl(),
                        'public_key' => PlatformManager::getPublicKey()->getContent(),
                    ]
                ])
        );
    }

    public function tool_data_action(): void
    {
        PageLayout::setTitle(_('LTI-Tool Daten'));

        $toolData = ToolManager::getToolConfiguration();

        $this->render_vue_app(
            Studip\VueApp::create('lti/registrations/ShowToolData')
                ->withProps([
                    'toolData' => [
                        'id' => $toolData->getIdentifier(),
                        'name' => $toolData->getName(),
                        'audience' => $toolData->getAudience(),
                        'auth_init_url' => $toolData->getOidcInitiationUrl(),
                        'launch_url' => $toolData->getLaunchUrl(),
                        'deep_linking_url' => $toolData->getDeepLinkingUrl(),
                        'keyset_url' => ToolManager::getJwksUrl(),
                        'public_key' => ToolManager::getPublicKey()->getContent(),
                    ]
                ])
        );
    }

    private function extractRegistrationConfigsFromRequest(): array
    {
        $common = [
            [
                'name' => 'terms_of_use_url',
                'value' => Request::get('terms_of_use_url')
            ],
            [
                'name' => 'privacy_policy_url',
                'value' => Request::get('privacy_policy_url')
            ],
            [
                'name' => 'data_protection_notes',
                'value' => Markup::purifyHtml(Markup::markAsHtml(Request::get('data_protection_notes')))
            ]
        ];

        $toolCommon = [
            ...$common,
            [
                'name' => 'audience',
                'value' => Request::get('audience')
            ],
            [
                'name' => 'launch_url',
                'value' => Request::get('launch_url')
            ],
            [
                'name' => 'send_lis_person',
                'value' => Request::bool('send_lis_person')
            ],
            [
                'name' => 'custom_parameters',
                'value' => Request::get('custom_parameters')
            ],
            [
                'name' => 'launch_container',
                'value' => Request::get('launch_container')
            ]
        ];

        if (Request::get('version') === '1.3a') {
            if (Request::get('role') === 'tool') {
                return [
                    ...$toolCommon,
                    [
                        'name' => 'auth_init_url',
                        'value' => Request::get('auth_init_url')
                    ],
                    [
                        'name' => 'deep_linking_url',
                        'value' => Request::get('deep_linking_url')
                    ],
                    [
                        'name' => 'token_url',
                        'value' => Request::get('token_url')
                    ],
                    [
                        'name' => 'key_type',
                        'value' => Request::get('key_type')
                    ],
                    [
                        'name' => 'jwks_url',
                        'value' => Request::get('jwks_url')
                    ],
                    [
                        'name' => 'jwks_key_id',
                        'value' => Request::get('jwks_key_id')
                    ],
                    [
                        'name' => 'public_key',
                        'value' => Request::get('public_key')
                    ]
                ];
            }

            if (Request::get('role') === 'platform') {
                return [
                    ...$common,
                    [
                        'name' => 'issuer',
                        'value' => Request::get('issuer')
                    ],
                    [
                        'name' => 'auth_login_url',
                        'value' => Request::get('auth_login_url')
                    ],
                    [
                        'name' => 'token_url',
                        'value' => Request::get('token_url')
                    ],
                    [
                        'name' => 'key_type',
                        'value' => Request::get('key_type')
                    ],
                    [
                        'name' => 'jwks_url',
                        'value' => Request::get('jwks_url')
                    ],
                    [
                        'name' => 'public_key',
                        'value' => Request::get('public_key')
                    ]
                ];
            }
        }

        if (Request::get('version') === '1.1') {
            return [
                ...$toolCommon,
                [
                    'name' => 'consumer_key',
                    'value' => Request::get('consumer_key')
                ],
                [
                    'name' => 'consumer_secret',
                    'value' => Request::get('consumer_secret')
                ],
                [
                    'name' => 'oauth_signature_method',
                    'value' => Request::get('oauth_signature_method', 'sha1')
                ]
            ];
        }

        return $common;
    }

    private function syncRegistrationConfigs($registrationId): void
    {
        foreach ($this->extractRegistrationConfigsFromRequest() as $config) {
            if (!Request::bool($config['name'])) {
                RegistrationConfig::deleteBySQL(
                    "registration_id = :registration_id AND name = :name",
                    [
                        'registration_id' => $registrationId,
                        'name' => strtolower($config['name'])
                    ]
                );

                continue;
            }

            if (empty($config['value'])) {
                continue;
            }

            RegistrationConfig::updateOrCreate(
                [
                    'registration_id' => $registrationId,
                    'name' => strtolower($config['name'])
                ],
                [
                    'value' => $config['value']
                ]
            );
        }
    }
}

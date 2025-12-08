<?php
use Lti\Registration;
use Lti\RegistrationConfig;

class Lti_RegistrationsController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
    }

    public function index_action(): void
    {
        dd('index');
    }

    public function show_action(Registration $registration): void
    {
        $this->render_vue_app(
            Studip\VueApp::create('lti/registrations/Show')
                ->withProps([
                    'registration' => [
                        ...$registration->toRawArray(),
                        ...$registration->config_values
                    ]
                ])
        );
    }

    public function create_action(): void
    {
        $this->render_vue_app(
            Studip\VueApp::create('lti/registrations/Create')
                ->withProps([
                    'role' => 'tool'
                ])
        );
    }

    public function store_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $registration = Registration::create([
            'version' => Request::get('version', '1.3a'),
            'name' => Request::get('name'),
            'description' => Request::get('description'),
            'data_protection_notes' => Request::get('data_protection_notes'),
            'terms_of_use_url' => Request::get('terms_of_use_url'),
            'privacy_policy_url' => Request::get('privacy_policy_url'),
            'client_id' => 'random-string',
            'range_id' => Context::getId() ?? 'global',
        ]);

        $this->storeRegistrationConfigs($registration->id);

        PageLayout::postSuccess(
            sprintf(
                _('Die LTI-Registrierung „%s“ wurde gespeichert.'),
                htmlReady($registration->name)
            )
        );

        $this->redirect('admin/lti/tools');
    }

    public function edit_action(Registration $registration): void
    {
        $this->render_vue_app(
            Studip\VueApp::create('lti/registrations/Edit')
                ->withProps([
                    'registration' => [
                        ...$registration->toRawArray(),
                        ...$registration->config_values
                    ]
                ])
        );
    }

    public function update_action(Registration $registration): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $registration->setData([
            'version' => Request::get('version', '1.3a'),
            'name' => Request::get('name'),
            'description' => Request::get('description'),
            'data_protection_notes' => Request::get('data_protection_notes'),
            'terms_of_use_url' => Request::get('terms_of_use_url'),
            'privacy_policy_url' => Request::get('privacy_policy_url'),
            'client_id' => 'random-string',
            'range_id' => Context::getId() ?? 'global',
        ]);

        $registration->store();

        $this->storeRegistrationConfigs($registration->id);

        PageLayout::postSuccess(
            sprintf(
                _('Die LTI-Registrierung „%s“ wurde gespeichert.'),
                htmlReady($registration->name)
            )
        );

        $this->redirect('admin/lti/tools');
    }

    public function delete_action(Registration $registration): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $registrationName = $registration->name;
        $registration->delete();

        PageLayout::postSuccess(
            sprintf(
                _('Die LTI-Registrierung „%s“ wurde gelöscht.'),
                htmlReady($registrationName)
            )
        );

        $this->redirect('admin/lti/tools');
    }

    private function extractConfigFromRequest(): array
    {
        $common = [
            [
                'name' => 'launch_url',
                'value' => Request::get('launch_url'),
            ],
            [
                'name' => 'send_lis_person',
                'value' => Request::get('send_lis_person'),
            ],
            [
                'name' => 'custom_parameters',
                'value' => Request::get('custom_parameters'),
            ],
            [
                'name' => 'launch_container',
                'value' => Request::get('launch_container'),
            ]
        ];

        if (Request::get('version') === '1.3a') {
            return [
                ...$common,
                [
                    'name' => 'auth_init_url',
                    'value' => Request::get('auth_init_url')
                ],
                [
                    'name' => 'deep_linking_url',
                    'value' => Request::get('deep_linking_url')
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

        if (Request::get('version') === '1.1') {
            return [
                ...$common,
                [
                    'name' => 'consumer_key',
                    'value' => Request::get('consumer_key')
                ],
                [
                    'name' => 'consumer_secret',
                    'value' => Request::get('consumer_secret')
                ]
            ];
        }

        return $common;
    }

    private function storeRegistrationConfigs($registration_id): void
    {
        foreach ($this->extractConfigFromRequest() as $config) {
            if (!empty($config['value'])) {
                RegistrationConfig::updateOrCreate(
                    [
                        'registration_id' => $registration_id,
                        'name' => strtolower($config['name'])
                    ],
                    [
                        'value' => $config['value']
                    ]
                );
            }
        }
    }
}

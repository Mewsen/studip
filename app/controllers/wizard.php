<?php

use Studip\WizardPart;

class WizardController extends AuthenticatedController
{

    public function before_filter(&$action, &$args)
    {
        $GLOBALS['perm']->check('root');

        parent::before_filter($action, $args);
    }

    public function index_action()
    {
        PageLayout::setTitle('Wizard');

        $form = Studip\Forms\Form::fromSORM(
            User::findCurrent(),
            [
                'legend' => _('Herzlich willkommen!'),
                'fields' => [
                    'username' => [
                        'label' => _('Benutzername'),
                        'required' => true,
                        'maxlength' => '63',
                        'attributes' => ['autocomplete' => 'off'],
                        'validate' => function ($value, $input) {
                            if (!preg_match(Config::get()->USERNAME_REGULAR_EXPRESSION, $value)) {
                                return Config::get()->getMetadata('USERNAME_REGULAR_EXPRESSION')['comment'] ?:
                                    _('Benutzername muss mindestens 4 Zeichen lang sein und darf nur aus Buchstaben, '
                                        . 'Ziffern, Unterstrich, @, Punkt und Minus bestehen.');
                            }
                            $user = User::findByUsername($value);
                            $context = $input->getContextObject();
                            if ($user && ($user->id !== $context->getId())) {
                                return _('Benutzername ist schon vergeben.');
                            }
                            return true;
                        }
                    ],
                    'password' => [
                        'label' => _('Passwort'),
                        'type' => 'password',
                        'required' => true,
                        'maxlength' => '31',
                        'minlength' =>  '8',
                        'attributes' => ['autocomplete' => 'new-password'],
                        'mapper' => function($value) {
                            $hasher = UserManagement::getPwdHasher();
                            return $hasher->HashPassword($value);
                        }
                    ],
                    'confirm_password' => [
                        'label' => _('Passwortbestätigung'),
                        'type' => 'password',
                        'required' => true,
                        'maxlength' => '31',
                        'minlength' =>  '8',
                        'attributes' => ['autocomplete' => 'new-password'],
                        ':pattern'    => "password.replace(/[.*+?^\${}()|[\\]\\\\]/g, '\\\\$&')", //mask special chars
                        'data-validation_requirement' => _('Die Passwörter stimmen nicht überein.'),
                        'store' => function() {}
                    ],
                    'title_front' => [
                        'label' => _('Titel'),
                        'type'  => 'datalist',
                        'attributes' => ['autocomplete' => 'honorific-prefix'],
                        'options' => $GLOBALS['TITLE_FRONT_TEMPLATE']
                    ],
                    'title_rear' => [
                        'label' => _('Titel nachgestellt'),
                        'type'  => 'datalist',
                        'attributes' => ['autocomplete' => 'honorific-suffix'],
                        'options' => $GLOBALS['TITLE_REAR_TEMPLATE'],
                    ],
                    'vorname' => [
                        'label' => _('Vorname'),
                        'attributes' => ['autocomplete' => 'given-name'],
                        'required' => true
                    ],
                    'nachname' => [
                        'label' => _('Nachname'),
                        'attributes' => ['autocomplete' => 'family-name'],
                        'required' => true
                    ],
                    'geschlecht' => [
                        'name' => 'geschlecht',
                        'value' => 0,
                        'label' => _('Geschlecht'),
                        'type' => 'radio',
                        'orientation' => 'horizontal',
                        'options' => [
                            '0' => _('Keine Angabe'),
                            '1' => _('männlich'),
                            '2' => _('weiblich'),
                            '3' => _('divers'),
                        ],
                    ],
                    'email' => [
                        'label' => _('E-Mail'),
                        'required' => true,
                        'attributes' => ['autocomplete' => 'email'],
                        'validate' => function ($value, $input) {
                            $user = User::findOneByEmail($value);
                            $context = $input->getContextObject();
                            if ($user && ($user->id !== $context->getId())) {
                                return _('Diese Emailadresse ist bereits registriert.');
                            }
                            return true;
                        }
                    ],
                ]
            ]
        )->noButtons();

        $form2 = \Studip\Forms\Form::create()->noButtons();
        $details_part = new \Studip\Forms\Fieldset(_('Angaben zur gefundenen Barriere'));
        $details_part->addInput(
            new \Studip\Forms\SelectInput(
                'barrier_type',
                _('Um welche Art von Barriere handelt es sich?'),
                '',
                [
                    'options' => [
                        _('Inhalte auf dieser Seite (z.B. PDF, Bilder oder Lernmodule)') => _('Inhalte auf dieser Seite (z.B. PDF, Bilder oder Lernmodule)'),
                        _('Ein Problem mit der Seite selbst oder der Navigation') => _('Ein Problem mit der Seite selbst oder der Navigation'),
                        _('Sonstiges') => _('Sonstiges')
                    ]
                ]
            )
        )->setRequired();
        $details_part->addInput(
            new \Studip\Forms\TextareaInput(
                'barrier_details',
                _('Beschreiben Sie die Barriere'),
                ''
            )
        )->setRequired();
        $form2->addPart($details_part);
        $personal_data_part = new \Studip\Forms\Fieldset(_('Ihre persönlichen Daten'));
        $personal_data_part->addText(sprintf('<p>%s</p>', _('Freiwillige Angaben Ihrer Kontaktdaten für etwaige Rückfragen.')));
        $personal_data_part->addInput(
            new \Studip\Forms\SelectInput(
                'salutation',
                _('Anrede'),
                'Keine Angabe',
                [
                    'options' => [
                        _('Keine Angabe') => _('Keine Angabe'),
                        _('Frau') => _('Frau'),
                        _('Herr') => _('Herr'),
                        _('divers') => _('divers')
                    ]
                ]
            )
        );
        $personal_data_part->addInput(
            new \Studip\Forms\TextInput(
                'name',
                _('Vorname und Nachname'),
                ''
            )
        );
        $personal_data_part->addInput(
            new \Studip\Forms\TextInput(
                'phone_number',
                _('Telefonnummer'),
                ''
            )
        );
        $personal_data_part->addInput(
            new \Studip\Forms\TextInput(
                'email_address',
                _('E-Mail-Adresse'),
                ''
            )
        );
        $form2->addPart($personal_data_part);

        $steps = [
            WizardPart::create(
                Studip\VueApp::create('massmail/MassMailMessagesList'),
                'Nachrichtenübersicht',
                'mail2'
            ),
            WizardPart::create(
                Studip\VueApp::create('CacheAdministration')
                    ->withProps([
                        'enabled'       => true,
                        'currentCache'  => StudipDbCache::class,
                        'currentConfig' => StudipDbCache::getConfig(),
                        'cacheTypes'    => CacheType::findAndMapBySQL(
                            fn(CacheType $type) => $type->toArray(),
                            "1 ORDER BY `cache_id`"
                        ),
                    ]),
                'Cache',
                'admin'
            ),
            WizardPart::create(
                Studip\VueApp::create('ColourSelector')
                    ->withProps([
                        'autofocus' => true,
                        'colours' => collect($GLOBALS['PERS_TERMIN_KAT'])->map(
                            fn($data, $id) => ['id' => $id, 'colour' => $data['bgcolor']]
                        )->values()
                    ]),
                'Farbwähler',
                'colorpicker'
            ),
            WizardPart::create(
                Studip\VueApp::create('ThemeSettings')
                    ->withVuexStore(
                        'theme-settings.module.js',
                        'theme-settings-module',
                        [
                            'setUserId' => User::findCurrent()->id,
                        ]
                    ),
                'Themes',
                'style'
            ),
        ];

        /*$steps = [
            WizardPart::create($form, 'User form', 'person'),
            WizardPart::create($form2, 'Report barrier', 'accessibility')
        ];*/

        Sidebar::Get()->addWidget(new VueWidget('wizard-sidebar'));

        $this->render_wizard($steps);
    }

}

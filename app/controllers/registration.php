<?php

use Studip\Forms\Form;

class RegistrationController extends AuthenticatedController
{
    protected $allow_nobody = true;

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        PageLayout::setTitle(_('Registrierung'));
    }

    public function index_action()
    {
        $new_user = new User();
        $new_user->perms = 'user';
        $new_user->auth_plugin = 'standard';
        $new_user->preferred_language = $_SESSION['_language'] ?? Config::get()->DEFAULT_LANGUAGE;
        $this->registrationform = Form::fromSORM(
            $new_user,
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
                        'data-validation_requirement' => _('Passwörter stimmen nicht überein.'),
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
                            '0' => _('keine Angabe'),
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
        );
        $this->registrationform->setSaveButtonText(_('Registrierung abschließen'));
        $this->registrationform->setCancelButtonText(_('Abbrechen'));
        $this->registrationform->setCancelButtonName(URLHelper::getURL('index.php?cancel_login=1'));

        $this->registrationform->addStoreCallback(
            function (Form $form) {
                $new_user = $form->getLastPart()->getContextObject();
                sess()->regenerateId();
                auth()->setAuthenticatedUser($new_user);
                auth()->sendValidationMail($new_user);
                return 1;
            }
        );

        $this->registrationform->autoStore()->setURL(URLHelper::getURL('dispatch.php/start'));
    }

    public function email_validation_action()
    {
        if (!User::findCurrent()) {
            $_SESSION['redirect_after_login'] = Request::url();
            sess()->save();
            $this->redirect(URLHelper::getURL('dispatch.php/login'));
            return;
        }
        // hier wird noch mal berechnet, welches secret in der Bestaetigungsmail uebergeben wurde
        $secret = Request::option('secret');
        PageLayout::setHelpKeyword('Basis.AnmeldungMail');
        PageLayout::setTitle(_('Bestätigung der E-Mail-Adresse'));
        //user bereits vorhanden
        if ($GLOBALS['perm']->have_perm('autor')) {
            $info = sprintf(_('Sie haben schon den Status <b>%s</b> im System.
                       Eine Aktivierung des Accounts ist nicht mehr nötig, um Schreibrechte zu bekommen'), $GLOBALS['user']->perms);
            $details = [];
            $details[] = sprintf('<a href="%s">%s</a>', URLHelper::getLink('index.php'), _('zurück zur Startseite'));
            $message = MessageBox::info($info, $details);
        }

        //  So, wer bis hier hin gekommen ist gehoert zur Zielgruppe...
        // Volltrottel (oder abuse)
        elseif (empty($secret)) {
            $message = MessageBox::error(_('Sie müssen den vollständigen Link aus der Bestätigungsmail in die Adresszeile Ihres Browsers kopieren.'));
        }

        // abuse (oder Volltrottel)
        else {
            if (!Token::isValid($secret, User::findCurrent()->id)) {
                $error = _('Der übergebene <em>Secret-Code</em> ist nicht korrekt.');
                $details = [];
                $details[] = _('Sie müssen unter dem Benutzernamen eingeloggt sein, für den Sie die Bestätigungsmail erhalten haben.');
                $details[] = _('Und Sie müssen den vollständigen Link aus der Bestätigungsmail in die Adresszeile Ihres Browsers kopieren.');
                $message = MessageBox::error($error, $details);

                // Mail an abuse
                $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
                $Zeit = date("H:i:s, d.m.Y", time());
                $username = User::findCurrent()->username;
                StudipMail::sendAbuseMessage("Validation", "Secret falsch\n\nUser: $username\n\nIP: $REMOTE_ADDR\nZeit: $Zeit\n");
            } // alles paletti, Status ändern
            else {
                $studip_user = User::findCurrent();
                $studip_user->perms = 'autor';
                if (!$studip_user->store()) {
                    $error = _('Fehler! Bitte wenden Sie sich an den Systemadministrator.');
                    $message = MessageBox::error($error);
                } else {
                    $success = _('Ihr Status wurde erfolgreich auf <em>autor</em> gesetzt.<br>
                      Damit dürfen Sie in den meisten Veranstaltungen schreiben, für die Sie sich anmelden.');
                    $details = [];
                    $details[] = _('Einige Veranstaltungen erfordern allerdings bei der Anmeldung die Eingabe eines Passwortes.
                        Dieses Passwort erfahren Sie von den Lehrenden der Veranstaltung.');
                    $message = MessageBox::success($success, $details);

                    // Auto-Inserts
                    AutoInsert::instance()->saveUser($studip_user->id, "autor");

                    auth()->setAuthenticatedUser(\User::build(['user_id' => 'nobody', 'perms' => null]));

                    $info = sprintf(_('Die Statusänderung wird erst nach einem erneuten %sLogin%s wirksam!<br>
                          Deshalb wurden Sie jetzt automatisch ausgeloggt.'),
                        '<a href="' . URLHelper::getLink('index.php') . '"><em>',
                        '</em></a>');
                    $message .= MessageBox::info($info);
                }
                $this->message = $message;
            }
        }
    }
}

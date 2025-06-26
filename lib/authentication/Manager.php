<?php
/**
 * Authentication Manager
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      André Noack <noack@data-quest.de>
 */
namespace Studip\Authentication;

use AccessDeniedException;
use Config;
use MessageBox;
use Metrics;
use PageLayout;
use Request;
use Seminar_Perm;
use Seminar_User;
use StudipAuthAbstract;
use StudipAuthSSO;
use StudipMail;
use Token;
use User;

class Manager
{
    public const DEFAULT_KEPT_SESSION_VARIABLES = [
        'auth',
        '_language',
        'phpCAS',
        'contrast',
        'redirect_after_login',
    ];

    private ?array $auth = [];

    public function __construct(
        private bool $nobody = false
    ) {
    }

    public function getNobody(): bool
    {
        return $this->nobody;
    }

    public function setNobody(bool $allow_nobody = false): void
    {
        $this->nobody = $allow_nobody;
    }


    public function start(): bool
    {
        $this->auth =& $_SESSION['auth'];

        if (!$this->isAuthenticated()) {
            $user = null;

            $provider = Request::option('sso');

            if ($provider) {
                Metrics::increment('core.sso_login.attempted');
                // then do login
                $authplugin = StudipAuthAbstract::GetInstance($provider);
                if ($authplugin instanceof StudipAuthSSO) {
                    $user = $authplugin->authenticateUser('', '');
                    if ($user) {
                        if ($user->isExpired()) {
                            throw new AccessDeniedException(
                                _('Dieses Benutzerkonto ist abgelaufen. Wenden Sie sich bitte an die Administration.')
                            );
                        }
                        if ($user->locked) {
                            throw new AccessDeniedException(
                                _('Dieser Benutzer ist gesperrt! Wenden Sie sich bitte an die Administration.')
                            );
                        }
                        Metrics::increment('core.sso_login.succeeded');

                        $this->setAuthenticatedUser($user);
                        sess()->regenerateId(self::DEFAULT_KEPT_SESSION_VARIABLES);
                    } else {
                        PageLayout::postMessage(
                            MessageBox::error($authplugin->plugin_name . ': ' . _('Login fehlgeschlagen'),
                                $authplugin->error_msg ? [$authplugin->error_msg] : []),
                            md5($authplugin->error_msg)
                        );
                    }
                }
            }
            if (!$user) {
                if ($this->nobody && !Request::get('again')) {
                    $this->setAuthenticatedUser(User::build(['user_id' => 'nobody', 'perms' => null]));
                } elseif (!match_route('dispatch.php/login')) {
                    return false;
                }
            }
        } else {
            $this->setAuthenticatedUser($this->auth['uid'] !== 'nobody' ? User::find($this->auth['uid']) : User::build(['user_id' => 'nobody', 'perms' => null]));
        }
        return true;
    }

    public function isAuthenticated(): string|false
    {
        if (!is_array($this->auth)) {
            $this->auth = [];
        }
        if (
            isset($this->auth['uid'])
            && $this->auth['uid'] === 'nobody'
            && (!$this->nobody || Request::option('again'))
        ) {
            $this->auth['uid'] = null;
        }

        $maintenance_mode = Config::get()->getValue('MAINTENANCE_MODE_ENABLE');

        //check if the user got kicked meanwhile, or if user is locked out
        $user = null;
        if (!empty($this->auth['uid']) && $this->auth['uid'] != 'nobody') {
            if (isset($GLOBALS['user']) && $GLOBALS['user']->id === $this->auth['uid']) {
                $user = User::findCurrent();
            } else {
                $user = User::find($this->auth['uid']);
            }
            if (!$user->username || $user->isBlocked()) {
                $this->auth = [];
            }
        } elseif ($maintenance_mode && Request::username('loginname')) {
            $user = User::findByUsername(Request::username('loginname'));
        }
        if ($maintenance_mode && $user?->perms !== 'root') {
            $this->auth = [];
            throw new AccessDeniedException(_("Das System befindet sich im Wartungsmodus. Zur Zeit ist kein Zugriff möglich."));
        }
        return $this->auth['uid'] ?? false;
    }

    public function setAuthenticatedUser(User $user): void
    {
        $this->auth['uid'] = $user->id;

        $GLOBALS['user'] = new Seminar_User($user);
        $GLOBALS['perm'] = new Seminar_Perm();
    }

    public function sendValidationMail(?User $user = null): void
    {
        $user ??= User::findCurrent();

        // template-variables for the include partial
        $Zeit     = date('H:i:s, d.m.Y', $user->mkdate);
        $username = $user->username;
        $Vorname  = $user->vorname;
        $Nachname = $user->nachname;
        $Email    = $user->email;

        // (re-)send the confirmation mail
        $to     = $user->email;
        $token  = Token::create(7 * 24 * 60 * 60, $user->id); // Link is valid for 1 week
        $url    = $GLOBALS['ABSOLUTE_URI_STUDIP'] . 'dispatch.php/registration/email_validation?secret=' . $token;
        $mail   = new StudipMail();
        $abuse  = $mail->getReplyToEmail();

        $lang_path = getUserLanguagePath($user->id);

        // include language-specific subject and mailbody
        // TODO: This should be refactored so that the included file returns an array
        include "locale/{$lang_path}/LC_MAILS/register_mail.inc.php"; // Defines $subject and $mailbody

        // send the mail
        $mail->setSubject($subject ?? '')
            ->addRecipient($to)
            ->setBodyText($mailbody ?? '')
            ->send();
    }
}

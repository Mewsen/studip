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

class Manager
{
    private $auth = [];
    public function __construct(private $nobody = false)
    {
    }

    /**
     * @return false|mixed
     */
    public function getNobody(): mixed
    {
        return $this->nobody;
    }

    public function setNobody($allow_nobody = false): void
    {
        $this->nobody = $allow_nobody;
    }


    public function start()
    {
        $this->auth =& $_SESSION['auth'];

        if (!$this->isAuthenticated()) {
            $user = null;
            if (($provider = \Request::option('sso'))) {
                \Metrics::increment('core.sso_login.attempted');
                // then do login
                $authplugin = \StudipAuthAbstract::GetInstance($provider);
                if ($authplugin) {
                    $authplugin->authenticateUser('', '');
                    if ($authplugin->getUser()) {
                        $user = $authplugin->getStudipUser($authplugin->getUser());
                        $exp_d = \UserConfig::get($user->id)->EXPIRATION_DATE;
                        if ($exp_d > 0 && $exp_d < time()) {
                            throw new \AccessDeniedException(
                                _('Dieses Benutzerkonto ist abgelaufen. Wenden Sie sich bitte an die Administration.')
                            );
                        }
                        if ($user->locked == 1) {
                            throw new \AccessDeniedException(
                                _('Dieser Benutzer ist gesperrt! Wenden Sie sich bitte an die Administration.')
                            );
                        }
                        \Metrics::increment('core.sso_login.succeeded');
                        sess()->regenerateId(['auth', '_language', 'phpCAS', 'contrast']);
                    }
                }
            }
            if (!$user) {
                if ($this->nobody && !\Request::get('again')) {
                    $this->setAuthenticatedUser(\User::build(['user_id' => 'nobody', 'perms' => null]));
                }
                if (!match_route('dispatch.php/login')) {
                    return false;
                }
            }
        } else {
            if ($this->auth['uid'] !== 'nobody' && \Request::get('again') && !match_route('dispatch.php/login')) {
                return false;
            }
            $this->setAuthenticatedUser($this->auth['uid'] !== 'nobody' ? \User::find($this->auth['uid']) : \User::build(['user_id' => 'nobody', 'perms' => null]));
        }
        return true;
    }

    public function isAuthenticated()
    {
        if (!is_array($this->auth)) {
            $this->auth = [];
        }
        if (isset($this->auth['uid']) && $this->auth['uid'] === 'nobody' && (!$this->nobody || \Request::option('again'))) {
            $this->auth['uid'] = null;
        }
        $cfg = \Config::GetInstance();
        //check if the user got kicked meanwhile, or if user is locked out
        if (!empty($this->auth['uid']) && !in_array($this->auth['uid'], ['nobody'])) {
            $user = null;
            if (isset($GLOBALS['user']) && $GLOBALS['user']->id == $this->auth['uid']) {
                $user = $GLOBALS['user'];
            } else {
                $user = \User::find($this->auth['uid']);
            }
            $exp_d = $user->username ? \UserConfig::get($user->id)->EXPIRATION_DATE : 0;
            if (!$user->username || $user->locked || ($exp_d > 0 && $exp_d < time())) {
                $this->auth = [];
            }
        } elseif ($cfg->getValue('MAINTENANCE_MODE_ENABLE') && \Request::username('loginname')) {
            $user = \User::findByUsername(\Request::username('loginname'));
        }
        if ($cfg->getValue('MAINTENANCE_MODE_ENABLE') && $user->perms != 'root') {
            $this->auth = [];
            throw new \AccessDeniedException(_("Das System befindet sich im Wartungsmodus. Zur Zeit ist kein Zugriff möglich."));
        }
        return @$this->auth['uid'] ? : false;
    }

    public function setAuthenticatedUser(\User $user): void
    {
        $this->auth['uid'] = $user->id;
        $GLOBALS['user'] = new \Seminar_User($user);
        $GLOBALS['perm'] = new \Seminar_Perm();
    }

    public function sendValidationMail(\User $user = null): void
    {
        if (is_null($user)) {
            $user = \User::findCurrent();
        }

        // template-variables for the include partial
        $Zeit     = date('H:i:s, d.m.Y', $user->mkdate);
        $username = $user->username;
        $Vorname  = $user->vorname;
        $Nachname = $user->nachname;
        $Email    = $user->email;

        // (re-)send the confirmation mail
        $to     = $user->email;
        $token  = \Token::create(7 * 24 * 60 * 60, $user->id); // Link is valid for 1 week
        $url    = $GLOBALS['ABSOLUTE_URI_STUDIP'] . 'dispatch.php/registration/email_validation?secret=' . $token;
        $mail   = new \StudipMail();
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

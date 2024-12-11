<?php
/**
 * Basic Stud.IP authentication, using the Stud.IP database
 *
 * @author André Noack <noack@data-quest.de>
 * @license GPL2 or any later version
 */
class StudipAuthStandard extends StudipAuthAbstract
{
    public $show_login = true;

    /**
     * @return bool
     */
    public function isAuthenticated($username, $password)
    {
        $user = User::findByUsername($username);
        if (!$user || !$password || mb_strlen($password) > 72) {
            $this->error_msg= _('Ungültige Benutzername/Passwort-Kombination!') ;
            return false;
        }

        if ($user->username !== $username) {
            $this->error_msg = _('Bitte achten Sie auf korrekte Groß-Kleinschreibung beim Username!');
            return false;
        }

        if (isset($user->auth_plugin) && $user->auth_plugin !== 'standard') {
            $this->error_msg = sprintf(_('Dieser Benutzername wird bereits über %s authentifiziert!'), $user->auth_plugin) ;
            return false;
        }

        if (!password_verify($password, $user->password)) {
            $this->error_msg= _('Das Passwort ist falsch!');
            return false;
        }

        if (password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->store();
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isUsedUsername($username)
    {
        return (bool) User::findByUsername($username);
    }
}

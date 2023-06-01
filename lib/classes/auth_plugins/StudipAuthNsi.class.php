<?php
# Lifter007: TODO
# Lifter003: TODO
# Lifter010: TODO
// +---------------------------------------------------------------------------+
// This file is part of Stud.IP
// StudipAuthStandard.class.php
// Basic Stud.IP authentication, using the Stud.IP database
//
// Copyright (c) 2003 André Noack <noack@data-quest.de>
// Suchi & Berg GmbH <info@data-quest.de>
// +---------------------------------------------------------------------------+
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or any later version.
// +---------------------------------------------------------------------------+
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// +---------------------------------------------------------------------------+

/**
 * Basic Stud.IP authentication, using the Stud.IP database
 *
 * Basic Stud.IP authentication, using the Stud.IP database
 *
 * @access   public
 * @author   André Noack <noack@data-quest.de>
 * @package
 */
class StudipAuthNsi extends StudipAuthStandard
{

    public $error_head = 'NSI-Portal';

    /**
     *
     *
     *
     * @access public
     *
     */
    function isAuthenticated($username, $password)
    {
        $username = mb_strtolower($username);
        $user = User::findByUsername($username);
        if (!$user || !$password || mb_strlen($password) > 72) {
            $this->error_msg= _("Ungültige Benutzername/Passwort-Kombination!") ;
            return false;
        } elseif ($user->auth_plugin != $this->plugin_name) {
            $this->error_msg = sprintf(_("Dieser Benutzername wird bereits über %s authentifiziert!"),$user->auth_plugin) ;
            return false;
        } else {
            $pass = $user->password;
        }
        if (mb_strlen($pass) != 60) {
            $this->error_msg = sprintf(_("Sie benötigen die Anmeldedaten des NSI-Portals. Bitte stellen Sie sicher, dass Sie sich bereits einmal im
%s angemeldet haben. Bei einer Passwortänderung im NSI-Portal kann es bis zu 24 Stunden dauern, bis die Änderung in Stud.IP wirksam wird."), '<a href="https://www.nsi-hsvn.de/login.html" target="_blank">NSI-Portal</a>') ;
            return false;
        }
        $hasher = UserManagement::getPwdHasher();
        $check = $hasher->CheckPassword($password, $pass);
        if (!$check) {
            $this->error_msg= _("Das Passwort ist falsch!");
            return false;
        } else {
            return true;
        }
    }

}

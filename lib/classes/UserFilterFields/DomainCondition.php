<?php

/**
 * DomainCondition.php
 *
 * All conditions concerning the user domain in Stud.IP can be specified here.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
namespace UserFilterFields;

class DomainCondition extends \UserFilterField
{
    // --- ATTRIBUTES ---
    public $valuesDbTable = 'userdomains';
    public $valuesDbIdField = 'userdomain_id';
    public $valuesDbNameField = 'name';
    public $userDataDbTable = 'user_userdomains';
    public $userDataDbField = 'userdomain_id';

    public static $sortOrder = 8;

    public static function isActive()
    {
        return \UserDomain::countBySQL("1") > 0;
    }

    /**
     * Get this field's display name.
     *
     * @return String
     */
    public function getName()
    {
        return _('Domäne');
    }

}

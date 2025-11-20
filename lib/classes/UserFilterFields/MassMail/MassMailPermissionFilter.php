<?php

/**
 * PermissionCondition.php
 *
 * All conditions concerning the semester of study in Stud.IP can be specified here.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig <elmar.ludwig@uos.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
namespace UserFilterFields\MassMail;

use UserFilterFields\PermissionCondition;
use User;
use DBManager;
use MassMail\MassMailPermission;

class MassMailPermissionFilter extends PermissionCondition
{

    public string $target = '';

    public static $sortOrder = 10;

    /**
     * @see \UserFilterField::getTargets()
     */
    public static function getTargets()
    {
        return ['students', 'employees'];
    }

    /**
     * @see UserFilterField::__construct
     */
    public function __construct($fieldId = '')
    {
        $this->userDataDbTable = 'auth_user_md5';
        $this->userDataDbField = 'perms';

        parent::__construct($fieldId);

        $this->validValues = [
            'autor' => _('Student/in'),
            'tutor' => _('Tutor/in'),
            'dozent' => _('Lehrende/r')
        ];
    }

    /**
     * Get this field's display name.
     *
     * @return String
     */
    public function getName()
    {
        return _('Globaler Status');
    }

    /**
     * Gets all users with given gender.
     *
     * @return array All users that are affected by the current condition
     * field.
     */
    public function getUsers($restrictions = array())
    {
        $users = [];
        if (MassMailPermission::has(User::findCurrent()->id, true)) {
            $users = DBManager::get()->fetchFirst("SELECT DISTINCT `user_id` " .
                "FROM `" . $this->userDataDbTable . "` " .
                "WHERE `" . $this->userDataDbField . "`" . $this->compareOperator .
                "?", [$this->value]);
        } else if (MassMailPermission::has(User::findCurrent()->id)) {

            $allowed = MassMailPermission::getForUser(User::findCurrent());

            $sql = "SELECT DISTINCT `" . $this->userDataDbTable . "`.`user_id` FROM `" . $this->userDataDbTable . "` ";
            $where = "WHERE `" . $this->userDataDbTable . "`.`" . $this->userDataDbField . "`" . $this->compareOperator . ":value";
            $parameters = ['value' => $this->value];

            switch ($this->target) {
                case 'employees':
                    $sql .= "JOIN `user_inst` USING (`user_id`) ";
                    $where .= " AND `user_inst`.`Institut_id` IN (:institutes) AND `user_inst`.`inst_perms` IN (:perms)";
                    $parameters['institutes'] = $allowed['allowed_institutes'];
                    $parameters['perms'] = ['autor', 'tutor', 'dozent', 'admin'];
                    break;
                case 'students':
                default:
                    $sql .= "JOIN `user_studiengang` USING (`user_id`) ";
                    $where .= " AND (
                            `user_studiengang`.`abschluss_id` IN (:degrees)
                            OR `user_studiengang`.`fach_id` IN (:subjects)
                        )";
                    $parameters['degrees'] = $allowed['allowed_degrees'];
                    $parameters['subjects'] = $allowed['allowed_subjects'];
            }

            $users = DBManager::get()->fetchFirst($sql.$where, $parameters);
        }

        return $users;
    }
}

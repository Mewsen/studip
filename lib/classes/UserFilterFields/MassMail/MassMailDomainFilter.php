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
namespace UserFilterFields\MassMail;

use MassMail\MassMailPermission;
use UserFilterFields\DomainCondition;
use DBManager;
use User;

class MassMailDomainFilter extends DomainCondition
{

    public string $target = '';

    /**
     * Gets all users belonging to given domain.
     *
     * @return array All users that are affected by the current condition
     * field.
     */
    public function getUsers($restrictions = [])
    {
        $users = [];
        if (MassMailPermission::has(User::findCurrent()->id, true)) {
            $users = parent::getUsers($restrictions);
        } else if (MassMailPermission::has(User::findCurrent()->id)) {

            $allowed = MassMailPermission::getForUser(User::findCurrent());

            switch ($this->target) {
                case 'employees':
                    $sql = "SELECT DISTINCT `" . $this->userDataDbTable . "`.`user_id` FROM `" . $this->userDataDbTable
                        . "`JOIN `user_inst` USING (`user_id`) ";
                    $where = "WHERE `" . $this->userDataDbTable . "`.`" . $this->userDataDbField . "`" . $this->compareOperator
                        . ":value AND `user_inst`.`Institut_id` IN (:institutes) AND `user_inst`.`inst_perms` IN (:perms)";
                    $parameters = [
                        'value' => $this->value,
                        'institutes' => $allowed['allowed_institutes'],
                        'perms' => ['autor', 'tutor', 'dozent', 'admin']
                    ];
                    break;
                case 'students':
                default:
                    $sql = "SELECT DISTINCT `" . $this->userDataDbTable . "`.`user_id` FROM `" . $this->userDataDbTable
                        . "`JOIN `user_studiengang` USING (`user_id`) ";
                    $where = "WHERE `" . $this->userDataDbTable . "`.`" . $this->userDataDbField . "`" . $this->compareOperator
                        . ":value AND `user_studiengang`.`abschluss_id` IN (:degrees)
                        AND `user_studiengang`.`fach_id` IN (:subjects)";
                    $parameters = [
                        'value' => $this->value,
                        'degrees' => $allowed['allowed_degrees'],
                        'subjects' => $allowed['allowed_subjects']
                    ];
                    break;
            }
            $users = DBManager::get()->fetchFirst($sql . $where, $parameters);
        }

        return $users;
    }
}

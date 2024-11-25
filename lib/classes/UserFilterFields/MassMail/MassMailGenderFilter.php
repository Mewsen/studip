<?php

namespace UserFilterFields\MassMail;

use UserFilterField;
use MassMail\MassMailPermission;
use User;
use DBManager;

class MassMailGenderFilter extends UserFilterField
{
    public $userDataDbField = 'geschlecht';
    public $userDataDbTable = 'user_info';

    public static $sortOrder = 8;

    public $target = '';

    public function __construct($fieldId = '')
    {
        parent::__construct($fieldId);

        $this->validCompareOperators = [
            '='   => _('ist'),
            '!=' => _('ist nicht'),
        ];

        $this->validValues = [
            0 => _('unbekannt'),
            1 => _('männlich'),
            2 => _('weiblich'),
            3 => _('divers')
        ];
    }

    public function getName()
    {
        return _('Geschlecht');
    }

    /**
     * Gets all users with given gender.
     *
     * @return array All users that are affected by the current condition
     * field.
     */
    public function getUsers($restrictions = [])
    {
        $users = [];
        if (MassMailPermission::has(User::findCurrent()->id, true)) {
            $users = DBManager::get()->fetchFirst("SELECT DISTINCT `user_id` " .
                "FROM `" . $this->userDataDbTable . "` " .
                "WHERE `" . $this->userDataDbField . "`" . $this->compareOperator .
                "?", [$this->value]);
        } else if (MassMailPermission::has(User::findCurrent()->id)) {

            $allowed = MassMailPermission::getForUser(User::findCurrent());

            $sql = "SELECT DISTINCT `" . $this->userDataDbTable . "`.`user_id` FROM `" . $this->userDataDbTable
                . "`JOIN `user_inst` USING (`user_id`) ";
            $where = "WHERE `" . $this->userDataDbTable . "`.`" . $this->userDataDbField . "`" . $this->compareOperator
                . ":value AND `user_inst`.`Institut_id` IN (:institutes) AND `user_inst`.`inst_perms` IN (:perms)";
            $parameters = [
                'value' => $this->value,
                'institutes' => $allowed['allowed_institutes'],
                'perms' => ['autor', 'tutor', 'dozent', 'admin']
            ];

            $users = DBManager::get()->fetchFirst($sql.$where, $parameters);
        }

        return $users;
    }
}

<?php

namespace UserFilterFields\MassMail;

use UserFilterField;
use MassMail\MassMailPermission;
use User;
use DBManager;

class MassMailStatusgroupFilter extends UserFilterField
{
    public $valuesDbTable = 'statusgruppen';
    public $valuesDbIdField = 'statusgruppe_id';
    public $valuesDbNameField = 'name';
    public $userDataDbTable = 'statusgruppe_user';
    public $userDataDbField = 'statusgruppe_id';

    public static $sortOrder = 10;

    public static function getTargets()
    {
        return ['employees'];
    }

    public function __construct($fieldId = '')
    {
        parent::__construct($fieldId);

        $this->validCompareOperators = [
            '='   => _('ist'),
            '!=' => _('ist nicht'),
        ];

        $this->validValues = [];
        if (MassMailPermission::has(User::findCurrent()->id, true)) {
            $this->validValues = DBManager::get()->fetchFirst(
                "SELECT DISTINCT `name` FROM `statusgruppen` ORDER BY `name` ASC"
            );
        } else if (MassMailPermission::has(User::findCurrent()->id)) {
            $allowed = MassMailPermission::getForUser(User::findCurrent());

            $this->validValues = DBManager::get()->fetchFirst(
                "SELECT DISTINCT `name` FROM `statusgruppen` WHERE `range_id` IN (:institutes) ORDER BY `name` ASC",
                ['institutes' => $allowed['allowed_institutes']]
            );
        }
    }

    public function getName()
    {
        return _('Statusgruppe');
    }

    /**
     * Gets all users belonging to a statusgroup with the given name. This is not done via statusgroup_id
     * in ordner to enable several institutes as filter.
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

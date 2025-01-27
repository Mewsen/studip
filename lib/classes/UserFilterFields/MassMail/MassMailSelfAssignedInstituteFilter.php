<?php

namespace UserFilterFields\MassMail;

use UserFilterField;
use MassMail\MassMailPermission;
use User;
use DBManager;

class MassMailSelfAssignedInstituteFilter extends UserFilterField
{
    public $valuesDbTable = 'Institute';
    public $valuesDbIdField = 'Institut_id';
    public $valuesDbNameField = 'Name';
    public $userDataDbTable = 'user_inst';
    public $userDataDbField = 'Institut_id';

    public static $sortOrder = 9;

    public static function getTargets()
    {
        return ['students'];
    }

    public function __construct($fieldId = '')
    {
        parent::__construct($fieldId);

        $this->validCompareOperators = [
            '='   => _('ist'),
            '!=' => _('ist nicht'),
        ];

        if (MassMailPermission::has(User::findCurrent()->id, true)) {
            // Get all available institutes from database, grouped by faculty.
            $faculties = DBManager::get()->fetchAll(
                "SELECT `Institut_id`, `Name` FROM `Institute`
                         WHERE `fakultaets_id` = `Institut_id` ORDER BY `Name`"
            );
            foreach ($faculties as $f) {
                $this->validValues[$f[$this->valuesDbIdField]] = $f[$this->valuesDbNameField];
                $this->validValues[$f[$this->valuesDbIdField].'_children'] =
                    sprintf(_('%s und Untereinrichtungen'),
                        $f[$this->valuesDbNameField]);
                $institutes = DBManager::get()->fetchAll(
                    "SELECT `Institut_id`, `Name` FROM `Institute`
                             WHERE `fakultaets_id` = :fak AND `Institut_id` != :fak ORDER BY `Name`",
                    ['fak' => $f[$this->valuesDbIdField]]
                );
                foreach ($institutes as $i) {
                    $this->validValues[$i[$this->valuesDbIdField]] = $i[$this->valuesDbNameField];
                }
            }
        } else if (MassMailPermission::has(User::findCurrent()->id)) {
            $this->validValues = [];

            $allowed = MassMailPermission::getForUser(User::findCurrent());

            // Get all available institutes from database, grouped by faculty.
            $faculties = DBManager::get()->fetchAll(
                "SELECT `Institut_id`, `Name` FROM `Institute`
                         WHERE `fakultaets_id` = `Institut_id` AND `Institut_id` IN (:allowed)
                         ORDER BY `Name`",
                ['allowed' => $allowed['allowed_institutes']]
            );
            foreach ($faculties as $f) {
                $this->validValues[$f[$this->valuesDbIdField]] = $f[$this->valuesDbNameField];
                $this->validValues[$f[$this->valuesDbIdField] . '_children'] =
                    sprintf(_('%s und Untereinrichtungen'),
                        $f[$this->valuesDbNameField]);
                $institutes = DBManager::get()->fetchAll(
                    "SELECT `Institut_id`, `Name` FROM `Institute`
                             WHERE `fakultaets_id` = :fak AND `Institut_id` != :fak AND `Institut_id` IN (:allowed)
                             ORDER BY `Name`",
                    ['fak' => $f[$this->valuesDbIdField], 'allowed' => $allowed['allowed_institutes']]
                );
                foreach ($institutes as $i) {
                    $this->validValues[$i[$this->valuesDbIdField]] = $i[$this->valuesDbNameField];
                }
            }

            $institutes = DBManager::get()->fetchAll(
                "SELECT `Institut_id`, `Name`
                 FROM `Institute`
                 WHERE `Institut_id` IN (:allowed)
                   AND `Institut_id` NOT IN (:processed)
                 ORDER BY `Name`",
                [
                    'allowed' => $allowed['allowed_institutes'],
                    'processed' => count($this->validValues) > 0 ? array_keys($this->validValues) : ''
                ]
            );
            foreach ($institutes as $i) {
                $this->validValues[$i[$this->valuesDbIdField]] = $i[$this->valuesDbNameField];
            }
        }
    }

    public function getName()
    {
        return _('Selbst zugeordnete Einrichtung');
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
                "? AND `inst_perms` = 'user'", [$this->value]);
        } else if (MassMailPermission::has(User::findCurrent()->id)) {

            $allowed = MassMailPermission::getForUser(User::findCurrent());

            $sql = "SELECT DISTINCT `" . $this->userDataDbTable . "`.`user_id` FROM `" . $this->userDataDbTable
                . "`JOIN `user_inst` USING (`user_id`) ";
            $where = "WHERE `" . $this->userDataDbTable . "`.`" . $this->userDataDbField . "`" . $this->compareOperator
                . ":value AND `user_inst`.`Institut_id` IN (:institutes) AND `user_inst`.`inst_perms` = 'user'";
            $parameters = [
                'value' => $this->value,
                'institutes' => $allowed['institutes']->pluck('id')
            ];

            $users = DBManager::get()->fetchFirst($sql.$where, $parameters);
        }

        return $users;
    }
}

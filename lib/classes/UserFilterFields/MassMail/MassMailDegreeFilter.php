<?php

namespace UserFilterFields\MassMail;

use UserFilterFields\DegreeCondition;
use MassMail\MassMailPermission;
use User;
use DBManager;
use PDO;

class MassMailDegreeFilter extends DegreeCondition
{
    /**
     * @see \UserFilterField::getTargets()
     */
    public static function getTargets()
    {
        return ['students'];
    }

    public function __construct($fieldId = '')
    {
        parent::__construct($fieldId);

        if (!MassMailPermission::has(User::findCurrent()->id, true)) {
            $this->validValues = [];

            $permission = MassMailPermission::getForUser(User::findCurrent(), true);

            foreach ($permission['allowed_degrees'] as [$id, $name]) {
                $this->validValues[$id] = (string) $name;
            }
        }
    }

    public function getUsers($restrictions = [])
    {
        $users = [];

        if (MassMailPermission::has(User::findCurrent()->id, true)) {
            $users = parent::getUsers($restrictions);
        } else if (count($this->validValues) > 0) {
            // Standard query getting the values without respecting other values.
            $select = "SELECT DISTINCT `" . $this->userDataDbTable . "`.`user_id` ";
            $from = "FROM `" . $this->userDataDbTable . "` ";
            $where = "WHERE `" . $this->userDataDbTable . "`.`" . $this->userDataDbField .
                "`" . $this->compareOperator . "?";
            $parameters = [$this->value];
            $joinedTables = [
                $this->userDataDbTable => true
            ];
            // Check if there are restrictions given.
            foreach ($restrictions as $otherField => $restriction) {
                // We only take the value into consideration if it represents a valid restriction.
                if ($this->relations[$otherField]) {
                    // Do we need to join in another table?
                    if (!$joinedTables[$restriction['table']]) {
                        $joinedTables[$restriction['table']] = true;
                        $from .= " INNER JOIN `" . $restriction['table'] . "` ON (`" .
                            $this->userDataDbTable . "`.`" .
                            $this->relations[$otherField]['local_field'] . "`=`" .
                            $restriction['table'] . "`.`" .
                            $this->relations[$otherField]['foreign_field'] . "`)";
                    }
                    // Expand WHERE statement with the value from restriction.
                    $where .= " AND `" . $restriction['table'] . "`.`" .
                        $restriction['field'] . "`" . $restriction['compare'] . "?";
                    $parameters[] = $restriction['value'];
                }
            }

            $where .= " AND `" . $this->userDataDbTable . "`.`" . $this->userDataDbField . "` IN (?)";
            $parameters[] = array_keys($this->validValues);

            // Get all the users that fulfill the condition.
            $users = \DBManager::get()->fetchFirst($select . $from . $where, $parameters);
        }

        return $users;
    }

    /**
     * Gets the value for the given user that is relevant for this
     * condition field. Here, this method looks up the study degree(s)
     * for the user. These can then be compared with the required degrees
     * whether they fit.
     *
     * @param  String $userId User to check.
     * @param  array $additional conditions that are required for check.
     * @return array The value(s) for this user.
     */
    public function getUserValues($userId, $additional = null)
    {
        if (MassMailPermission::has(User::findCurrent()->id, true)) {
            $result = parent::getUserValues($userId, $additional);
        } else {
            $result = [];
            $query = "SELECT DISTINCT `" . $this->userDataDbField . "` " .
                "FROM `" . $this->userDataDbTable . "` " .
                "WHERE `user_id`=?";
            $parameters = [$userId];
            // Additional requirements given...
            if (is_array($additional)) {

                // Don't use the same database field twice as this can only get ugly.
                $usedFields = [$this->userDataDbField];

                foreach ($additional as $a_condition) {
                    if ($a_condition->id != $this->id && $this->userDataDbTable == $a_condition->userDataDbTable &&
                        !in_array($a_condition->userDataDbField, $usedFields)) {
                        $query .= " AND `" . $a_condition->userDataDbField . "` " . $a_condition->compareOperator . "?";
                        $parameters[] = $a_condition->value;
                    }
                }
            }
            // Get semester of study for user.
            $stmt = DBManager::get()->prepare($query);
            $stmt->execute($parameters);
            while ($current = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $current[$this->userDataDbField];
            }
        }
        return $result;
    }

}

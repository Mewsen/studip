<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Nico Müller <nico.mueller@uni-oldenburg.de>
 * @author      Michael Riehemann <michael.riehemann@uni-oldenburg.de>
 * @author      Jan Hendrik Willms <jan.hendrik.willms@uni-oldenburg.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       2.1
 */

/**
 * AutoInsert.class.php
 * Provides functions required by StEP00216:
 * - Assign seminars for automatic registration of certain user types
 * - Maintenance of registration rules
 *
 * Example of use:
 * @code
 *
 *   # show all auto insert seminars
 *   $auto_sems = AutoInsert::getAllSeminars();
 *
 *   # Save a new auto insert seminar with the user status
 *   AutoInsert::saveSeminar($sem_id, $rechte);
 *
 * @endcode
 */
class AutoInsert
{
    private static $instance = null;
    protected static $seminar_cache = null;

    private $settings = [];

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->loadSettings();
    }

    private function loadSettings()
    {
        $query = "SELECT a.`seminar_id`, GROUP_CONCAT(a.`status`,IF(LENGTH(a.`range_id`)=0,':keine', " .
            "CONCAT(':',a.`range_id`))) AS range_status, a.`range_type`, s.`Name`, s.`Schreibzugriff`, " .
            "s.`start_time` ";
        $query .= "FROM `auto_insert_sem` a ";
        $query .= "JOIN `seminare` AS s USING (`Seminar_id`) ";
        $query .= "GROUP BY s.`seminar_id`, a.`range_type` ";
        $query .= "ORDER BY s.`Name`";
        $statement = DBManager::get()->query($query);

        $results   = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            if ($result['Schreibzugriff'] < 3) {
                $ranges = explode(',', $result['range_status']);

                foreach ($ranges as $range) {
                    $array                                       = explode(':', $range);
                    $key                                         = $array[1] . '.' . $array[0];
                    $this->settings[$result['range_type']][$key][$result['seminar_id']] = [
                        'Seminar_id'     => $result['seminar_id'],
                        'name'           => $result['Name'],
                        'Schreibzugriff' => $result['Schreibzugriff'],
                        'start_time'     => $result['start_time']
                    ];
                }
            }
        }
    }


    private function getUserSeminars($user_id, $seminare)
    {
        $statement = DBManager::get()->prepare("SELECT Seminar_id,s.name,s.Schreibzugriff,s.start_time,su.status
            FROM seminar_user su
            INNER JOIN seminare s USING(Seminar_id)
            WHERE user_id = ? AND Seminar_id IN(?)");
        $statement->execute([$user_id, $seminare]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trägt den Benutzer in den Eingestellten veranstaltungen automatisch ein.
     * @param type $user_id
     * @param type $status Wenn Status nicht angegeben wird, wird der Status des Users aus user_id genommen
     * @return array 'added' Namen der Seminare in die der User eingetragen wurde
     *                     array 'removed' Namen der Seminare aus denen der User ausgetragen wurde
     */
    public function saveUser($user_id, $status = false, $check = 'domain', $range_id = '')
    {
        $entries = [];
        if (!$status) {
            $status = $GLOBALS['perm']->get_perm($user_id);
        }

        switch ($check) {
            case 'degree':
                foreach (UserStudyCourse::findByUser($user_id) as $entry) {
                    $entries[] = $entry->abschluss_id;
                }
                break;
            case 'domain':
                foreach (UserDomain::getUserDomainsForUser($user_id) as $d) {
                    $entries[] = $d->id; //Domains des Users
                }

                if (count($entries) === 0) {
                    $entries[] = 'keine';
                }
                break;
            case 'institute':
                $memberships = InstituteMember::findBySQL(
                    "`user_id` = :user AND `inst_perms` = :perm",
                    ['user' => $user_id, 'perm' => $status]
                );

                $entries = array_map(function ($m) { return $m->institut_id; }, $memberships);
                break;
            case 'semester':
                foreach (UserStudyCourse::findByUser($user_id) as $entry) {
                    $entries[] = $entry->semester;
                }
                break;
            case 'subject':
                foreach (UserStudyCourse::findByUser($user_id) as $entry) {
                    $entries[] = $entry->fach_id;
                }
                break;

        }

        $entries = array_unique($entries);

        $settings     = [];
        $all_seminare = [];
        foreach ($entries as $entry) {
            $key = $entry . '.' . $status;
            if (is_array($this->settings[$check][$key])) {
                foreach ($this->settings[$check][$key] as $id => $value) {
                    $settings[$check][$id] = $value;
                    $all_seminare[$id] = $value;
                }
            }
        }
        foreach ($this->settings as $type) {
            foreach ($type as $range) {
                foreach ($range as $cid => $course) {
                    $all_seminare[$cid] = $course;
                }
            }
        }

        $seminare              = [];
        $seminare_tutor_dozent = [];
        foreach ($this->getUserSeminars($user_id, array_keys($all_seminare)) as $sem) {
            $seminare[$sem['Seminar_id']] = $sem;
            if (in_array($sem['status'], ['tutor', 'dozent'])) {
                $seminare_tutor_dozent[$sem['Seminar_id']] = $sem;
            }
        }
        $toAdd    = array_diff_key($settings[$check] ?: [], $seminare ?: []) ?: [];
        $toRemove = array_diff_key($all_seminare ?: [], $toAdd ?: [], $settings[$check] ?: [], $seminare_tutor_dozent ?: []) ?: [];

        $added   = [];
        $removed = [];

        foreach ($toAdd as $id => $seminar) {
            if ($this->addUser($user_id, $seminar)) $added[] = $seminar['name'];
        }
        foreach ($toRemove as $id => $seminar) {
            if ($this->removeUser($user_id, $seminar)) $removed[] = $seminar['name'];
        }

        return ['added' => $added, 'removed' => $removed];
    }

    private function addUser($user_id, $seminar)
    {
        $query = "INSERT IGNORE INTO seminar_user (Seminar_id, user_id, status, gruppe, mkdate)
            VALUES (?, ?, 'autor', ?, UNIX_TIMESTAMP())";
        $statement = DBManager::get()->prepare($query);
        $statement->execute([$seminar['Seminar_id'], $user_id, select_group($seminar['start_time'])]);
        $rows = $statement->rowCount();
        if ($rows > 0) return true;

        return false;
    }

    private function removeUser($user_id, $seminar)
    {
        $query = "DELETE FROM seminar_user " . "WHERE user_id = ? " . "AND Seminar_id = ? ";

        $statement = DBManager::get()->prepare($query);
        $statement->execute([$user_id, $seminar['Seminar_id']]);
        $rows = $statement->rowCount();

        $query             = "DELETE FROM statusgruppe_user " . "WHERE user_id = ? " . "AND statusgruppe_id IN (SELECT statusgruppe_id FROM statusgruppen WHERE range_id = ?)";
        $statusgruppe_stmt = DBManager::get()->prepare($query);
        $statusgruppe_stmt->execute([$user_id, $seminar['Seminar_id']]);
        $statusgruppe_rows = $statusgruppe_stmt->rowCount();
        if ($rows > 0 || $statusgruppe_rows > 0) return true;

        return false;
    }

    /**
     *
     * @param type $user_id
     */
    public function deleteUserSeminare($user_id)
    {
        $db = DBManager::get();
        $db->exec("DELETE FROM seminar_user " . "WHERE user_id = " . $db->quote($user_id));
    }

    /**
     * Tests if a seminar already has an autoinsert record
     * @param  string $seminar_id Id of the seminar
     * @return bool   Indicating whether the seminar already has an autoinsert record
     */
    public static function checkSeminar($seminar_id, $range_id = false, $range_type = 'domain')
    {
        $cached = self::getSeminarCache();

        if (!isset($cached[$seminar_id][$range_type][$range_id])) {
            $query = "SELECT `range_id`, 1
                      FROM `auto_insert_sem`
                      WHERE `seminar_id` = :course AND `range_type` = :type AND `range_id` = :range";
            $cached[$seminar_id][$range_type][$range_id] = DBManager::get()->fetchGroupedPairs(
                $query,
                ['course' => $seminar_id, 'type' => $range_type, 'range' => $range_id],
                function ($value) {
                    return (bool) $value;
                }
            );
        }

        return array_key_exists($range_id ?: '', $cached[$seminar_id])
             ? $cached[$seminar_id][$range_id ?: '']
             : false;
    }

    /**
     * Enables a seminar for autoinsertion of users with the given status(ses)
     * @param string $seminar_id Id of the seminar
     * @param mixed $status      Either a single string or an array of strings
     *                           containing the status(ses) to enable for
     *                           autoinsertion
     */
    public static function saveSeminar($seminar_id, $status, $range_id, $range_type = 'domain')
    {
        $query     = "INSERT INTO auto_insert_sem (seminar_id, status, range_id, range_type) VALUES (?, ?, ?, ?)";
        $statement = DBManager::get()->prepare($query);

        foreach ((array)$status as $s) {
            $statement->execute([$seminar_id, $s, $range_id, $range_type]);
        }
    }

    /**
     * Updates an autoinsert record for a given seminar, dependent on the
     * parameter $remove it either inserts or removes the record for the given
     * parameters
     *
     * @param string $seminar_id Id of the seminar
     * @param string $status     Status for autoinsertion
     * @param bool $remove       Whether the record should be added or removed
     */
    public static function updateSeminar($seminar_id, $domain = '', $status, $remove = false)
    {
        $query     = $remove ? "DELETE FROM auto_insert_sem WHERE seminar_id = ? AND status= ? AND domain_id = ?" : "INSERT IGNORE INTO auto_insert_sem (seminar_id, status,domain_id) VALUES (?, ?, ?)";
        $statement = DBManager::get()->prepare($query);
        $statement->execute([$seminar_id, $status, $domain]);

        if ($remove) {
            unset(self::getSeminarCache()[$seminar_id]);
        }
    }

    /**
     * Removes a seminar from the autoinsertion process.
     * @param string $seminar_id Id of the seminar
     * @param string $type which type of assignment to delete?
     * @param string $range_id Id of the range this seminar is assigned to
     */
    public static function deleteSeminar($seminar_id, $type, $range_id)
    {
        $query     = "DELETE FROM `auto_insert_sem` WHERE `seminar_id` = :course
            AND `range_type` = :type AND `range_id` = :range";
        $statement = DBManager::get()->prepare($query);
        $result = $statement->execute(['course' => $seminar_id, 'type' => $type, 'range' => $range_id]);

        unset(self::getSeminarCache()[$seminar_id]);

        return $result !== false;
    }

    /**
     * Returns a list of all seminars enabled for autoinsertion
     * @param  bool  Indicates whether only the seminar ids (true) or the full
     *               dataset shall be returned (false)
     * @return array The list of all enabled seminars (format according to $only_sem_id)
     */
    public static function getAllSeminars($only_sem_id = false)
    {
        if ($only_sem_id) {
            $query     = "SELECT DISTINCT seminar_id FROM auto_insert_sem";
            $statement = DBManager::get()->query($query);
            $results   = $statement->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $results = [];

            foreach (words('degree domain institute semester subject') as $type) {
                $select = "SELECT a.`seminar_id`, s.`Name`, a.`range_type`, a.`range_id`,
                    GROUP_CONCAT(a.`status`) AS status, s.`Schreibzugriff`, s.`start_time`";
                $from = " FROM `auto_insert_sem` a ";
                $join = [
                    "JOIN `seminare` s ON (s.`Seminar_id` = a.`seminar_id`)"
                ];
                $where = " WHERE a.`range_type` = :type ";
                $order = "GROUP BY a.`range_type`, a.`range_id` ORDER BY s.`start_time` DESC, s.`Name`, range_name";

                switch ($type) {
                    case 'domain':
                        $select .= ", IF(LENGTH(`range_id`) = 0, 'keine', a.`range_id`) AS range_name";
                        break;
                    case 'institute':
                        $select .= ", i.`Name` AS range_name ";
                        $join[] = "JOIN `Institute` i ON (i.`Institut_id` = a.`range_id`)";
                        break;
                    case 'degree':
                        $select .= ", d.`name` AS range_name";
                        $join[] = "JOIN `abschluss` d ON (d.`abschluss_id` = a.`range_id`)";
                        break;
                    case 'subject':
                        $select .= ", f.`name` AS range_name";
                        $join[] = "JOIN `fach` f ON (f.`fach_id` = a.`range_id`)";
                        break;
                    case 'semester':
                        $select .= ", a.`range_id` AS range_name";
                }

                $query = $select.$from.implode(' ', $join).$where.$order;
                $results += array_merge($results, DBManager::get()->fetchAll($query, ['type' => $type]));
            }
        }

        return $results;
    }

    /**
     * Returns a seminar's info for autoinsertion
     * @param  string $seminar_id Id of the seminar
     * @return array  The seminar's data as an associative array
     */
    public static function getSeminar($seminar_id)
    {
        $query = "SELECT a.seminar_id, GROUP_CONCAT(a.status) AS status,a.range_type, s.Name ";
        $query .= "FROM auto_insert_sem a ";
        $query .= "JOIN seminare AS s USING (Seminar_id) ";
        $query .= "WHERE a.seminar_id = ? ";
        $query .= "GROUP BY s.seminar_id";
        $statement = DBManager::get()->prepare($query);
        $statement->execute([$seminar_id]);

        $result           = $statement->fetch(PDO::FETCH_ASSOC);
        $result['status'] = explode(',', $result['status']);
        return $result;
    }

    /**
     * Store the user's automatic registration in a seminar redundantly to
     * avoid an annoying reregistration although the user explicitely left the
     * according seminar
     * @param string $user_id    Id of the user
     * @param string $seminar_id Id of the seminar
     */
    public static function saveAutoInsertUser($seminar_id, $user_id)
    {
        $query     = "INSERT INTO auto_insert_user (Seminar_id, user_id, mkdate)
                  SELECT ?, user_id, UNIX_TIMESTAMP() FROM auth_user_md5 WHERE
                  user_id=? AND perms NOT IN('root','admin')";
        $statement = DBManager::get()->prepare($query);
        $statement->execute([$seminar_id, $user_id]);
        return $statement->rowCount();
    }

    /**
     * Tests whether a user was already automatically registered for a certain
     * seminar.
     * @param  string $seminar_id Id of the seminar
     * @param  string $user_id    If of the user
     * @return bool   Indicates whether the user was already registered
     */
    public static function checkAutoInsertUser($seminar_id, $user_id)
    {
        $query     = "SELECT 1 FROM auto_insert_user WHERE seminar_id = ? AND user_id = ?";
        $statement = DBManager::get()->prepare($query);
        $statement->execute([$seminar_id, $user_id]);
        $result = $statement->fetchColumn();

        return $result > 0;
    }

    /**
     * Returns the cache for seminars.
     */
    protected static function getSeminarCache(): StudipCachedArray
    {
        if (self::$seminar_cache === null) {
            self::$seminar_cache = new StudipCachedArray('AutoInsertSeminars');
        }
        return self::$seminar_cache;
    }

    public static function getRangeTypes()
    {
        return  [
            'degree' => _('Abschluss'),
            'domain' => _('Domäne'),
            'institute' => _('Einrichtung'),
            'semester' => _('Fachsemester'),
            'subject' => _('Fach')
        ];
    }
}

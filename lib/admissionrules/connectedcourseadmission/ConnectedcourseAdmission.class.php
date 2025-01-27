<?php

/**
 * ConnectedAdmission.class.php
 *
 * Represents a rule for access only for members of connected courses.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Rasmus Fuhse <fuhse@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

class ConnectedcourseAdmission extends AdmissionRule
{
    /**
     * Standard constructor.
     *
     * @param  String ruleId
     */
    public function __construct($ruleId = '', $courseSetId = '')
    {
        parent::__construct($ruleId, $courseSetId);
        $this->default_message = _('Die Anmeldung ist nur für Mitglieder der dazu gehörigen Lehrveranstaltung möglich.');
    }


    /**
     * Gets some text that describes what this AdmissionRule (or respective
     * subclass) does.
     */
    public static function getDescription()
    {
        return _('Diese Art von Anmelderegel erlaubt die Anmeldung an bestimmte Studiengruppen. Nur wer in einer verknüpften Lehrveranstaltung eingetragen ist, darf sich auch in die Studiengruppe anmelden.');
    }

    /**
     * Return this rule's name.
     */
    public static function getName()
    {
        return _('Anmeldung nur über verknüpfte Lehrveranstaltung');
    }

    /**
     * Internal helper function for loading rule definition from database.
     */
    public function load()
    {

    }

    /**
     * Does the current rule allow the given user to register as participant
     * in the given course? Never happens here as admission is completely
     * locked.
     *
     * @param  String userId
     * @param  String courseId
     * @return Array Any errors that occurred on admission.
     */
    public function ruleApplies($userId, $courseId)
    {
        $errors = [];
        $statement = DBManager::get()->prepare("
            SELECT 1
            FROM `studygroup_courses`
                INNER JOIN `seminar_user` ON (`seminar_user`.`Seminar_id` = `studygroup_courses`.`course_id`)
            WHERE `studygroup_courses`.`studygroup_id` = :studygroup_id
                AND `seminar_user`.`user_id` = :user_id
            LIMIT 1
        ");
        $statement->execute([
            'user_id' => $userId,
            'studygroup_id' => $courseId
        ]);
        if (!$statement->fetch(PDO::FETCH_COLUMN)) {
            $errors[] = $this->getMessage();
        }
        return $errors;
    }

    /**
     * Helper function for storing data to DB.
     */
    public function store()
    {

    }

    /**
     * A textual description of the current rule.
     *
     * @return String
     */
    public function toString() {
        $factory = new Flexi\Factory(dirname(__FILE__).'/templates/');
        $tpl = $factory->open('info');
        $tpl->set_attribute('rule', $this);
        return $tpl->render();
    }

}

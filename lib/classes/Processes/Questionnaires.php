<?php

namespace Studip\Processes;

class Questionnaires implements ProcessProvider
{
    /**
     * Retrieves all active questionnaires accessible to the given user.
     *
     * This method fetches questionnaires that:
     * - Are currently active (within start and stop dates)
     * - Are visible
     * - Are assigned to courses, institutes, or status groups where the user is a member
     * - Are either unanswered or still editable by the user
     *
     * For each qualifying questionnaire, it creates a RunningProcess object containing:
     * - Context information (course/institute/status group)
     * - Questionnaire details (title, URL)
     * - Time constraints (start and end dates)
     * - Response statistics (number of answers / total possible respondents)
     *
     * @return array : RunningProcess[]
     */
    public static function getProcesses(\User $user) : array
    {
        $statement = \DBManager::get()->prepare("
            SELECT `questionnaires`.*
            FROM `questionnaires`
                INNER JOIN `questionnaire_assignments` USING (`questionnaire_id`)
                LEFT JOIN `seminar_user` ON (`seminar_user`.`Seminar_id` = `questionnaire_assignments`.`range_id` AND `questionnaire_assignments`.`range_type` = 'course')
                LEFT JOIN `user_inst` ON (`user_inst`.`Institut_id` = `questionnaire_assignments`.`range_id` AND `questionnaire_assignments`.`range_type` = 'institute')
                LEFT JOIN `statusgruppe_user` ON (`statusgruppe_user`.`statusgruppe_id` = `questionnaire_assignments`.`range_id` AND `questionnaire_assignments`.`range_type` = 'statusgruppe')
                LEFT JOIN `statusgruppen` ON (`statusgruppen`.`statusgruppe_id` = `questionnaire_assignments`.`range_id` AND `questionnaire_assignments`.`range_type` = 'statusgruppe')
                LEFT JOIN `seminar_user` AS `teacher` ON (`teacher`.`Seminar_id` = `statusgruppen`.`range_id` AND `questionnaire_assignments`.`range_type` = 'statusgruppe' AND `teacher`.`status` IN ('tutor', 'dozent'))
            WHERE `questionnaires`.`startdate` <= UNIX_TIMESTAMP()
                AND `questionnaires`.`stopdate` >= UNIX_TIMESTAMP()
                AND `questionnaires`.`visible` = 1
                AND (`seminar_user`.`user_id` = :user_id OR `teacher`.`user_id` = :user_id OR `user_inst`.`user_id` = :user_id OR `statusgruppe_user`.`user_id` = :user_id)
            GROUP BY `questionnaires`.`questionnaire_id`
        ");
        $statement->execute(['user_id' => $user->id]);
        $questionnaires = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $result = [];

        foreach ($questionnaires as $questionnaire_data) {
            $questionnaire = \Questionnaire::buildExisting($questionnaire_data);
            if ($questionnaire->isViewable() && (!$questionnaire->isAnswered() || $questionnaire->isEditable())) {
                foreach ($questionnaire->assignments as $assignment) {
                    if ($questionnaire->isAnswerable() || $questionnaire->isEditable()) {

                        $answers = $questionnaire->countAnswers();
                        if ($assignment->range_type === 'course') {
                            $allPersons = \CourseMember::countBySQL("`Seminar_id` = ?", [$assignment->range_id]);
                            $range_id = $assignment->range_id;
                        } elseif($assignment->range_type === 'institute') {
                            $allPersons = \InstituteMember::countBySQL("`Institut_id` = ?", [$assignment->range_id]);
                            $range_id = $assignment->range_id;
                        } elseif($assignment->range_type === 'statusgruppe') {
                            $allPersons = \StatusgruppeUser::countBySQL("`statusgruppe_id` = ?", [$assignment->range_id]);
                            $statusgroup = \Statusgruppen::find($assignment->range_id);
                            if ($statusgroup) {
                                $range_id = $statusgroup->range_id;
                            }
                        }

                        $result[] = new \RunningProcess(
                            $range_id,
                            \Icon::create("vote"),
                            _('Fragebogen'),
                            $questionnaire->isEditable()
                                ? \URLHelper::getURL('dispatch.php/questionnaire/evaluate/'.$questionnaire->id)
                                : \URLHelper::getURL('dispatch.php/questionnaire/answer/'.$questionnaire->id),
                            $questionnaire->startdate,
                            $questionnaire->stopdate,
                            true,
                            $questionnaire->title,
                            $questionnaire->isEditable() ? $answers.'/'.$allPersons : '',
                            $questionnaire->isEditable() ? sprintf(_('Rücklaufquote: %s'), $answers.'/'.$allPersons) : ''
                        );
                    }
                }
            }
        }
        return $result;
    }
}

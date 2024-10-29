<?php
trait StudipTreeNodeCourseTrait
{
    protected function getCoursesCondition(
        string $alias,
        string $semester_id,
        $sem_class,
        string $searchterm = '',
        array $courses = []
    ): array {
        $parameters = [];
        $order_by = [];

        $condition = " JOIN `seminare` s ON (s.`Seminar_id` = {$alias}.`seminar_id`)";

        if ($semester_id !== 'all') {
            $condition .= " LEFT JOIN `semester_courses` sc ON ({$alias}.`seminar_id` = sc.`course_id`)
                  LEFT JOIN `semester_data` sd USING (`semester_id`)
                  WHERE (sc.`semester_id` = :semester OR sc.`semester_id` IS NULL)";
            $parameters[':semester'] = $semester_id;
            $order_by[] = 'sd.`beginn`';
        } else {
            $condition .= " WHERE 1";
        }

        if (!$GLOBALS['perm']->have_perm(Config::get()->SEM_VISIBILITY_PERM)) {
            $condition .= " AND s.`visible` = 1";
        }

        if ($sem_class) {
            $condition .= "  AND s.`status` IN (:types)";
            $parameters['types'] = array_map(
                function ($type) {
                    return $type['id'];
                },
                array_filter(
                    SemType::getTypes(),
                    function ($t) use ($sem_class) {
                        return $t['class'] === $sem_class;
                    }
                )
            );
        }

        if ($searchterm) {
            $condition .= " AND s.`Name` LIKE :searchterm";
            $parameters['searchterm'] = '%' . trim($searchterm) . '%';
        }

        if ($courses) {
            $condition .= " AND {$alias}.`seminar_id` IN (:courses)";
            $parameters['courses'] = $courses;
        }

        if (Config::get()->IMPORTANT_SEMNUMBER) {
            $order_by[] = 's.`VeranstaltungsNummer`';
        }
        $order_by[] = 's.`Name`';

        return [$condition, $parameters, $order_by];
    }
}

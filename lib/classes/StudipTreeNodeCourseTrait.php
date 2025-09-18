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
        $joins = [];
        $conditions = [];
        $parameters = [];
        $order_by = [];

        $joins[] = "JOIN `seminare` s ON (s.`Seminar_id` = {$alias}.`seminar_id`)";

        if ($semester_id !== 'all') {
            $joins[] = "LEFT JOIN `semester_courses` sc ON (s.`seminar_id` = sc.`course_id`)";
            $joins[] = "LEFT JOIN `semester_data` sd USING (`semester_id`)";
            $conditions[] = "(sc.`semester_id` = :semester OR sc.`semester_id` IS NULL)";
            $parameters[':semester'] = $semester_id;
            $order_by[] = 'sd.`beginn`';
        }

        if (!$GLOBALS['perm']->have_perm(Config::get()->getValue('SEM_VISIBILITY_PERM'))) {
            $conditions[] = "s.`visible` = 1";
        }

        if ($sem_class !== 0) {
            $conditions[] = "s.`status` IN (:types)";
            $semclass = new SemClass($sem_class);
            $parameters['types'] = array_keys($semclass->getSemTypes());
        }

        if ($searchterm) {
            $lang_name = "s.`Name`";
            if (I18N::isEnabled() && $_SESSION['_language'] !== I18NString::getDefaultLanguage()) {
                $lang_name = "IFNULL(`i18n`.`value`, {$lang_name})";

                $joins[] = "LEFT JOIN `i18n`
                          ON `i18n`.`object_id` = s.`Seminar_id`
                            AND `i18n`.`table` = 'seminare'
                            AND `i18n`.`field` = 'name'
                            AND `lang` = :language";
                $parameters['language'] = $_SESSION['_language'];
            }

            $parameters['searchterm'] = '%' . trim($searchterm) . '%';

            # Search by lecturer's name
            $joins[] = "LEFT JOIN `seminar_user` su ON (su.`Seminar_id` = s.`Seminar_id` AND su.`status` = 'dozent')";
            $joins[] = "LEFT JOIN `auth_user_md5` a ON (a.`user_id` = su.`user_id`)";

            $conditions[] = '(' . implode(' OR ', [
                "CONCAT(IFNULL(s.`VeranstaltungsNummer`, '') , ' ', {$lang_name}) LIKE :searchterm",
                "CONCAT(a.`Nachname`, ', ', a.`Vorname`, ' ', a.`Nachname`) LIKE :searchterm"
            ]) . ')';
        }

        if ($courses) {
            $conditions = "s.`seminar_id` IN (:courses)";
            $parameters['courses'] = $courses;
        }

        if (Config::get()->getValue('IMPORTANT_SEMNUMBER')) {
            $order_by[] = 's.`VeranstaltungsNummer`';
        }
        $order_by[] = 's.`Name`';

        return [
            implode(' ', $joins) . ' WHERE ' . implode(' AND ', $conditions),
            $parameters,
            $order_by
        ];
    }
}

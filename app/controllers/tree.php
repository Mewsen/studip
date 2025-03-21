<?php

class TreeController extends AuthenticatedController
{
    public function export_csv_action()
    {
        if (!Request::isPost()) {
            throw new MethodNotAllowedException();
        }

        $ids = explode(',', Request::get('courses', ''));
        $courses = Course::findMany($ids);

        $captions = [
            _('Veranstaltungsnummer'),
            _('Name'),
            _('Semester'),
            _('Zeiten'),
            _('Lehrende'),
            _('Bereich')
        ];

        $data = [];
        foreach ($courses as $course) {
            $sem = Seminar::getInstance($course->id);
            $lecturers = SimpleCollection::createFromArray(
                CourseMember::findByCourseAndStatus($course->id, 'dozent')
            )->orderBy(
                'position, nachname, vorname'
            )->map(
                function($member) { return $member->getUserFullname(); }
            );

            $studyAreaPaths = [];
            foreach ($course->study_areas as $area) {
                $studyAreaPaths[] = $area->getPath(' > ');
            }

            $data[] = [
                $course->veranstaltungsnummer,
                $course->getFullname('type-number-name'),
                $course->getTextualSemester(),
                strip_tags($sem->getDatesExport()),
                implode(', ', $lecturers),
                implode("\n", $studyAreaPaths)
            ];
        }

        $tmpname = md5(uniqid('ErgebnisVeranstaltungssuche'));
        if (array_to_csv($data, $GLOBALS['TMP_PATH'] . '/' . $tmpname, $captions)) {
            $this->render_text(FileManager::getDownloadURLForTemporaryFile(
                $tmpname,
                'veranstaltungssuche.csv'
            ));
        } else {
            $this->set_status(400, 'The csv could not be created.');
        }
    }
}

<?php

namespace JsonApi\Routes\Mvv;

use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\NonJsonApiController;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamFactoryInterface;

class ComponentVersionsDeep extends NonJsonApiController
{
    protected $allowedFilteringParameters = ['q', 'institute', 'semester', 'section'];

    public function __construct(
        ContainerInterface $container,
        private StreamFactoryInterface $streamFactory
    ) {
        parent::__construct($container);
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $component_version = \StgteilVersion::find($args['id']);
        if (!$component_version) {
            throw new RecordNotFoundException();
        }

        $parameters = $request->getQueryParams();

        $this->validateParameters($parameters);

        $data = $this->getVersionData($component_version, $parameters);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream(json_encode($data)));
    }

    private function validateParameters(array $parameters): void
    {
        if (!isset($parameters['semester'])) {
            throw new BadRequestException('Parameter semester is missing');
        }

        if (!\Semester::exists($parameters['semester'])) {
            throw new BadRequestException('Semester not found');
        }
    }

    private function getVersionData(\StgteilVersion $version, array $parameters): array
    {
        $data = [
            'id' => $version->id,
            'display-name' => $version->getDisplayName(),
            'start-semester' => $version->start_semester ? $this->getSemesterData($version->start_semester) : '',
            'end-semester' => $version->end_semester ? $this->getSemesterData($version->end_semester) : '',
            'code' => $version->code,
            'description' => $version->beschreibung,
            'date-of-decision' => $version->beschlussdatum,
            'edition-number' => $version->fassung_nr,
            'edition-type' => \Config::get()->MVV_STGTEILVERSION['FASSUNG_TYP'][$version->fassung_typ] ?? '',
            'status' => \Config::get()->MVV_STGTEILVERSION['STATUS']['values'][$version->stat],
            'sections' => $this->getSectionsData($version, $parameters),
        ];
        return $data;
    }

    private function getSectionsData(\StgteilVersion $version, array $parameters): array
    {
        $data = [];
        foreach ($version->abschnitte as $section) {
            $data[] = [
                'id' => $section->id,
                'display-name' => $section->getDisplayName(),
                'comment' => $section->kommentar,
                'position' => $section->position,
                'cp' => $section->kp,
                'caption' => $section->ueberschrift,
                'modules' => $this->getModulesData($section, $parameters),
            ];
        }
        return $data;
    }

    private function getModulesData(\StgteilAbschnitt $section, array $parameters): array
    {
        $status = \Config::get()->MVV_MODUL['STATUS']['values'];
        $semester = \Semester::find($parameters['semester']);
        $modules_filtered = $section->module->filter(
            fn(\Modul $module) =>
                ((empty($module->start_semester) || $module->start_semester->beginn <= $semester->ende)
                && (empty($module->end_semester) || $module->end_semester->ende >= $semester->beginn)
                && $status[$module->stat]['public'] === 1)
        );
        $data = [];
        foreach ($modules_filtered as $module) {
            $data[] = [
                'id' => $module->id,
                'display-name' => (string) $module->getDisplayName(),
                'code' => (string) $module->code,
                'date-of-decision' => $module->beschlussdatum ? date('c', $module->beschlussdatum) : '',
                'edition-number' => (string) $module->fassung_nr,
                'edition-type' => \Config::get()->MVV_MODUL['FASSUNG_TYP'][$module->fassung_typ] ?? '',
                'version-number' => (string) $module->version,
                'semester-duration' => (string) $module->dauer,
                'capacity' => (string) $module->kapazitaet,
                'cp' => $module->kp,
                'workload-self' => (string) $module->wl_selbst,
                'workload-exam' => (string) $module->wl_pruef,
                'examination-period' => \Config::get()->MVV_MODUL['PRUEF_EBENE']['values'][$module->pruef_ebene] ?? '',
                'grade-factor' => (string) $module->faktor_note,
                'foreign-key' => (string) $module->flexnow_modul,
                'name' => (string) $module->deskriptoren->bezeichnung,
                'responsible' => (string) $module->deskriptoren->verantwortlich,
                'prerequisite' => (string) $module->deskriptoren->voraussetzung,
                'objectives' => (string) $module->deskriptoren->kompetenzziele,
                'content' => (string) $module->deskriptoren->inhalte,
                'literature' => (string) $module->deskriptoren->literatur,
                'links' => (string) $module->deskriptoren->links,
                'comment' => (string) $module->deskriptoren->kommentar,
                'cycle' => (string) $module->deskriptoren->turnus,
                'comment-capacity' => (string) $module->deskriptoren->kommentar_kapazitaet,
                'comment_sws' => (string) $module->deskriptoren->kommentar_sws,
                'status' => \Config::get()->MVV_MODUL['STATUS']['values'][$module->stat],
                'module-languages' => $this->getModuleLanguagesData($module),
                'module-section-data' => $this->getModuleSectionData(
                    $module->abschnitte_modul->findOneBy('abschnitt_id', $section->id)),
                'module-components' => $this->getModuleComponentsData($module, $parameters),
                'start-semester' => $module->start_semester ? $this->getSemesterData($module->start_semester) : '',
                'end-semester' => $module->end_semester ? $this->getSemesterData($module->end_semester) : '',
            ];
        }
        return $data;
    }

    private function getSemesterData(\Semester $semester): array
    {
        return [
            'id' => $semester->id,
            'name' => $semester->name,
            'short-name' => $semester->semester_token,
            'semester-start' => $semester->beginn,
            'semester-end' => $semester->ende,
            'foreign-key' => $semester->external_id,
            'teaching-start' => $semester->vorles_beginn,
            'teaching-end' => $semester->vorles_ende,
            'semester-switch-time' => $semester->sem_wechsel,
        ];
    }

    private function getModuleComponentsData(\Modul $module, array $parameters): array
    {
        foreach ($module->modulteile as $component) {
            $data[] = [
                'id' => $component->id,
                'name' => (string) $component->deskriptoren->bezeichnung,
                'position' => $component->position,
                'foreign-key' => $component->flexnow_modul,
                'number' => $component->nummer,
                'number-label' => \Config::get()->MVV_MODULTEIL['NUM_BEZEICHNUNG']['values'][$component->num_bezeichnung] ?? '',
                'teaching-method' => \Config::get()->MVV_MODULTEIL['LERNLEHRFORM']['values'][$component->lernlehrform] ?? '',
                'semester' => $component->semester,
                'number-of-participants' => $component->kapazitaet,
                'cp' => $component->kp,
                'sws' => $component->sws,
                'workload-compulsory' => $component->wl_praesenz,
                'workload-preparation' => $component->wl_bereitung,
                'workload-self' => $component->wl_selbst,
                'workload-exam' => $component->wl_pruef,
                'share-of-grade' => $component->anteil_note,
                'compensable' => $component->ausgleichbar,
                'compulsory-attendance' => $component->pflicht,
                'prerequisites' => $component->deskriptoren->voraussetzung,
                'comment' => $component->deskriptoren->kommentar,
                'comment-capacity' => $component->deskriptoren->kommentar_kapazitaet,
                'comment-wl-compulsory' => $component->deskriptoren->kommentar_wl_praesenz,
                'comment-wl-preparation' => $component->deskriptoren->kommentar_wl_bereitung,
                'comment-wl-self' => $component->deskriptoren->kommentar_wl_selbst,
                'comment-wl-exam' => $component->deskriptoren->kommentar_wl_pruef,
                'exam-prerequisites' => $component->deskriptoren->pruef_vorleistung,
                'exam-requirements' => $component->deskriptoren->pruef_leistung,
                'comment-compulsory-attendance' => $component->deskriptoren->kommentar_pflicht,
                'courses' => $this->getCoursesData($component, $parameters),
                'course-semesters' => $this->getModuleComponentSectionData($component),
            ];
        }
        return $data;
    }

    private function getCoursesData(\Modulteil $component, array $parameters): array
    {
        $course_ids = [];
        foreach ($component->lvgruppen as $lvgruppe) {
            $course_ids += $lvgruppe->courses->pluck('id');
        }

        if (count($course_ids) === 0) {
            return [];
        }

        $courses = \Course::findBySQL(
            '`seminar_id` IN (?) AND `visible` = 1 ORDER BY start_time, name',
            [$course_ids]
        );
        $semester = \Semester::find($parameters['semester']);
        $courses = array_filter($courses, fn (\Course $course) => $course->isInSemester($semester));

        $data = [];
        foreach ($courses as $course) {
            $data[] = [
                'id' => $course->id,
                'name' => $course->name,
                'number' => $course->veranstaltungsnummer
            ];
        }
        return $data;
    }

    private function getModuleComponentSectionData(\Modulteil $component): array
    {
        $data = [];
        foreach ($component->abschnitt_assignments as $assignment) {
            $data = [
                'course-semester' => $assignment->fachsemester,
                'differentiation' => $assignment->differenzierung,
            ];
        }
        return $data;
    }

    private function getModuleSectionData(\StgteilabschnittModul $module_section): array
    {
        return [
            'id' => $module_section->abschnitt_modul_id,
            'name' => $module_section->bezeichnung,
            'code' => $module_section->modulcode,
            'position' => $module_section->position,
            'foreign-key' => $module_section->flexnow_modul,
        ];
    }

    private function getModuleLanguagesData(\Modul $module): array
    {
        $languages = \Config::get()->MVV_MODUL['SPRACHE']['values'];
        $data = [];
        foreach ($module->languages as $language) {
            $data[] = [
                'language' => $language->lang,
                'name' => $languages[$language->lang]['name'],
                'position' => $language->position,
            ];
        }
        return $data;
    }
}

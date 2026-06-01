<?php

namespace JsonApi\Routes\Courses;

use Institute;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Semester;

class CoursesByInstituteIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    protected $allowedFilteringParameters = ['semester'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $institute = Institute::find($args['id']);
        if (!$institute) {
            throw new RecordNotFoundException();
        }

        $this->validateFilters();
        $courses = $this->findCoursesByInstitute($institute, $this->getSemesterFilter());
        [$offset, $limit] = $this->getOffsetAndLimit();

        return $this->getPaginatedContentResponse($courses->limit($offset, $limit), count($courses));
    }

    private function validateFilters(): void
    {
        $filtering = $this->getQueryParameters()->getFilteringParameters();

        if (isset($filtering['semester']) && !Semester::find($filtering['semester'])) {
            throw new BadRequestException('Invalid "semester".');
        }
    }

    private function getSemesterFilter(): ?Semester
    {
        $filtering = $this->getQueryParameters()->getFilteringParameters();

        if (!isset($filtering['semester'])) {
            return null;
        }

        return Semester::find($filtering['semester']);
    }

    private function findCoursesByInstitute(Institute $institute, ?Semester $semester)
    {
        $courses = $institute->courses;

        if (!$GLOBALS['perm']->have_perm(\Config::get()->SEM_VISIBILITY_PERM)) {
            $courses = $courses->filter(fn($course) => $course->visible);
        }

        if ($semester) {
            $courses = $courses->filter(fn($course) => $course->isInSemester($semester));
        }

        return $courses;
    }
}

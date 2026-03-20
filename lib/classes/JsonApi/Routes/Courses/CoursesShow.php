<?php

namespace JsonApi\Routes\Courses;

use JsonApi\Schemas\Course as CourseSchema;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

/**
 * Zeigt eine bestimmte Veranstaltung an.
 */
class CoursesShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        CourseSchema::REL_BLUBBER,
        CourseSchema::REL_END_SEMESTER,
        CourseSchema::REL_EVENTS,
        CourseSchema::REL_FEEDBACK,
        CourseSchema::REL_INSTITUTE,
        CourseSchema::REL_MEMBERSHIPS,
        CourseSchema::REL_NEWS,
        CourseSchema::REL_PARTICIPATING_INSTITUTES,
        CourseSchema::REL_SEM_CLASS,
        CourseSchema::REL_SEM_TYPE,
        CourseSchema::REL_START_SEMESTER,
        CourseSchema::REL_STATUS_GROUPS,
        CourseSchema::REL_WIKI_PAGES,
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $course = \Course::find($args['id']);
        if (!$course) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowCourse($this->getUser($request), $course)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($course);
    }
}

<?php

namespace JsonApi\Routes\StudyAreas;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\Courses\Authority;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ByCourseIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $course = \Course::find($args['id']);
        if (!$course) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowCourse($this->getUser($request), $course)) {
            throw new AuthorizationFailedException();
        }

        $areas = $course->study_areas;
        [$offset, $limit] = $this->getOffsetAndLimit();

        return $this->getPaginatedContentResponse($areas->limit($offset, $limit), count($areas));
    }
}

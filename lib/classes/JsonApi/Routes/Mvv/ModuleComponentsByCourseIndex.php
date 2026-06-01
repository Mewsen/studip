<?php

namespace JsonApi\Routes\Mvv;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\Courses\Authority;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ModuleComponentsByCourseIndex extends JsonApiController
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

        $components = new \SimpleCollection();

        foreach ($course->lvgruppen as $lvg) {
            $components->merge($lvg->modulteile);
        }

        [$offset, $limit] = $this->getOffsetAndLimit();

        return $this->getPaginatedContentResponse($components->limit($offset, $limit), count($components));
    }
}

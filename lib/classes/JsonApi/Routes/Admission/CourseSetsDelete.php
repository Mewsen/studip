<?php

namespace JsonApi\Routes\Admission;

use JsonApi\Errors\AuthorizationFailedException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

/**
 * Deletes a courseset
 */
class CourseSetsDelete extends JsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        $cs = new \CourseSet($args['id']);
        if (!$cs->getChdate()) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canUpdateCourseSet($user, $cs)) {
            throw new AuthorizationFailedException();
        }

        $cs->delete();

        return $this->getCodeResponse(204);
    }
}

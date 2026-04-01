<?php

namespace JsonApi\Routes\Institutes;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Schemas\Institute as InstituteSchema;

class ByFacultyIndex extends JsonApiController
{
    protected $allowedIncludePaths = [
        InstituteSchema::REL_FACULTY,
        InstituteSchema::REL_STATUS_GROUPS,
        InstituteSchema::REL_COURSES_OF_STUDY,
    ];

    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        $institute = \Institute::find($args['id']);

        if (!$institute) {
            throw new RecordNotFoundException();
        }

        $institutes = $institute->sub_institutes;
        $total = count($institutes);
        [$offset, $limit] = $this->getOffsetAndLimit();

        return $this->getPaginatedContentResponse($institutes->limit($offset, $limit), $total);
    }
}

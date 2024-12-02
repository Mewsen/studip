<?php

namespace JsonApi\Routes\Mvv;

use JsonApi\Schemas\Subject;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

class SubjectsByCourseOfStudyComponentsShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        Subject::REL_DEPARTMENTS,
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $component = \StudiengangTeil::find($args['id']);
        if (empty($component->fach)) {
            throw new RecordNotFoundException('Could not find subject.');
        }

        return $this->getContentResponse($component->fach);
    }
}

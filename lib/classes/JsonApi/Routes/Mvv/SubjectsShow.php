<?php

namespace JsonApi\Routes\Mvv;

use JsonApi\Schemas\Subject;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

class SubjectsShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        Subject::REL_DEPARTMENTS,
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $subject = \Fach::find($args['id']);
        if (!$subject) {
            throw new RecordNotFoundException('Could not find subject.');
        }

        return $this->getContentResponse($subject);
    }
}

<?php

namespace JsonApi\Routes\Mvv;

use JsonApi\Schemas\CourseOfStudyComponent;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

class CourseOfStudyComponentsShow extends JsonApiController
{

    protected $allowedIncludePaths = [
        CourseOfStudyComponent::REL_SUBJECT,
        CourseOfStudyComponent::REL_VERSIONS,
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $component = \StudiengangTeil::find($args['id']);
        if (!$component) {
            throw new RecordNotFoundException();
        }

        return $this->getContentResponse($component);
    }
}

<?php

namespace JsonApi\Routes;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

/**
 * List all the semesters.
 */
class DatafieldsIndex extends JsonApiController
{
    protected $allowedFilteringParameters = ['object_type'];
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        [$offset, $limit] = $this->getOffsetAndLimit();

        $params = $this->getQueryParameters();
        $filtering = $params->getFilteringParameters();
        if (isset($filtering['object_type'])) {
            $datafields = \DataField::getDataFields($filtering['object_type']);
        } else {
            $datafields = \DataField::getDataFields();
        }
        $datafields = array_filter(
            $datafields,
            fn($field) => $field->accessAllowed()
        );

        return $this->getPaginatedContentResponse(
            array_slice($datafields, $offset, $limit),
            count($datafields)
        );
    }
}

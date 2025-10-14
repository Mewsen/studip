<?php

namespace JsonApi\Routes\Mvv;

use Degree;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class DegreesIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        [$offset, $limit] = $this->getOffsetAndLimit();

        return $this->getPaginatedContentResponse(
            Degree::findBySQL("1 ORDER BY name LIMIT {$offset}, {$limit}"),
            Degree::countBySql('1')
        );
    }
}

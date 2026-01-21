<?php

namespace JsonApi\Routes\Lti;

use JsonApi\Errors\AuthorizationFailedException;
use Lti\ResourceLink;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use JsonApi\JsonApiController;
use JsonApi\Schemas\Lti\Resource as ResourceSchema;
use Range;

class ResourceIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedFilteringParameters = [
        'launch-types'
    ];

    protected $allowedIncludePaths = [
        ResourceSchema::REL_RANGE,
        ResourceSchema::REL_REGISTRATION,
        ResourceSchema::REL_DEPLOYMENT
    ];

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $range = get_object_by_range_id($args['range_id']);
        if (!Authority::canShowRegistration($range, $this->getUser($request))) {
            throw new AuthorizationFailedException();
        }

        $baseQuery = $this->resolveBaseQuery($range);

        $totalCount = ResourceLink::countBySql(...$baseQuery);
        $resourceLinks = ResourceLink::findBySQL(
            $baseQuery[0]." ORDER BY `position` LIMIT ?, ?",
           [
               ...$baseQuery[1],
               ...$this->getOffsetAndLimit()
           ]
        );

        return $this->getPaginatedContentResponse($resourceLinks, $totalCount);
    }

    private function resolveBaseQuery(Range $range): array
    {
        $filtering = $this->getQueryParameters()->getFilteringParameters() ?? [];

        $baseQuery = [
            "`course_id` = :range_id",
            [
                'range_id' => $range->id
            ]
        ];

        if (isset($filtering['launch-type'])) {
            $baseQuery[0] .= " AND `launch_type` IN (:launch_types)";
            $baseQuery[1]['launch_types'] = explode(',', $filtering['launch-types']);
        }

        return $baseQuery;
    }
}

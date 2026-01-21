<?php

namespace JsonApi\Routes\Lti;

use Range;
use Lti\Publication;
use JsonApi\JsonApiController;
use JsonApi\Errors\AuthorizationFailedException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use JsonApi\Schemas\Lti\Publication as PublicationSchema;

class PublicationIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedFilteringParameters = [
        'status'
    ];

    protected $allowedIncludePaths = [
        PublicationSchema::REL_RANGE,
        PublicationSchema::REL_USER,
        PublicationSchema::REL_MEMBERS,
    ];

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $range = get_object_by_range_id($args['range_id']);
        if (!Authority::canShowRegistration($range, $this->getUser($request))) {
            throw new AuthorizationFailedException();
        }

        $baseQuery = $this->resolveBaseQuery($range);

        $totalCount = Publication::countBySql(...$baseQuery);
        $resourceLinks = Publication::findBySQL(
            $baseQuery[0]." LIMIT ?, ?",
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
            "`range_id` = :range_id",
            [
                'range_id' => $range->id
            ]
        ];

        if (isset($filtering['status'])) {
            $baseQuery[0] .= " AND `status` IN (:status)";
            $baseQuery[1]['status'] = explode(',', $filtering['status']);
        }

        return $baseQuery;
    }
}

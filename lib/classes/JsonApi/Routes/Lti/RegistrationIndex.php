<?php

namespace JsonApi\Routes\Lti;

use Lti\Registration;
use JsonApi\Errors\AuthorizationFailedException;
use LtiToolModule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use JsonApi\JsonApiController;
use JsonApi\Schemas\Lti\Registration as RegistrationSchema;

class RegistrationIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedFilteringParameters = [
        'role'
    ];
    protected $allowedIncludePaths = [
        RegistrationSchema::REL_RANGE
    ];

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $range = null;
        if (isset($args['range_id'])) {
            $range = get_object_by_range_id($args['range_id']);

            if (!Authority::canShowRegistration($range, $this->getUser($request))) {
                throw new AuthorizationFailedException();
            }
        }

        $filtering = $this->getQueryParameters()->getFilteringParameters() ?? [];
        $registrationRoles = $filtering['role'] ?? ['tool', 'platform'];

        $selectQuery = [
            "`role` IN (:role)",
            [
                'role' => $registrationRoles
            ]
        ];

        if (!LtiToolModule::isAdmin($this->getUser($request)->id)) {
            $selectQuery[0] .= " AND `range_id` IN (:range_ids)";
            $selectQuery[1]['range_ids'] = [$range?->id, 'global'];
        }

        $totalCount = Registration::countBySql(...$selectQuery);
        $registrations = Registration::findBySQL(
            $selectQuery[0]." ORDER BY `name` LIMIT ?, ?",
           [
               ...$selectQuery[1],
               ...$this->getOffsetAndLimit()
           ]
        );

        return $this->getPaginatedContentResponse($registrations, $totalCount);
    }
}

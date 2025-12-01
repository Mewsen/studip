<?php

namespace JsonApi\Routes\Scores;

use JsonApi\NonJsonApiController;
use JsonApi\Errors\AuthorizationFailedException;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Schema\ErrorCollection;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
class ScoresIndex extends NonJsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $performedBy = $this->getUser($request);
        if (!Authority::canIndexScores($performedBy)) {
            throw new AuthorizationFailedException();
        }

        $params = $request->getQueryParams();

        list($offset, $limit) = $this->getOffsetAndLimit($params);

        [$usersList, $total] = \UserInfo::loadPaginatedUsersListForScores($limit, $offset);

        $scoreUsers = \Score::getScoreContent($usersList);

        $result = [
            'list' => array_values($scoreUsers),
            'total' => $total,
        ];

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write((string) json_encode($result));

        return $response;
    }

    protected function getOffsetAndLimit($params): array
    {
        return [
            $params && array_key_exists('offset', $params) ? (int) $params['offset'] : 0,
            $params && array_key_exists('limit', $params) ? (int) $params['limit'] : \Config::get()->ENTRIES_PER_PAGE,
        ];
    }
}

<?php

namespace JsonApi\Routes\Community;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Community\CommunityGroup as CommunityGroup;

class CommunityGroupsOfUsersIndex extends JsonApiController
{
    protected $allowedIncludePaths = ['participants', 'pinboard-items'];
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        $user_id = $args['id'];
        if ($user->id !== $user_id) {
             throw new AuthorizationFailedException();
        }
        $resources = CommunityGroup::findByUserId($user_id);
        $total = count($resources);
        list($offset, $limit) = $this->getOffsetAndLimit();

        return $this->getPaginatedContentResponse(array_slice($resources, $offset, $limit), $total);
    }
}
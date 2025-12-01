<?php

namespace JsonApi\Routes\Contacts;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserContactGroupsIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        if (!Authority::canIndexGroups($user)) {
            throw new AuthorizationFailedException();
        }

        $userContactGroups = \SimpleCollection::createFromArray(\ContactGroup::findBySQL('owner_id = ?', [$user->id]));

        list($offset, $limit) = $this->getOffsetAndLimit();

        return $this->getPaginatedContentResponse($userContactGroups->limit($offset, $limit), count($userContactGroups));
    }
}

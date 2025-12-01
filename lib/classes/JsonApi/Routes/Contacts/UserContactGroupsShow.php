<?php

namespace JsonApi\Routes\Contacts;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserContactGroupsShow extends JsonApiController
{
    protected $allowedIncludePaths = ['owner', 'items'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        if (!$resource = \ContactGroup::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowGroups($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($resource);
    }
}

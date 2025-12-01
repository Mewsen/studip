<?php

namespace JsonApi\Routes\Contacts;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserContactGroupsUpdate extends JsonApiController
{
    use ValidationTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!$resource = \ContactGroup::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canUpdateGroups($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        $name = self::arrayGet($json, 'data.attributes.name', '');
        $resource->name = $name;
        $resource->store();

        return $this->getContentResponse($resource);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data.attributes.name')) {
            return 'Attribute \'name\' is required.';
        }
    }
}

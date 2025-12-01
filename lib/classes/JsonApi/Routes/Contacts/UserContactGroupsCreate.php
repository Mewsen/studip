<?php

namespace JsonApi\Routes\Contacts;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserContactGroupsCreate extends JsonApiController
{
    use ValidationTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!Authority::canCreateGroups($user)) {
            throw new AuthorizationFailedException();
        }

        $name = self::arrayGet($json, 'data.attributes.name', '');
        $contactGroup = \ContactGroup::create(
            [
                'owner_id' => $user->id,
                'name' => $name,
            ]
        );

        return $this->getCreatedResponse($contactGroup);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data.attributes.name')) {
            return 'Attribute \'name\' is required.';
        }
    }
}
